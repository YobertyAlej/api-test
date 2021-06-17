<?php

namespace App\Http\Requests;

use App\Repositories\RoleRepository;
use Illuminate\Foundation\Http\FormRequest;

class DeleteRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(RoleRepository $role_repo)
    {
        $role_id = $this->segment(3);
        if($role_repo->getSuperAdmin()->id != $role_id){
            return false;
        }
        
        return $this->user()->isAllowedTo('manage_roles');
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
