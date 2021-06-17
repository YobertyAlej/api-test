<?php

namespace App\Http\Requests;

use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(UserRepository $user_repo)
    {
        try {
            $user_id = $this->segment(3);
            if($user_repo->findOrFail($user_id)->isSuperAdmin()){
                return $this->user()->isSuperAdmin();
            }
        } catch(UserNotFoundException $e){}

        return $this->user()->isAllowedTo('update_user');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'max:200',
            'password' => [Password::min(8)],
            'email' => 'email|unique:users|max:200',
            'dob' => 'date_format:Y-m-d|before:'. date('Y-m-d'),
            'gender' => 'in:M,m,F,f',
            'dni' => 'alpha_num',
            'address' => 'max:500',
            'country' => 'max:50'
        ];
    }
}
