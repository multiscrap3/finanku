<?php

namespace App\Services\BankParsers;

class BSIParser extends AbstractCsvBankParser
{
    protected array $dateHeaders = ['tanggal', 'date', 'transaction date', 'posting date'];
    protected array $descriptionHeaders = ['keterangan', 'description', 'deskripsi', 'uraian', 'remark'];
    protected array $debitHeaders = ['debit', 'db', 'keluar', 'withdrawal'];
    protected array $creditHeaders = ['kredit', 'credit', 'cr', 'masuk', 'deposit'];
    protected array $amountHeaders = ['amount', 'nominal', 'jumlah', 'mutasi'];
    protected array $balanceHeaders = ['saldo', 'balance'];

    public function supports(string $bankCode): bool
    {
        return strtolower($bankCode) === 'bsi';
    }
}