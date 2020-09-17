<?php

namespace App\Http\Requests\Documents;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = $this->user()->id;

        return [
            'url'     => [
                'required',
                'url',
            ],
            /**
             * folder_id must be specified, and pointing to an existing folder
             * already owned by current user
             */
            'folder_id' => [
                'required',
                Rule::exists('App\Models\Folder', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }),
            ],
        ];
    }
}
