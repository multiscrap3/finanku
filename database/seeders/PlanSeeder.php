<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'nama' => 'Free',
                'slug' => 'free',
                'harga' => 0,
                'max_anggota' => 3,
                'max_transaksi' => 100,
                'max_ocr' => 10,
                'fitur' => json_encode([
                    'transaksi_dasar' => true,
                    'kategori' => true,
                    'anggaran' => true,
                    'laporan_dasar' => true,
                    'multi_sumber' => true,
                    'recurring' => false,
                    'tabungan' => false,
                    'hutang_piutang' => false,
                    'import_bank' => false,
                    'ocr' => false,
                    'ai_insights' => false,
                    'export_excel' => false,
                    'export_pdf' => true,
                    'backup' => false,
                ]),
                'is_active' => true,
            ],
            [
                'nama' => 'Basic',
                'slug' => 'basic',
                'harga' => 29000,
                'max_anggota' => 5,
                'max_transaksi' => 500,
                'max_ocr' => 50,
                'fitur' => json_encode([
                    'transaksi_dasar' => true,
                    'kategori' => true,
                    'anggaran' => true,
                    'laporan_dasar' => true,
                    'multi_sumber' => true,
                    'recurring' => true,
                    'tabungan' => true,
                    'hutang_piutang' => true,
                    'import_bank' => false,
                    'ocr' => true,
                    'ai_insights' => false,
                    'export_excel' => true,
                    'export_pdf' => true,
                    'backup' => true,
                ]),
                'is_active' => true,
            ],
            [
                'nama' => 'Premium',
                'slug' => 'premium',
                'harga' => 79000,
                'max_anggota' => -1, // unlimited
                'max_transaksi' => -1, // unlimited
                'max_ocr' => -1, // unlimited
                'fitur' => json_encode([
                    'transaksi_dasar' => true,
                    'kategori' => true,
                    'anggaran' => true,
                    'laporan_dasar' => true,
                    'laporan_advanced' => true,
                    'multi_sumber' => true,
                    'recurring' => true,
                    'tabungan' => true,
                    'hutang_piutang' => true,
                    'import_bank' => true,
                    'ocr' => true,
                    'ai_insights' => true,
                    'export_excel' => true,
                    'export_pdf' => true,
                    'backup' => true,
                    'priority_support' => true,
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
