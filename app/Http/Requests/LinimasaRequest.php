<?php

namespace App\Http\Requests;

use App\Models\Linimasa;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class LinimasaRequest extends FormRequest
{
    private const COMPLETED_STATUSES = ['Selesai Lebih Cepat', 'Tepat Waktu', 'Terlambat'];

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $status = $this->string('status_proyek')->toString();
            $completedAt = $this->input('tanggal_selesai');

            if (in_array($status, self::COMPLETED_STATUSES, true) && ! $completedAt) {
                $validator->errors()->add('tanggal_selesai', 'Completion date is required for a completed project.');

                return;
            }

            if (! in_array($status, self::COMPLETED_STATUSES, true) && $completedAt) {
                $validator->errors()->add('tanggal_selesai', 'Completion date is only allowed for a completed project.');

                return;
            }

            if ($completedAt && $this->input('tenggat')) {
                $this->validateCompletionStatus($validator, $status, $completedAt, $this->input('tenggat'));
            }
        });
    }

    private function validateCompletionStatus(
        Validator $validator,
        string $status,
        string $completedAt,
        string $deadline
    ): void {
        $completed = CarbonImmutable::parse($completedAt)->startOfDay();
        $deadlineDate = CarbonImmutable::parse($deadline)->startOfDay();
        $expected = match (true) {
            $completed->lt($deadlineDate) => 'Selesai Lebih Cepat',
            $completed->equalTo($deadlineDate) => 'Tepat Waktu',
            default => 'Terlambat',
        };

        if ($status !== $expected) {
            $validator->errors()->add('status_proyek', "Status must be '{$expected}' for the selected completion date.");
        }
    }
}
