<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('pegawai');

        return [
            'nama' => ['required', 'string', 'max:255', Rule::unique('pegawais', 'nama')->ignore($employee)],
            'nomor_telepon' => [
                'required',
                'regex:/^\+?[0-9]{9,15}$/',
                Rule::unique('pegawais', 'nomor_telepon')->ignore($employee),
            ],
            'email' => ['required', 'email', 'max:255', Rule::unique('pegawais', 'email')->ignore($employee)],
        ];
    }
}
