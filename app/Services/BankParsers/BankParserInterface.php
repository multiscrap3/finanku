<?php

namespace App\Services\BankParsers;

interface BankParserInterface
{
    /**
     * Parse bank mutation file into normalized transaction rows.
     *
     * Expected output per row:
     * [
     *     'tanggal' => 'Y-m-d',
     *     'keterangan' => string,
     *     'debit' => float,
     *     'kredit' => float,
     *     'jumlah' => float,
     *     'jenis' => 'pemasukan'|'pengeluaran',
     *     'saldo' => float|null,
     *     'raw' => array,
     * ]
     */
    public function parse(string $filePath, array $options = []): array;

    public function supports(string $bankCode): bool;
}