<?php

namespace App\Services\BankParsers;

use Carbon\Carbon;
use InvalidArgumentException;

abstract class AbstractCsvBankParser implements BankParserInterface
{
    protected array $dateHeaders = ['tanggal', 'date', 'tgl', 'transaction date', 'posting date'];
    protected array $descriptionHeaders = ['keterangan', 'description', 'deskripsi', 'uraian', 'remark', 'remarks', 'mutasi'];
    protected array $debitHeaders = ['debit', 'db', 'keluar', 'withdrawal', 'mutasi debit'];
    protected array $creditHeaders = ['kredit', 'credit', 'cr', 'masuk', 'deposit', 'mutasi kredit'];
    protected array $amountHeaders = ['amount', 'nominal', 'jumlah', 'mutation', 'mutasi'];
    protected array $balanceHeaders = ['saldo', 'balance', 'running balance'];

    public function parse(string $filePath, array $options = []): array
    {
        if (! is_readable($filePath)) {
            throw new InvalidArgumentException('File mutasi bank tidak dapat dibaca.');
        }

        $rows = $this->readRows($filePath);

        if ($rows === []) {
            return [];
        }

        $headers = $this->normalizeHeaders(array_shift($rows));
        $parsed = [];

        foreach ($rows as $index => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $assoc = $this->combineRow($headers, $row);
            $parsed[] = $this->normalizeRow($assoc, $index + 2);
        }

        return $parsed;
    }

    protected function readRows(string $filePath): array
    {
        $delimiter = $this->detectDelimiter($filePath);
        $rows = [];

        $handle = fopen($filePath, 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException('Gagal membuka file mutasi bank.');
        }

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rows[] = array_map(fn ($value) => trim((string) $value), $row);
        }

        fclose($handle);

        return $rows;
    }

    protected function detectDelimiter(string $filePath): string
    {
        $sample = (string) file_get_contents($filePath, false, null, 0, 2048);
        $delimiters = [',', ';', "\t", '|'];
        $scores = [];

        foreach ($delimiters as $delimiter) {
            $scores[$delimiter] = substr_count($sample, $delimiter);
        }

        arsort($scores);

        return (string) array_key_first($scores);
    }

    protected function normalizeHeaders(array $headers): array
    {
        return array_map(fn ($header) => strtolower(trim((string) $header)), $headers);
    }

    protected function combineRow(array $headers, array $row): array
    {
        $assoc = [];

        foreach ($headers as $index => $header) {
            $assoc[$header] = $row[$index] ?? null;
        }

        return $assoc;
    }

    protected function normalizeRow(array $row, int $lineNumber): array
    {
        $date = $this->getFirstValue($row, $this->dateHeaders);
        $description = $this->getFirstValue($row, $this->descriptionHeaders) ?: 'Import mutasi bank';
        $debit = $this->parseMoney($this->getFirstValue($row, $this->debitHeaders));
        $credit = $this->parseMoney($this->getFirstValue($row, $this->creditHeaders));
        $amount = $this->parseMoney($this->getFirstValue($row, $this->amountHeaders));
        $balance = $this->parseMoney($this->getFirstValue($row, $this->balanceHeaders));

        if ($credit <= 0 && $debit <= 0 && $amount !== 0.0) {
            if ($amount > 0) {
                $credit = $amount;
            } else {
                $debit = abs($amount);
            }
        }

        $jenis = $credit >= $debit ? 'pemasukan' : 'pengeluaran';
        $jumlah = max($credit, $debit);

        if ($jumlah <= 0) {
            throw new InvalidArgumentException("Nominal pada baris {$lineNumber} tidak valid.");
        }

        return [
            'tanggal' => $this->parseDate($date, $lineNumber),
            'keterangan' => $description,
            'debit' => $debit,
            'kredit' => $credit,
            'jumlah' => $jumlah,
            'jenis' => $jenis,
            'saldo' => $balance,
            'raw' => $row,
        ];
    }

    protected function getFirstValue(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return (string) $row[$key];
            }
        }

        return null;
    }

    protected function parseMoney(?string $value): float
    {
        if ($value === null || trim($value) === '') {
            return 0.0;
        }

        $normalized = trim($value);
        $normalized = str_replace(['Rp', 'IDR', 'idr', ' '], '', $normalized);
        $isNegative = str_contains($normalized, '-') || str_contains($normalized, 'D');

        $normalized = preg_replace('/[^0-9,.-]/', '', $normalized) ?? '0';

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            $lastComma = strrpos($normalized, ',');
            $lastDot = strrpos($normalized, '.');

            if ($lastComma > $lastDot) {
                $normalized = str_replace('.', '', $normalized);
                $normalized = str_replace(',', '.', $normalized);
            } else {
                $normalized = str_replace(',', '', $normalized);
            }
        } else {
            $normalized = str_replace(',', '.', $normalized);
        }

        $amount = abs((float) $normalized);

        return $isNegative ? -$amount : $amount;
    }

    protected function parseDate(?string $value, int $lineNumber): string
    {
        if ($value === null || trim($value) === '') {
            throw new InvalidArgumentException("Tanggal pada baris {$lineNumber} wajib diisi.");
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'm/d/Y', 'Y/m/d', 'd M Y', 'd F Y'];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value))->format('Y-m-d');
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            throw new InvalidArgumentException("Format tanggal pada baris {$lineNumber} tidak valid.");
        }
    }

    protected function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isEmpty();
    }
}