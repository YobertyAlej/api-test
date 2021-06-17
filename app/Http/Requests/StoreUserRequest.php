<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAllowedTo('create_user');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:200',
            'password' => ['required', Password::min(8)],
            'email' => 'required|email|unique:users|max:200',
            'dob' => 'required|date_format:Y-m-d|before:'. date('Y-m-d'),
            'gender' => 'required|in:M,m,F,f',
            'dni' => 'required|alpha_num',
            'address' => 'required|max:500',
            'country' => 'required|max:50',
            'phone' => 'required',
        ];
    }
}
