<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PendataanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'universitas' => ['required', 'string', 'max:255'],
            'jumlah_orang' => ['required', 'integer', 'min:1'],
            'tanggal_masuk' => ['required', 'date'],
            'tanggal_keluar' => ['required', 'date', 'after:tanggal_masuk'],
        ];
    }
}
