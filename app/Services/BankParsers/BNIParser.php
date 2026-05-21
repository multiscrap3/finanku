<?php

namespace App\Services\BankParsers;

class BNIParser extends AbstractCsvBankParser
{
    protected array $dateHeaders = ['tanggal', 'trx date', 'transaction date', 'date'];
    protected array $descriptionHeaders = ['keterangan', 'description', 'deskripsi', 'remark'];
    protected array $debitHeaders = ['debit', 'db', 'withdrawal'];
    protected array $creditHeaders = ['kredit', 'credit', 'cr', 'deposit'];
    protected array $amountHeaders = ['amount', 'nominal', 'jumlah'];
    protected array $balanceHeaders = ['saldo', 'balance'];

    public function supports(string $bankCode): bool
    {
        return strtolower($bankCode) === 'bni';
    }
}