<?php

namespace App\Http\Requests;

use App\Exceptions\UserNotFoundException;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
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
                return false;
            }
        } catch(UserNotFoundException $e){}

        return $this->user()->isAllowedTo('delete_user');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
