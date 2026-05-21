<?php

namespace App\Services\BankParsers;

class BCAParser extends AbstractCsvBankParser
{
    protected array $dateHeaders = ['tanggal', 'date', 'tgl', 'transaction date'];
    protected array $descriptionHeaders = ['keterangan', 'description', 'deskripsi', 'uraian'];
    protected array $debitHeaders = ['debit', 'db', 'keluar'];
    protected array $creditHeaders = ['kredit', 'credit', 'cr', 'masuk'];
    protected array $amountHeaders = ['mutasi', 'amount', 'nominal', 'jumlah'];
    protected array $balanceHeaders = ['saldo', 'balance'];

    public function supports(string $bankCode): bool
    {
        return strtolower($bankCode) === 'bca';
    }
}