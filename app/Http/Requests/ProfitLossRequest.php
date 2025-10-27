<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfitLossRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bulan' => 'required|date_format:Y-m', // ✅ pastikan format YYYY-MM
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ];
    }

    public function messages(): array
    {
        return [
            'bulan.required' => 'Bulan wajib diisi (format: YYYY-MM).',
            'bulan.date_format' => 'Format bulan harus YYYY-MM.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}
