<?php

namespace App\Services;

use App\Models\Tabungan;
use App\Models\TabunganTransaksi;
use App\Models\SumberTransaksi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TabunganService
{
    public function create(array $data): Tabungan
    {
        return Tabungan::create([
            'household_id'  => auth()->user()->household_id,
            'nama'          => $data['nama'],
            'target_jumlah' => $data['target'],
            'terkumpul'     => 0,
            'target_tanggal'=> $data['tanggal_target'] ?? null,
            'deskripsi'     => $data['keterangan'] ?? null,
            'status'        => 'aktif',
        ]);
    }

    public function update(Tabungan $tabungan, array $data): Tabungan
    {
        $tabungan->update([
            'nama'           => $data['nama'] ?? $tabungan->nama,
            'target_jumlah'  => $data['target'] ?? $tabungan->target_jumlah,
            'target_tanggal' => $data['tanggal_target'] ?? $tabungan->target_tanggal,
            'deskripsi'      => $data['keterangan'] ?? $tabungan->deskripsi,
        ]);

        return $tabungan->fresh();
    }

    public function delete(Tabungan $tabungan): bool
    {
        if ($tabungan->terkumpul > 0) {
            throw new \Exception('Tidak dapat menghapus tabungan yang masih memiliki saldo');
        }

        return $tabungan->delete();
    }

    public function setor(Tabungan $tabungan, array $data): TabunganTransaksi
    {
        return DB::transaction(function () use ($tabungan, $data) {
            $sumber = SumberTransaksi::findOrFail($data['sumber_transaksi_id']);

            if ($sumber->saldo_saat_ini < $data['jumlah']) {
                throw new \Exception('Saldo sumber transaksi tidak mencukupi');
            }

            $sumber->decrement('saldo_saat_ini', $data['jumlah']);
            $tabungan->increment('terkumpul', $data['jumlah']);
            $tabungan->refresh();

            $transaksi = TabunganTransaksi::create([
                'tabungan_id'         => $tabungan->id,
                'sumber_transaksi_id' => $data['sumber_transaksi_id'],
                'jenis'               => 'setor',
                'jumlah'              => $data['jumlah'],
                'tanggal'             => $data['tanggal'] ?? Carbon::now(),
                'keterangan'          => $data['keterangan'] ?? null,
            ]);

            if ($tabungan->terkumpul >= $tabungan->target_jumlah && $tabungan->status !== 'tercapai') {
                $tabungan->update(['status' => 'tercapai']);

                $this->sendNotification(
                    'Target Tabungan Tercapai!',
                    "Selamat! Target tabungan '{$tabungan->nama}' sebesar Rp " .
                        number_format($tabungan->target_jumlah, 0, ',', '.') . " telah tercapai!",
                    'tabungan'
                );
            }

            return $transaksi;
        });
    }

    public function tarik(Tabungan $tabungan, array $data): TabunganTransaksi
    {
        return DB::transaction(function () use ($tabungan, $data) {
            if ($tabungan->terkumpul < $data['jumlah']) {
                throw new \Exception('Saldo tabungan tidak mencukupi');
            }

            $tabungan->decrement('terkumpul', $data['jumlah']);
            $tabungan->refresh();

            $sumber = SumberTransaksi::findOrFail($data['sumber_transaksi_id']);
            $sumber->increment('saldo_saat_ini', $data['jumlah']);

            $transaksi = TabunganTransaksi::create([
                'tabungan_id'         => $tabungan->id,
                'sumber_transaksi_id' => $data['sumber_transaksi_id'],
                'jenis'               => 'tarik',
                'jumlah'              => $data['jumlah'],
                'tanggal'             => $data['tanggal'] ?? Carbon::now(),
                'keterangan'          => $data['keterangan'] ?? null,
            ]);

            if ($tabungan->terkumpul < $tabungan->target_jumlah && $tabungan->status === 'tercapai') {
                $tabungan->update(['status' => 'aktif']);
            }

            return $transaksi;
        });
    }

    public function getProgress(Tabungan $tabungan): array
    {
        $persentase = $tabungan->target_jumlah > 0
            ? ($tabungan->terkumpul / $tabungan->target_jumlah) * 100
            : 0;

        $sisa = max(0, $tabungan->target_jumlah - $tabungan->terkumpul);

        $estimasi = null;
        if ($tabungan->status === 'aktif' && $sisa > 0) {
            $rataRata = $this->getRataRataSetorPerBulan($tabungan);
            if ($rataRata > 0) {
                $bulanDibutuhkan = ceil($sisa / $rataRata);
                $estimasi = Carbon::now()->addMonths($bulanDibutuhkan)->translatedFormat('F Y');
            }
        }

        return [
            'target_jumlah'  => $tabungan->target_jumlah,
            'terkumpul'      => $tabungan->terkumpul,
            'sisa'           => $sisa,
            'persentase'     => round(min(100, $persentase), 2),
            'status'         => $tabungan->status,
            'tercapai'       => $tabungan->status === 'tercapai',
            'target_tanggal' => $tabungan->target_tanggal,
            'estimasi_tercapai' => $estimasi,
        ];
    }

    protected function getRataRataSetorPerBulan(Tabungan $tabungan): float
    {
        $totalSetor = TabunganTransaksi::where('tabungan_id', $tabungan->id)
            ->where('jenis', 'setor')
            ->where('tanggal', '>=', Carbon::now()->subMonths(3))
            ->sum('jumlah');

        return $totalSetor / 3;
    }

    public function getRiwayat(Tabungan $tabungan, array $filters = [])
    {
        $query = TabunganTransaksi::with('sumberTransaksi')
            ->where('tabungan_id', $tabungan->id)
            ->orderBy('tanggal', 'desc');

        if (!empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        if (!empty($filters['tanggal_mulai'])) {
            $query->where('tanggal', '>=', $filters['tanggal_mulai']);
        }

        if (!empty($filters['tanggal_akhir'])) {
            $query->where('tanggal', '<=', $filters['tanggal_akhir']);
        }

        return $query->paginate(20);
    }

    protected function sendNotification(string $judul, string $pesan, string $jenis): void
    {
        \App\Models\Notifikasi::create([
            'household_id' => auth()->user()->household_id,
            'user_id'      => auth()->id(),
            'judul'        => $judul,
            'pesan'        => $pesan,
            'jenis'        => $jenis,
            'is_read'      => false,
        ]);
    }
}
