<?php

namespace App\Http\Requests\V1\Auth;

use App\Rules\MobileRule;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'otp' => 'required|digits:6',
            'mobile' => ['required','min:11','max:11','exists:users,mobile',new MobileRule()]
        ];
    }
}
