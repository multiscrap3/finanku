<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHutangPiutangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis' => 'required|in:hutang,piutang',
            'nama_pihak' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'jatuh_tempo' => 'nullable|date|after_or_equal:tanggal',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis wajib dipilih',
            'jenis.in' => 'Jenis harus hutang atau piutang',
            'nama_pihak.required' => 'Nama pihak wajib diisi',
            'nama_pihak.max' => 'Nama pihak maksimal 255 karakter',
            'jumlah.required' => 'Jumlah wajib diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 0',
            'tanggal.required' => 'Tanggal wajib diisi',
            'tanggal.date' => 'Tanggal harus berupa tanggal yang valid',
            'jatuh_tempo.date' => 'Jatuh tempo harus berupa tanggal yang valid',
            'jatuh_tempo.after_or_equal' => 'Jatuh tempo harus setelah atau sama dengan tanggal',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'household_id' => auth()->user()->household_id,
            'sisa' => $this->jumlah,
            'status' => 'aktif',
        ]);
    }
}
