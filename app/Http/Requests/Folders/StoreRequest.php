<?php

namespace App\Http\Requests\Folders;

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
        $parentFolder = Folder::find($this->parent_id);

        return $this->user()->can('createIn', $parentFolder);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $groupId = $this->group_id;

        return [
            'title'     => [
                'required',
                'max:255',
            ],
            // Parent folder ID must exist and in the same group as requested
            'parent_id' => [
                'required',
                Rule::exists(Folder::class, 'id')->where(function ($query) use ($groupId) {
                    $query->where('group_id', '=', $groupId);
                }),
            ],
            'group_id'  => [
                'required',
                Rule::exists(Group::class, 'id'),
            ],
        ];
    }
}
