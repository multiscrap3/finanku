<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class ImportBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required', 'file', 'max:10240',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! $value instanceof UploadedFile) {
                        return;
                    }
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (! in_array($ext, ['csv', 'txt', 'xlsx'], true)) {
                        $fail('File harus berformat CSV, TXT, atau XLSX.');
                    }
                },
            ],
            'bank_code' => ['nullable', 'string', 'in:generic,bca,mandiri,bni,bsi'],
            'sumber_transaksi_id' => ['required', 'integer', 'exists:sumber_transaksi,id'],
            'kategori_id' => ['nullable', 'integer', 'exists:kategori,id'],
            'password' => ['nullable', 'string', 'max:255'],
        ];
    }
}