<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProyekRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_proyek' => [
                'required',
                'string',
                'max:255',
                Rule::unique('proyeks', 'nama_proyek')->ignore($this->route('proyek')),
            ],
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'aplikasi_id' => ['nullable', 'integer', 'exists:aplikasis,id_aplikasi'],
            'deskripsi' => ['required', 'string', 'max:5000'],
        ];
    }
}
