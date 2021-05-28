<?php

namespace Framework\Baseapp\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class AbstractRequest
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Http\Requests
 * @license https://opensource.org/licenses/MIT MIT
 */
class AbstractRequest extends FormRequest
{
    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    //protected $redirectRoute = 'roles.create';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    /*public function authorize()
    {
        return true;
    }*/

    public function failedValidation($validator)
    {
        $error= $validator->errors()->all();
        // $error = $validator;
        throw new HttpResponseException(response()->json(['code' => 400 , 'message'=>$error[0]]));
    }

    /*public function rules()
    {
    }*/

    public function rulesbak()
    {
        return [
            'name'        => 'required|unique:admin_permissions|max:50',
            'name' => 'nullable|string|max:255',
            'name'  => [
                'required',
                Rule::unique('admin_permissions')->ignore($this->name, 'name'),
            ],
            'name'        => 'required|unique:admin_roles|max:50',
            'name'  => [
                'required',
                Rule::unique('users')->ignore($this->username, 'username'),
                'max:50',
            ],
            'username'	=> [
                'required',
            ],

            'permissions' => 'array|required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->email, 'email'),
            ],
            'email' => 'required|email|unique:newsletter_subscriptions',
            'email'         => 'required|email|unique:users,email',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'email'	                => 'required|email|unique:users|max:150',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,

            'password'              => 'required(nullable)|confirmed',
            'password_confirmation' => 'required',

            'posted_at' => 'required|after_or_equal:' . $this->comment->post->posted_at,
            'author_id' => 'required|exists:users,id',
            'image' => 'required|image',
            'posted_at' => 'required|date',
            'thumbnail_id' => 'nullable|exists:media,id',
            'author_id' => ['required', 'exists:users,id', new CanBeAuthor],
            'slug' => 'unique:posts,slug,' . (optional($this->post)->id ?: 'NULL'),
            'roles.*' => 'exists:roles,id',
            'username'      => 'required|max:50|unique:users,email',
            'provider'         => 'required|in:xiaomi,facebook,twitter,google',
            'oauth_data'       => 'required|json',
            'expires_at'       => 'required|date',
            'username' => 'required|unique:users|max:50',
            'post_id' => 'required|exists:posts,id',
            'password_confirmation' => 'required',
            'current_password' => ['required', new CurrentPassword],
            'username'              => 'required|unique:admin_users|max:190',
            'password_confirmation' => 'required',
            'permissions'           => 'array',
            'roles'                 => 'array',
            'username' => [
                'required',
                Rule::unique('admin_users')->ignore($this->username, 'username'),
                'max:190',
            ],
            'password_confirmation' => 'required',
            'permissions'           => 'array',
            'roles'                 => 'array',
            'username' => [
                'nullable',
                Rule::unique('users')->ignore($this->username, 'username'),
                'max:50',
            ],
            'gender' => [
                'nullable',
                Rule::in(['F', 'M']),
            ],
            'birthday' => 'nullable|date_format:Y-m-d',
            'country'  => [
                'nullable',
                Rule::in(CountryModel::getCountryISOCodeList()),
            ],
            'timezone' => 'nullable|timezone',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    /*protected function prepareForValidation(): void
    {
        $this->merge([
            'posted_at' => Carbon::parse($this->input('posted_at')),
            'slug' => Str::slug($this->input('title'))
        ]);
    }*/
    /**
     * Get the URL to redirect to on a validation error.
     *
     * @return string
     */
    /*protected function getRedirectUrl()
    {
        return $this->getSession()->previousUrl();
    }*/
}
