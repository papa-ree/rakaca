<?php

namespace Paparee\Rakaca\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Paparee\Rakaca\Enums\SubmissionStatus;

class SubmissionStatusCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?SubmissionStatus
    {
        if ($value === null || $value === '') {
            return null;
        }

        return SubmissionStatus::fromLegacy($value);
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value instanceof SubmissionStatus) {
            return $value->value;
        }

        if (is_string($value)) {
            return SubmissionStatus::tryFrom($value)?->value;
        }

        return null;
    }
}
