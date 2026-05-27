<?php

namespace App\Services;

use App\Models\HutangPiutang;
use App\Models\HutangPiutangPembayaran;
use App\Models\SumberTransaksi;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HutangPiutangService
{
    /**
     * Create hutang/piutang baru
     */
    public function create(array $data): HutangPiutang
    {
        return HutangPiutang::create([
            'household_id'        => auth()->user()->household_id,
            'jenis'               => $data['jenis'],
            'nama_pihak'          => $data['nama_pihak'],
            'jumlah_total'        => $data['jumlah'],
            'jumlah_terbayar'     => 0,
            'tanggal_mulai'       => $data['tanggal'] ?? Carbon::now(),
            'tanggal_jatuh_tempo' => $data['jatuh_tempo'] ?? null,
            'keterangan'          => $data['keterangan'] ?? null,
            'status'              => 'aktif',
            'tipe_pembayaran'     => $data['tipe_pembayaran'] ?? 'sekali',
            'jumlah_cicilan'      => ($data['tipe_pembayaran'] ?? 'sekali') === 'cicilan' ? $data['jumlah_cicilan'] : null,
            'frekuensi_cicilan'   => ($data['tipe_pembayaran'] ?? 'sekali') === 'cicilan' ? $data['frekuensi_cicilan'] : null,
        ]);
    }

    /**
     * Update hutang/piutang
     */
    public function update(HutangPiutang $hutangPiutang, array $data): HutangPiutang
    {
        $hutangPiutang->update([
            'nama_pihak'          => $data['nama_pihak'] ?? $hutangPiutang->nama_pihak,
            'tanggal_jatuh_tempo' => $data['jatuh_tempo'] ?? $hutangPiutang->tanggal_jatuh_tempo,
            'keterangan'          => $data['keterangan'] ?? $hutangPiutang->keterangan,
        ]);

        return $hutangPiutang->fresh();
    }

    /**
     * Delete hutang/piutang
     */
    public function delete(HutangPiutang $hutangPiutang): bool
    {
        // Cek apakah sudah ada pembayaran
        if ($hutangPiutang->pembayaran()->exists()) {
            throw new \Exception('Tidak dapat menghapus hutang/piutang yang sudah memiliki riwayat pembayaran');
        }

        return $hutangPiutang->delete();
    }

    /**
     * Bayar hutang/piutang
     */
    public function bayar(HutangPiutang $hutangPiutang, array $data): HutangPiutangPembayaran
    {
        return DB::transaction(function () use ($hutangPiutang, $data) {
            $jumlahBayar = $data['jumlah'];

            // Validasi jumlah
            if ($jumlahBayar > $hutangPiutang->sisa) {
                throw new \Exception('Jumlah pembayaran melebihi sisa hutang/piutang');
            }

            $sumber = SumberTransaksi::findOrFail($data['sumber_transaksi_id']);

            // Update saldo sumber transaksi
            if ($hutangPiutang->jenis === 'hutang') {
                // Bayar hutang = kurangi saldo
                if ($sumber->saldo_saat_ini < $jumlahBayar) {
                    throw new \Exception('Saldo sumber transaksi tidak mencukupi');
                }
                $sumber->decrement('saldo_saat_ini', $jumlahBayar);
            } else {
                // Terima piutang = tambah saldo
                $sumber->increment('saldo_saat_ini', $jumlahBayar);
            }

            // Kurangi sisa hutang/piutang
            $hutangPiutang->increment('jumlah_terbayar', $jumlahBayar);

            // Update status jika lunas
            if ($hutangPiutang->fresh()->sisa == 0) {
                $hutangPiutang->update(['status' => 'lunas']);

                // Send notification
                $this->sendNotification(
                    $hutangPiutang->jenis === 'hutang' ? 'Hutang Lunas! 🎉' : 'Piutang Lunas! 💰',
                    "{$hutangPiutang->jenis} kepada {$hutangPiutang->nama_pihak} sebesar Rp " . number_format($hutangPiutang->jumlah_total, 0, ',', '.') . " telah lunas!",
                    'success'
                );
            }

            // Catat pembayaran
            $pembayaran = HutangPiutangPembayaran::create([
                'hutang_piutang_id'   => $hutangPiutang->id,
                'user_id'             => auth()->id(),
                'sumber_transaksi_id' => $data['sumber_transaksi_id'],
                'jumlah'              => $jumlahBayar,
                'tanggal'             => $data['tanggal'] ?? Carbon::now(),
                'keterangan'          => $data['keterangan'] ?? null,
            ]);

            return $pembayaran;
        });
    }

    /**
     * Edit record pembayaran yang sudah ada.
     * Reversal saldo lama, terapkan saldo baru.
     */
    public function editPembayaran(HutangPiutangPembayaran $pembayaran, array $data): HutangPiutangPembayaran
    {
        return DB::transaction(function () use ($pembayaran, $data) {
            $hp      = $pembayaran->hutangPiutang;
            $selisih = $data['jumlah'] - $pembayaran->jumlah;

            if ($selisih > $hp->sisa) {
                throw new \Exception('Jumlah pembayaran melebihi sisa hutang/piutang');
            }

            // Reversal saldo sumber lama
            if ($pembayaran->sumber_transaksi_id) {
                $sumberLama = SumberTransaksi::findOrFail($pembayaran->sumber_transaksi_id);
                if ($hp->jenis === 'hutang') {
                    $sumberLama->increment('saldo_saat_ini', $pembayaran->jumlah);
                } else {
                    $sumberLama->decrement('saldo_saat_ini', $pembayaran->jumlah);
                }
            }

            // Terapkan sumber & jumlah baru
            $sumberBaru = SumberTransaksi::findOrFail($data['sumber_transaksi_id']);
            if ($hp->jenis === 'hutang') {
                if ($sumberBaru->saldo_saat_ini < $data['jumlah']) {
                    throw new \Exception('Saldo sumber transaksi tidak mencukupi');
                }
                $sumberBaru->decrement('saldo_saat_ini', $data['jumlah']);
            } else {
                $sumberBaru->increment('saldo_saat_ini', $data['jumlah']);
            }

            // Update jumlah_terbayar di hutang_piutang
            $hp->decrement('jumlah_terbayar', $pembayaran->jumlah);
            $hp->increment('jumlah_terbayar', $data['jumlah']);

            // Sync status lunas
            $hp->refresh();
            if ($hp->sisa <= 0) {
                $hp->update(['status' => 'lunas']);
            } elseif ($hp->status === 'lunas') {
                $hp->update(['status' => 'aktif']);
            }

            $pembayaran->update([
                'sumber_transaksi_id' => $data['sumber_transaksi_id'],
                'jumlah'              => $data['jumlah'],
                'tanggal'             => $data['tanggal'],
                'keterangan'          => $data['keterangan'] ?? null,
            ]);

            return $pembayaran->fresh();
        });
    }

    /**
     * Hapus record pembayaran dan reversal saldo.
     */
    public function hapusPembayaran(HutangPiutangPembayaran $pembayaran): bool
    {
        return DB::transaction(function () use ($pembayaran) {
            $hp = $pembayaran->hutangPiutang;

            if ($pembayaran->sumber_transaksi_id) {
                $sumber = SumberTransaksi::findOrFail($pembayaran->sumber_transaksi_id);
                if ($hp->jenis === 'hutang') {
                    $sumber->increment('saldo_saat_ini', $pembayaran->jumlah);
                } else {
                    $sumber->decrement('saldo_saat_ini', $pembayaran->jumlah);
                }
            }

            $hp->decrement('jumlah_terbayar', $pembayaran->jumlah);

            if ($hp->status === 'lunas') {
                $hp->update(['status' => 'aktif']);
            }

            return $pembayaran->delete();
        });
    }

    /**
     * Get summary hutang/piutang
     */
    public function getSummary(): array
    {
        $hutang = HutangPiutang::where('jenis', 'hutang')
            ->where('status', 'aktif')
            ->get();

        $piutang = HutangPiutang::where('jenis', 'piutang')
            ->where('status', 'aktif')
            ->get();

        // Hutang jatuh tempo
        $hutangJatuhTempo = $hutang->filter(function ($item) {
            return $item->tanggal_jatuh_tempo && Carbon::parse($item->tanggal_jatuh_tempo)->isPast();
        });

        // Piutang jatuh tempo
        $piutangJatuhTempo = $piutang->filter(function ($item) {
            return $item->tanggal_jatuh_tempo && Carbon::parse($item->tanggal_jatuh_tempo)->isPast();
        });

        return [
            'total_hutang' => $hutang->sum('sisa'),
            'total_piutang' => $piutang->sum('sisa'),
            'jumlah_hutang' => $hutang->count(),
            'jumlah_piutang' => $piutang->count(),
            'hutang_jatuh_tempo' => $hutangJatuhTempo->count(),
            'piutang_jatuh_tempo' => $piutangJatuhTempo->count(),
            'hutang_list' => $hutang,
            'piutang_list' => $piutang,
        ];
    }

    /**
     * Get riwayat pembayaran
     */
    public function getRiwayat(HutangPiutang $hutangPiutang)
    {
        return HutangPiutangPembayaran::with('sumberTransaksi')
            ->where('hutang_piutang_id', $hutangPiutang->id)
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Check jatuh tempo dan kirim reminder
     */
    public function checkJatuhTempo(): int
    {
        $today = Carbon::today();
        $threeDaysLater = Carbon::today()->addDays(3);

        // Cek hutang/piutang yang akan jatuh tempo dalam 3 hari
        $items = HutangPiutang::where('status', 'aktif')
            ->whereNotNull('tanggal_jatuh_tempo')
            ->whereBetween('tanggal_jatuh_tempo', [$today, $threeDaysLater])
            ->get();

        $count = 0;
        foreach ($items as $item) {
            $daysLeft = Carbon::today()->diffInDays(Carbon::parse($item->tanggal_jatuh_tempo));
            
            $this->sendNotification(
                'Reminder Jatuh Tempo',
                "{$item->jenis} kepada {$item->nama_pihak} sebesar Rp " . number_format($item->sisa, 0, ',', '.') . " akan jatuh tempo dalam {$daysLeft} hari",
                'warning'
            );
            
            $count++;
        }

        return $count;
    }

    /**
     * Send notification
     */
    protected function sendNotification(string $judul, string $pesan, string $tipe): void
    {
        Notifikasi::create([
            'household_id' => auth()->user()->household_id,
            'user_id'      => auth()->id(),
            'judul'        => $judul,
            'pesan'        => $pesan,
            'jenis'        => 'hutang_piutang',
            'is_read'      => false,
        ]);
    }

    /**
     * Get hutang/piutang dengan filter
     */
    public function getHutangPiutang(array $filters = [])
    {
        $query = HutangPiutang::query();

        if (!empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where('nama_pihak', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('tanggal_mulai', 'desc')->get();
    }
}
