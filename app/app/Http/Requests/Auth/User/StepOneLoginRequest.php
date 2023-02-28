<?php

namespace App\Http\Requests\Auth\User;

use App\Enums\OtpTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StepOneLoginRequest extends FormRequest
{

    protected  $valueRole = '';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {

        if (request()->type == OtpTypes::SMS->value) {
            $this->valueRole = 'regex:/[0]{1}[0-9]{10}/';
        } else {
            $this->valueRole = 'email';
        }

        return [
            'type' => ['required', 'string', Rule::in(array_column( OtpTypes::cases(), 'value'))] ,//move this to enum
            'value' => 'string|required|' . $this->valueRole ,//add role validation for check mobile or email
        ];
    }


}
