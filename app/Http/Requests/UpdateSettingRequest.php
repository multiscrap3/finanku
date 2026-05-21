<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mata_uang' => 'nullable|string|max:10',
            'format_tanggal' => 'nullable|string|max:20',
            'zona_waktu' => 'nullable|string|max:50',
            'bahasa' => 'nullable|string|max:10',
            'notifikasi_email' => 'nullable|boolean',
            'notifikasi_push' => 'nullable|boolean',
            'notifikasi_anggaran' => 'nullable|boolean',
            'notifikasi_tabungan' => 'nullable|boolean',
            'notifikasi_hutang' => 'nullable|boolean',
            'tema' => 'nullable|in:light,dark,auto',
        ];
    }

    public function messages(): array
    {
        return [
            'mata_uang.max' => 'Mata uang maksimal 10 karakter',
            'format_tanggal.max' => 'Format tanggal maksimal 20 karakter',
            'zona_waktu.max' => 'Zona waktu maksimal 50 karakter',
            'bahasa.max' => 'Bahasa maksimal 10 karakter',
            'notifikasi_email.boolean' => 'Notifikasi email harus berupa boolean',
            'notifikasi_push.boolean' => 'Notifikasi push harus berupa boolean',
            'notifikasi_anggaran.boolean' => 'Notifikasi anggaran harus berupa boolean',
            'notifikasi_tabungan.boolean' => 'Notifikasi tabungan harus berupa boolean',
            'notifikasi_hutang.boolean' => 'Notifikasi hutang harus berupa boolean',
            'tema.in' => 'Tema harus light, dark, atau auto',
        ];
    }
}
