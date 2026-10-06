<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAplikasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', 'unique:aplikasis,nama'],
            'opd' => ['required', 'string', 'max:255'],
            'uraian' => ['nullable', 'string'],
            'tahun_pembuatan' => ['required', 'date'],
            'jenis' => ['required', 'string', 'max:255'],
            'basis_aplikasi' => ['required', 'in:Website,Desktop,Mobile'],
            'bahasa_framework' => ['required', 'string', 'max:255'],
            'database' => ['required', 'string', 'max:255'],
            'pengembang' => ['required', 'string', 'max:255'],
            'lokasi_server' => ['required', 'string', 'max:255'],
            'status_pemakaian' => ['required', 'in:Aktif,Tidak Aktif'],
            'atribut' => ['sometimes', 'array'],
        ];
    }
}
