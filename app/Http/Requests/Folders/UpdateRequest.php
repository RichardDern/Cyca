<?php

namespace App\Http\Requests\Folders;

use App\Models\Folder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (empty($this->parent_id)) {
            return $this->folder->type === 'root';
        }

        $parent = Folder::find($this->parent_id);

        return $this->user()->can('createIn', $parent) && $this->user()->can('update', $this->folder);
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
            'title'       => [
                'required',
                'max:255',
            ],
            'parent_id'   => [
                'nullable',
                Rule::exists('App\Models\Folder', 'id')->where(function ($query) use ($groupId) {
                    $query->where('group_id', '=', $groupId);
                }),
            ],
            'group_id'    => [
                'required',
                Rule::exists('App\Models\Group', 'id'),
            ],
            'is_expanded' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->isMoving()) {
                if ($this->folder->type !== 'folder') {
                    // Trying to move a "special" folder like root
                    $validator->errors()->add('parent_id', __("You cannot move this folder"));
                } else if ($this->targetParentIsDescendant()) {
                    $validator->errors()->add('parent_id', __("You cannot move this folder to a descendant"));
                }
            }
        });
    }

    /**
     * Return a boolean value indicating if we're moving a folder
     *
     * @return boolean
     */
    private function isMoving()
    {
        return $this->parent_id !== $this->folder->parent_id;
    }

    /**
     * Return a boolean value indicating if we're trying to move a folder into
     * one of its descendants
     *
     * @return boolean
     */
    private function targetParentIsDescendant()
    {
        $parent = Folder::find($this->parent_id);

        while ($parent) {
            if ($parent->id === $this->folder->id) {
                return true;
            }

            $parent = $parent->parent;
        }

        return false;
    }
}
