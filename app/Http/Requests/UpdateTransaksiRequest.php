<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransaksiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'jenis' => ['sometimes', 'in:pemasukan,pengeluaran,transfer'],
            'kategori_id' => ['sometimes', 'exists:kategori,id'],
            'sumber_transaksi_id' => ['sometimes', 'exists:sumber_transaksi,id'],
            'jumlah' => ['sometimes', 'numeric', 'min:0'],
            'tanggal' => ['sometimes', 'date'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti_transaksi' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'transfer_ke_id' => ['nullable', 'required_if:jenis,transfer', 'exists:sumber_transaksi,id', 'different:sumber_transaksi_id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'jenis.in' => 'Jenis transaksi tidak valid',
            'kategori_id.exists' => 'Kategori tidak ditemukan',
            'sumber_transaksi_id.exists' => 'Sumber transaksi tidak ditemukan',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 0',
            'tanggal.date' => 'Format tanggal tidak valid',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
            'bukti_transaksi.file' => 'Bukti transaksi harus berupa file',
            'bukti_transaksi.mimes' => 'Bukti transaksi harus berformat jpg, jpeg, png, atau pdf',
            'bukti_transaksi.max' => 'Ukuran bukti transaksi maksimal 2MB',
            'transfer_ke_id.required_if' => 'Tujuan transfer harus dipilih',
            'transfer_ke_id.exists' => 'Tujuan transfer tidak ditemukan',
            'transfer_ke_id.different' => 'Tujuan transfer harus berbeda dengan sumber',
            'tags.array' => 'Format tags tidak valid',
            'tags.*.exists' => 'Tag tidak ditemukan',
        ];
    }
}
