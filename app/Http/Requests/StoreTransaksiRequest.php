<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaksiRequest extends FormRequest
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
            'jenis' => ['required', 'in:pemasukan,pengeluaran,transfer'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'sumber_transaksi_id' => ['required', 'exists:sumber_transaksi,id'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['nullable', 'string', 'max:500'],
            'bukti_transaksi' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'ocr_image_path'  => ['nullable', 'string', 'max:500'],
            'ocr_history_id'  => ['nullable', 'integer', 'exists:ocr_history,id'],
            'ocr_items'       => ['nullable', 'string'],
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
            'jenis.required' => 'Jenis transaksi harus dipilih',
            'jenis.in' => 'Jenis transaksi tidak valid',
            'kategori_id.required' => 'Kategori harus dipilih',
            'kategori_id.exists' => 'Kategori tidak ditemukan',
            'sumber_transaksi_id.required' => 'Sumber transaksi harus dipilih',
            'sumber_transaksi_id.exists' => 'Sumber transaksi tidak ditemukan',
            'jumlah.required' => 'Jumlah harus diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 0',
            'tanggal.required' => 'Tanggal harus diisi',
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

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'jenis' => 'jenis transaksi',
            'kategori_id' => 'kategori',
            'sumber_transaksi_id' => 'sumber transaksi',
            'jumlah' => 'jumlah',
            'tanggal' => 'tanggal',
            'keterangan' => 'keterangan',
            'bukti_transaksi' => 'bukti transaksi',
            'transfer_ke_id' => 'tujuan transfer',
            'tags' => 'tags',
        ];
    }
}
