<?php

namespace App\Services;

use App\Models\RecurringTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecurringService
{
    /**
     * Process all active recurring transactions
     */
    public function processAll(): array
    {
        $today = Carbon::today();
        $processed = [];
        $failed = [];

        $recurringTransaksi = RecurringTransaksi::where('status', 'aktif')
            ->where('tanggal_mulai', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', $today);
            })
            ->get();

        foreach ($recurringTransaksi as $recurring) {
            try {
                if ($this->shouldExecute($recurring, $today)) {
                    $transaksi = $this->execute($recurring, $today);
                    $processed[] = [
                        'recurring_id' => $recurring->id,
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $transaksi->jumlah,
                    ];
                }
            } catch (\Exception $e) {
                $failed[] = [
                    'recurring_id' => $recurring->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'processed' => $processed,
            'failed' => $failed,
            'total_processed' => count($processed),
            'total_failed' => count($failed),
        ];
    }

    /**
     * Check if recurring transaction should be executed today
     */
    protected function shouldExecute(RecurringTransaksi $recurring, Carbon $today): bool
    {
        // Belum pernah dieksekusi
        if (!$recurring->tanggal_eksekusi_terakhir) {
            return true;
        }

        $lastExecution = Carbon::parse($recurring->tanggal_eksekusi_terakhir);

        switch ($recurring->frekuensi) {
            case 'harian':
                return $lastExecution->diffInDays($today) >= 1;

            case 'mingguan':
                return $lastExecution->diffInWeeks($today) >= 1;

            case 'bulanan':
                return $lastExecution->diffInMonths($today) >= 1;

            case 'tahunan':
                return $lastExecution->diffInYears($today) >= 1;

            default:
                return false;
        }
    }

    /**
     * Execute recurring transaction
     */
    public function execute(RecurringTransaksi $recurring, Carbon $date = null): Transaksi
    {
        $date = $date ?? Carbon::today();

        return DB::transaction(function () use ($recurring, $date) {
            // Create transaksi
            $transaksi = Transaksi::create([
                'household_id' => $recurring->household_id,
                'kategori_id' => $recurring->kategori_id,
                'sumber_transaksi_id' => $recurring->sumber_transaksi_id,
                'recurring_transaksi_id' => $recurring->id,
                'jenis' => $recurring->jenis,
                'jumlah' => $recurring->jumlah,
                'tanggal' => $date,
                'keterangan' => $recurring->keterangan . ' (Auto-generated)',
            ]);

            // Update saldo sumber transaksi
            $sumber = $recurring->sumberTransaksi;
            if ($recurring->jenis === 'pemasukan') {
                $sumber->increment('saldo', $recurring->jumlah);
            } else {
                $sumber->decrement('saldo', $recurring->jumlah);
            }

            // Update anggaran jika pengeluaran
            if ($recurring->jenis === 'pengeluaran') {
                $anggaran = \App\Models\Anggaran::where('household_id', $recurring->household_id)
                    ->where('kategori_id', $recurring->kategori_id)
                    ->where('bulan', $date->format('Y-m'))
                    ->first();

                if ($anggaran) {
                    $anggaran->increment('terpakai', $recurring->jumlah);
                }
            }

            // Update tanggal eksekusi terakhir
            $recurring->update(['tanggal_eksekusi_terakhir' => $date]);

            // Check if should be completed
            if ($recurring->tanggal_selesai && Carbon::parse($recurring->tanggal_selesai)->lte($date)) {
                $recurring->update(['status' => 'selesai']);
            }

            return $transaksi;
        });
    }

    /**
     * Get next execution date
     */
    public function getNextExecutionDate(RecurringTransaksi $recurring): ?Carbon
    {
        if ($recurring->status !== 'aktif') {
            return null;
        }

        $baseDate = $recurring->tanggal_eksekusi_terakhir
            ? Carbon::parse($recurring->tanggal_eksekusi_terakhir)
            : Carbon::parse($recurring->tanggal_mulai);

        switch ($recurring->frekuensi) {
            case 'harian':
                $nextDate = $baseDate->copy()->addDay();
                break;

            case 'mingguan':
                $nextDate = $baseDate->copy()->addWeek();
                break;

            case 'bulanan':
                $nextDate = $baseDate->copy()->addMonth();
                break;

            case 'tahunan':
                $nextDate = $baseDate->copy()->addYear();
                break;

            default:
                return null;
        }

        // Check if next date exceeds end date
        if ($recurring->tanggal_selesai && $nextDate->gt(Carbon::parse($recurring->tanggal_selesai))) {
            return null;
        }

        return $nextDate;
    }

    /**
     * Get upcoming recurring transactions (next 30 days)
     */
    public function getUpcoming(int $days = 30): array
    {
        $today = Carbon::today();
        $endDate = $today->copy()->addDays($days);
        $upcoming = [];

        $recurringTransaksi = RecurringTransaksi::where('status', 'aktif')
            ->where('tanggal_mulai', '<=', $endDate)
            ->where(function ($query) use ($today) {
                $query->whereNull('tanggal_selesai')
                    ->orWhere('tanggal_selesai', '>=', $today);
            })
            ->get();

        foreach ($recurringTransaksi as $recurring) {
            $nextDate = $this->getNextExecutionDate($recurring);

            if ($nextDate && $nextDate->lte($endDate)) {
                $upcoming[] = [
                    'recurring' => $recurring,
                    'next_execution' => $nextDate,
                    'days_until' => $today->diffInDays($nextDate),
                ];
            }
        }

        // Sort by next execution date
        usort($upcoming, function ($a, $b) {
            return $a['next_execution']->timestamp - $b['next_execution']->timestamp;
        });

        return $upcoming;
    }

    /**
     * Manually execute recurring transaction
     */
    public function manualExecute(RecurringTransaksi $recurring): Transaksi
    {
        if ($recurring->status !== 'aktif') {
            throw new \Exception('Recurring transaction is not active');
        }

        return $this->execute($recurring);
    }

    /**
     * Skip next execution
     */
    public function skipNext(RecurringTransaksi $recurring): bool
    {
        $nextDate = $this->getNextExecutionDate($recurring);

        if (!$nextDate) {
            throw new \Exception('No next execution date available');
        }

        $recurring->update(['tanggal_eksekusi_terakhir' => $nextDate]);

        return true;
    }
}
