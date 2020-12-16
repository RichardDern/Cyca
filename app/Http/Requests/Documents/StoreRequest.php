<?php

namespace App\Http\Requests\Documents;

use App\Models\Folder;
use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $folder = Folder::find($this->folder_id);

        return $this->user()->can('createBookmarkIn', $folder);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url'       => [
                'required',
                'url',
            ],
            'group_id'  => [
                'required',
                Rule::exists(Group::class, 'id'),
            ],
            'folder_id' => [
                'required',
                Rule::exists(Folder::class, 'id'),
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'url' => urldecode($this->url),
        ]);
    }
}
