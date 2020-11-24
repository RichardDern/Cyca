<?php

namespace App\Http\Requests\Folders;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SetPermissionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('setPermission', $this->folder);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ability' => [
                'nullable',
                Rule::in([
                    'can_create_folder',
                    'can_update_folder',
                    'can_delete_folder',
                    'can_create_document',
                    'can_delete_document'
                ])
            ],
            'granted' => [
                'nullable',
                'boolean'
            ],
            'user_id' => [
                'nullable',
                Rule::exists('users', 'id')
            ]
        ];
    }
}
