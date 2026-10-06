<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kategori', 'nama_kategori')->ignore($this->route('kategori')),
            ],
        ];
    }
}
