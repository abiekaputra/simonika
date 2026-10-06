<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAtributRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_atribut' => ['required', 'string', 'max:100', 'unique:atribut_tambahans,nama_atribut'],
            'tipe_data' => ['required', Rule::in(['varchar', 'number', 'date', 'text', 'enum'])],
            'enum_options' => ['required_if:tipe_data,enum', 'nullable', 'array', 'min:1'],
            'enum_options.*' => ['required', 'string', 'max:100', 'distinct'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->enum_options)) {
            $this->merge(['enum_options' => json_decode($this->enum_options, true)]);
        }
    }
}
