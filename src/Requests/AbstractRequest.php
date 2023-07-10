<?php

namespace Framework\Baseapp\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Swoolecan\Foundation\Requests\TraitRequest;

/**
 * Class AbstractRequest
 *
 * @category Framework\Baseapp
 * @package Framework\Baseapp\Http\Requests
 * @license https://opensource.org/licenses/MIT MIT
 */
class AbstractRequest extends FormRequest
{
    use TraitRequest;

    public function __construct($params = [])
    {
        $this->_scene = $params['scene'] ?? '';
        $this->_repository = $params['repository'] ?? null;
        parent::__construct();
    }

    public function getRule()
    {
        return new Rule();
    }

    protected function _getKeyValues($field)
    {
        return Rule::in(array_keys($this->getRepository()->getKeyValues($field)));
    }

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
            'mobile' => 'required|mobile|exists:user,mobile',
            'captcha' => 'required|captcha',
            'serial' => ['bail', 'required', 'integer'],
            'name' => 'required|string|max:50|unique:admin_users|unique:users,email',
            'name'  => [
                'required',
                'nullable',
                'bail',
                'filled',
                'alpha_dash',
                'string',
                'between:60,300',
                Rule::unique('users')->ignore($this->username, 'username'),
                $this->getRule()->unique('user')->ignore($this->routeParam('id', 0), 'user_id'),
                $this->getRule()->unique('attachment_path')->where(function ($query) use ($params){
                    $query->where('parent_id', $params['parent_id'])->where('path', $params['path']);
                }),
                'max:50',

                $this->getRule()->unique('auth_resource')->where(function ($query) use ($app) {
                    return $query->where('app', $app);
                })->ignore($this->routeParam('code', 0)),
            ],

            'permissions' => 'array|required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($this->email, 'email'),
            ],
            'email' => 'required|email|unique:newsletter_subscriptions',
            'email' => 'required|email|unique:users,email',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,

            'current_password' => ['required', new CurrentPassword],
            'password' => ['bail', 'filled', 'alpha_dash', 'between:6,20'],
            'password' => 'required(nullable)|confirmed',
            'password' => 'sometimes|same:confirm_password',
            'password_confirmation' => 'required',

            'posted_at' => 'required|after_or_equal:' . $this->comment->post->posted_at,
            'image' => 'required|image',
            'slug' => 'unique:posts,slug,' . (optional($this->post)->id ?: 'NULL'),
            'oauth_data' => 'required|json',

            // exists
            'author_id' => 'required|exists:users,id',
            'author_id' => ['required', 'exists:users,id', new CanBeAuthor],
            'roles.*' => 'exists:roles,id',

            // in
            'status' => $this->_getKeyValues('status'),
            'provider' => 'required|in:xiaomi,facebook,twitter,google',
            'gender' => [
                'nullable',
                Rule::in(['F', 'M']),
            ],
            'country' => [
                'nullable',
                Rule::in(CountryModel::getCountryISOCodeList()),
            ],

            'expires_at' => 'required|date',
            'birthday' => 'nullable|date_format:Y-m-d',
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
