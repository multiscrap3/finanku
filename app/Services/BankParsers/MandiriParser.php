<?php

namespace App\Services\BankParsers;

class MandiriParser extends AbstractCsvBankParser
{
    protected array $dateHeaders = ['tanggal', 'transaction date', 'posting date', 'date'];
    protected array $descriptionHeaders = ['keterangan', 'description', 'remark', 'remarks', 'uraian'];
    protected array $debitHeaders = ['debit', 'withdrawal', 'keluar'];
    protected array $creditHeaders = ['credit', 'kredit', 'deposit', 'masuk'];
    protected array $amountHeaders = ['amount', 'nominal', 'jumlah'];
    protected array $balanceHeaders = ['balance', 'saldo', 'running balance'];

    public function supports(string $bankCode): bool
    {
        return strtolower($bankCode) === 'mandiri';
    }
}