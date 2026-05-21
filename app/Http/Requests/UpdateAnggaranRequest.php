<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnggaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'kategori_id' => ['sometimes', 'exists:kategori,id'],
            'bulan' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'tahun' => ['sometimes', 'integer', 'min:2020', 'max:2100'],
            'jumlah' => ['sometimes', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.exists' => 'Kategori tidak ditemukan',
            'bulan.integer' => 'Bulan harus berupa angka',
            'bulan.min' => 'Bulan minimal 1',
            'bulan.max' => 'Bulan maksimal 12',
            'tahun.integer' => 'Tahun harus berupa angka',
            'tahun.min' => 'Tahun minimal 2020',
            'tahun.max' => 'Tahun maksimal 2100',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 0',
        ];
    }
}
