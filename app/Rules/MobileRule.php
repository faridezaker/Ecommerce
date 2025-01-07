<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MobileRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = '/^(\+98|98|0)?9\d{9}$/';

        if (!preg_match($pattern, $value)) {
            $fail('شماره موبایل وارد شده معتبر نیست.');
        }
    }
}
