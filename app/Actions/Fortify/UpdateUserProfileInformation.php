<?php

namespace App\Actions\Fortify;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param mixed $user
     */
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],

            'lang' => [
                'required',
                Rule::in(array_keys(config('lang'))),
            ],

            'theme' => [
                'nullable',
                Rule::in(['light', 'dark', 'auto']),
            ],
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name'  => $input['name'],
                'email' => $input['email'],
                'lang'  => $input['lang'],
                'theme' => $input['theme'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param mixed $user
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name'              => $input['name'],
            'email'             => $input['email'],
            'lang'              => $input['lang'],
            'theme'             => $input['theme'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
