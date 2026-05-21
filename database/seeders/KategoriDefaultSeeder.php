<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriDefaultSeeder extends Seeder
{
    public function run(): void
    {
        $household = Household::where('slug', 'rumah-admin')->first();

        if (! $household) {
            $this->command->warn('Admin household not found. Run AdminUserSeeder first.');
            return;
        }

        $existing = Kategori::withoutGlobalScope('household')
            ->where('household_id', $household->id)
            ->count();

        if ($existing > 0) {
            $this->command->info('Default categories already exist for admin household.');
            return;
        }

        $this->seedForHousehold($household->id);

        $this->command->info("Seeded default categories for household: {$household->nama}");
    }

    public static function seedForHousehold(int $householdId): void
    {
        $now = now()->toDateTimeString();

        $pengeluaran = [
            ['nama' => 'Makanan & Minuman', 'icon' => '🍽️', 'warna' => '#F59E0B'],
            ['nama' => 'Transportasi',       'icon' => '🚗', 'warna' => '#3B82F6'],
            ['nama' => 'Tagihan & Utilitas', 'icon' => '💡', 'warna' => '#8B5CF6'],
            ['nama' => 'Belanja',            'icon' => '🛍️', 'warna' => '#EC4899'],
            ['nama' => 'Kesehatan',          'icon' => '🏥', 'warna' => '#EF4444'],
            ['nama' => 'Pendidikan',         'icon' => '📚', 'warna' => '#06B6D4'],
            ['nama' => 'Hiburan',            'icon' => '🎬', 'warna' => '#F97316'],
            ['nama' => 'Lainnya',            'icon' => '📦', 'warna' => '#6B7280'],
        ];

        $pemasukan = [
            ['nama' => 'Gaji',      'icon' => '💼', 'warna' => '#10B981'],
            ['nama' => 'Bonus',     'icon' => '🎁', 'warna' => '#34D399'],
            ['nama' => 'Investasi', 'icon' => '📈', 'warna' => '#059669'],
            ['nama' => 'Bisnis',    'icon' => '🏢', 'warna' => '#047857'],
            ['nama' => 'Tabungan',  'icon' => '🐷', 'warna' => '#F59E0B'],
            ['nama' => 'Lainnya',   'icon' => '💰', 'warna' => '#6B7280'],
        ];

        foreach ($pengeluaran as $i => $k) {
            Kategori::withoutGlobalScope('household')->insert([
                'household_id' => $householdId,
                'nama'         => $k['nama'],
                'jenis'        => 'pengeluaran',
                'icon'         => $k['icon'],
                'warna'        => $k['warna'],
                'urutan'       => $i + 1,
                'is_active'    => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        foreach ($pemasukan as $i => $k) {
            Kategori::withoutGlobalScope('household')->insert([
                'household_id' => $householdId,
                'nama'         => $k['nama'],
                'jenis'        => 'pemasukan',
                'icon'         => $k['icon'],
                'warna'        => $k['warna'],
                'urutan'       => $i + 1,
                'is_active'    => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}
