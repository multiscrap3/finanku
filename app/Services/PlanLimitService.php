<?php

namespace App\Services;

use App\Models\Household;
use App\Models\OcrHistory;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;

class PlanLimitService
{
    /**
     * FASE INTERNAL:
     * Semua pembatasan masih bypass agar internal testing tidak terhambat.
     * Method tetap disiapkan supaya SaaS limit bisa diaktifkan tanpa ubah kontrak service.
     */
    private bool $internalBypass = true;

    /**
     * Cek apakah household masih boleh menambah transaksi.
     */
    public function canAddTransaksi(int $householdId): bool
    {
        if ($this->internalBypass) {
            return true;
        }

        $household = $this->getHouseholdWithPlan($householdId);
        $limit = (int) ($household?->plan?->max_transaksi ?? -1);

        if ($this->isUnlimited($limit)) {
            return true;
        }

        return $this->getTransaksiBulanIni($householdId) < $limit;
    }

    /**
     * Cek apakah household masih boleh memakai OCR.
     */
    public function canUseOCR(int $householdId): bool
    {
        if ($this->internalBypass) {
            return true;
        }

        $household = $this->getHouseholdWithPlan($householdId);
        $limit = (int) ($household?->plan?->max_ocr ?? -1);

        if ($this->isUnlimited($limit)) {
            return true;
        }

        return $this->getOcrBulanIni($householdId) < $limit;
    }

    /**
     * Cek apakah household masih boleh menambah anggota.
     */
    public function canAddAnggota(int $householdId): bool
    {
        if ($this->internalBypass) {
            return true;
        }

        $household = $this->getHouseholdWithPlan($householdId);
        $limit = (int) ($household?->plan?->max_anggota ?? -1);

        if ($this->isUnlimited($limit)) {
            return true;
        }

        return $this->getJumlahAnggota($householdId) < $limit;
    }

    /**
     * Ambil statistik pemakaian plan untuk dashboard admin/superadmin.
     */
    public function getUsage(int $householdId): array
    {
        $household = $this->getHouseholdWithPlan($householdId);
        $plan = $household?->plan;

        $transaksiBulanIni = $this->getTransaksiBulanIni($householdId);
        $ocrBulanIni = $this->getOcrBulanIni($householdId);
        $jumlahAnggota = $this->getJumlahAnggota($householdId);

        return [
            'household_id' => $householdId,
            'plan_name' => $plan?->nama ?? 'Unknown',
            'plan_slug' => $plan?->slug,
            'internal_bypass' => $this->internalBypass,

            'transaksi_bulan_ini' => $transaksiBulanIni,
            'max_transaksi' => (int) ($plan?->max_transaksi ?? -1),
            'can_add_transaksi' => $this->canAddTransaksi($householdId),

            'ocr_bulan_ini' => $ocrBulanIni,
            'max_ocr' => (int) ($plan?->max_ocr ?? -1),
            'can_use_ocr' => $this->canUseOCR($householdId),

            'jumlah_anggota' => $jumlahAnggota,
            'max_anggota' => (int) ($plan?->max_anggota ?? -1),
            'can_add_anggota' => $this->canAddAnggota($householdId),
        ];
    }

    /**
     * Ambil detail limit plan dalam format ringkas untuk UI/API.
     */
    public function getLimits(int $householdId): array
    {
        $household = $this->getHouseholdWithPlan($householdId);
        $plan = $household?->plan;

        return [
            'plan_name' => $plan?->nama ?? 'Unknown',
            'features' => $plan?->fitur ?? [],
            'limits' => [
                'transaksi' => (int) ($plan?->max_transaksi ?? -1),
                'ocr' => (int) ($plan?->max_ocr ?? -1),
                'anggota' => (int) ($plan?->max_anggota ?? -1),
            ],
        ];
    }

    /**
     * Helper untuk mengecek fitur plan.
     * FASE INTERNAL tetap pass meskipun fitur belum aktif.
     */
    public function hasFeature(int $householdId, string $feature): bool
    {
        if ($this->internalBypass) {
            return true;
        }

        $household = $this->getHouseholdWithPlan($householdId);
        $features = $household?->plan?->fitur ?? [];

        if (in_array('all', $features, true)) {
            return true;
        }

        return (bool) ($features[$feature] ?? in_array($feature, $features, true));
    }

    private function getHouseholdWithPlan(int $householdId): ?Household
    {
        return Household::with('plan')->find($householdId);
    }

    private function getTransaksiBulanIni(int $householdId): int
    {
        return Transaksi::query()
            ->where('household_id', $householdId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->count();
    }

    private function getOcrBulanIni(int $householdId): int
    {
        return OcrHistory::query()
            ->where('household_id', $householdId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->count();
    }

    private function getJumlahAnggota(int $householdId): int
    {
        return User::query()
            ->where('household_id', $householdId)
            ->where('is_active', true)
            ->count();
    }

    private function isUnlimited(int $limit): bool
    {
        return $limit < 0;
    }
}