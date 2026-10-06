<?php

namespace App\Services;

use App\Models\AtributTambahan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttributeValueValidator
{
    public function validate(array $values): array
    {
        if ($values === []) {
            return [];
        }

        $attributes = AtributTambahan::query()
            ->whereKey(array_keys($values))
            ->get()
            ->keyBy('id_atribut');

        if ($attributes->count() !== count($values)) {
            throw ValidationException::withMessages([
                'atribut' => 'One or more attributes do not exist.',
            ]);
        }

        $rules = [];
        foreach ($attributes as $attribute) {
            $rules["atribut.{$attribute->id_atribut}"] = $this->rulesFor($attribute);
        }

        return Validator::make(['atribut' => $values], $rules)->validate()['atribut'];
    }

    public function validateDefinitionChange(AtributTambahan $attribute, array $definition): void
    {
        $candidate = new AtributTambahan($definition);
        $invalidApplications = $attribute->aplikasis
            ->filter(fn ($application) => ! Validator::make(
                ['value' => $application->pivot->nilai_atribut],
                ['value' => $this->rulesFor($candidate)]
            )->passes())
            ->pluck('nama')
            ->all();

        if ($invalidApplications !== []) {
            throw ValidationException::withMessages([
                'tipe_data' => 'The new type is incompatible with existing values for: '.implode(', ', $invalidApplications).'.',
            ]);
        }
    }

    private function rulesFor(AtributTambahan $attribute): array
    {
        $rules = ['nullable'];

        return match ($attribute->tipe_data) {
            'number' => [...$rules, 'numeric'],
            'date' => [...$rules, 'date_format:Y-m-d'],
            'text' => [...$rules, 'string', 'max:10000'],
            'enum' => [...$rules, Rule::in($attribute->enum_options ?? [])],
            default => [...$rules, 'string', 'max:255'],
        };
    }
}
