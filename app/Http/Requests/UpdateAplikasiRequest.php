<?php

namespace App\Http\Requests;

use App\Models\Aplikasi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAplikasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $application = $this->route('aplikasi');

        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aplikasis', 'nama')->ignore($application),
            ],
            'opd' => ['required', 'string', 'max:255'],
            'uraian' => ['nullable', 'string', 'max:10000'],
            'tahun_pembuatan' => ['required', 'date', 'before_or_equal:today'],
            'jenis' => ['required', 'string', 'max:255'],
            'basis_aplikasi' => ['required', Rule::in(Aplikasi::BASES)],
            'bahasa_framework' => ['required', 'string', 'max:255'],
            'database' => ['required', 'string', 'max:255'],
            'pengembang' => ['required', 'string', 'max:255'],
            'lokasi_server' => ['required', 'string', 'max:255'],
            'status_pemakaian' => ['required', Rule::in(Aplikasi::STATUSES)],
            'atribut' => ['sometimes', 'array'],
        ];
    }
}
