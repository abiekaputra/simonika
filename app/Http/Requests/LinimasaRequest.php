<?php

namespace App\Http\Requests;

use App\Models\Linimasa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LinimasaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'exists:pegawais,id'],
            'proyek_id' => ['required', 'exists:proyeks,id'],
            'status_proyek' => ['required', Rule::in(Linimasa::STATUSES)],
            'mulai' => ['required', 'date'],
            'tenggat' => ['required', 'date', 'after_or_equal:mulai'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:mulai'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
