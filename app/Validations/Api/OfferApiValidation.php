<?php

namespace App\Validations\Api;

use App\Validations\BaseValidation;
use App\Models\Periodicity;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class OfferApiValidation extends BaseValidation
{
    public function validate(array $data): array
    {
        Log::info('Validating offer data: ' . json_encode($data));

        $periodicityIds = Periodicity::pluck('id')->toArray();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'offer_type_id' => ['required', 'exists:offer_types,id'],
            'validity' => ['required', 'array'],
            'validity.valid_from' => ['required', 'date'],
            'validity.valid_until' => ['required', 'date', 'after:validity.valid_from'],
        ];

        if (isset($data['offer_type_id']) && in_array($data['offer_type_id'], [3, 4])) {
            $rules['periodicalDetails'] = ['required', 'array'];
            $rules['periodicalDetails.periodicity_id'] = ['required', Rule::in($periodicityIds)];

            if ($data['offer_type_id'] == 4) {
                $rules['periodicalDetails.day_of_week'] = ['nullable', 'integer', 'between:0,6'];
                $rules['periodicalDetails.time_of_day_start'] = ['nullable', 'date_format:H:i:s'];
                $rules['periodicalDetails.time_of_day_end'] = ['nullable', 'date_format:H:i:s', 'after:periodicalDetails.time_of_day_start'];

                $rules['periodicalDetails'][] = function ($attribute, $value, $fail) {
                    if (!isset($value['day_of_week']) &&
                        (empty($value['time_of_day_start']) || empty($value['time_of_day_end']))) {
                        $fail('For offer type 4, either day of week or time of day (both start and end) must be provided.');
                    }
                };

                $rules['periodicalDetails.time_of_day_end'][] = Rule::requiredIf(function () use ($data) {
                    return !empty($data['periodicalDetails']['time_of_day_start']);
                });
                $rules['periodicalDetails.time_of_day_start'][] = Rule::requiredIf(function () use ($data) {
                    return !empty($data['periodicalDetails']['time_of_day_end']);
                });
            }
        }

        $result = $this->runValidation($data, $rules);

        if (!empty($result['errors'])) {
            Log::warning('Offer validation failed: ' . json_encode($result['errors']));
        } else {
            Log::info('Offer validation passed');
        }

        return $result;
    }
}