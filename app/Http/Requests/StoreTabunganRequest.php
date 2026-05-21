<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTabunganRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'target' => 'required|numeric|min:0',
            'terkumpul' => 'nullable|numeric|min:0',
            'deadline' => 'nullable|date|after:today',
            'keterangan' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama tabungan wajib diisi',
            'nama.max' => 'Nama tabungan maksimal 255 karakter',
            'target.required' => 'Target tabungan wajib diisi',
            'target.numeric' => 'Target harus berupa angka',
            'target.min' => 'Target minimal 0',
            'terkumpul.numeric' => 'Jumlah terkumpul harus berupa angka',
            'terkumpul.min' => 'Jumlah terkumpul minimal 0',
            'deadline.date' => 'Deadline harus berupa tanggal yang valid',
            'deadline.after' => 'Deadline harus setelah hari ini',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default terkumpul to 0 if not provided
        if (!$this->has('terkumpul')) {
            $this->merge(['terkumpul' => 0]);
        }

        // Add household_id from authenticated user
        $this->merge([
            'household_id' => auth()->user()->household_id,
            'status' => 'aktif',
        ]);
    }
}
