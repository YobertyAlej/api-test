<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrantPermissionToRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAllowedTo('manage_permissions');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'permission' => 'required'
        ];
    }
}
