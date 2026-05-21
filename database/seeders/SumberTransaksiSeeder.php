<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SumberTransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Note: Seeder ini akan dijalankan setelah user membuat household
     * Data default sumber transaksi akan dibuat otomatis via observer/service
     */
    public function run(): void
    {
        // Seeder ini kosong karena sumber transaksi dibuat per-household
        // Akan dibuat otomatis saat household pertama kali dibuat
        
        // Default sumber yang akan dibuat:
        // 1. Kas/Tunai
        // 2. Bank (template)
        // 3. E-Wallet (template)
    }
}
