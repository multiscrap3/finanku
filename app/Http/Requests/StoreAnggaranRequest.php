<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnggaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'kategori_id' => [
                'required',
                'exists:kategori,id',
                Rule::unique('anggaran')->where(function ($query) {
                    return $query->where('household_id', auth()->user()->household_id)
                        ->where('bulan', $this->bulan)
                        ->where('tahun', $this->tahun);
                }),
            ],
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2100'],
            'jumlah' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori harus dipilih',
            'kategori_id.exists' => 'Kategori tidak ditemukan',
            'kategori_id.unique' => 'Anggaran untuk kategori ini di bulan/tahun tersebut sudah ada',
            'bulan.required' => 'Bulan harus diisi',
            'bulan.integer' => 'Bulan harus berupa angka',
            'bulan.min' => 'Bulan minimal 1',
            'bulan.max' => 'Bulan maksimal 12',
            'tahun.required' => 'Tahun harus diisi',
            'tahun.integer' => 'Tahun harus berupa angka',
            'tahun.min' => 'Tahun minimal 2020',
            'tahun.max' => 'Tahun maksimal 2100',
            'jumlah.required' => 'Jumlah anggaran harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 0',
        ];
    }
}
