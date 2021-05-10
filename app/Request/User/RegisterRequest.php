<?php
declare(strict_types=1);

namespace App\Request\User;

use Hyperf\Validation\Request\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the User is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
//            'email' => 'unique:account_user|email',
//            'phone' => 'unique:account_user|regex:/^1[34578]\d{9}$/',
            'username' => 'required',
            'password' => 'required',
        ];
    }
}