<?php

namespace App\Services\BankParsers;

class GenericParser extends AbstractCsvBankParser
{
    public function supports(string $bankCode): bool
    {
        return strtolower($bankCode) === 'generic';
    }
}