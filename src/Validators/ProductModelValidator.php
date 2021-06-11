<?php

namespace Yeelight\Validators;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\LaravelValidator;

/**
 * Class ProductModelValidator
 *
 * @category Yeelight
 *
 * @package Yeelight\Validators
 *
 * @author Sheldon Lee <xdlee110@gmail.com>
 *
 * @license https://opensource.org/licenses/MIT MIT
 *
 * @link https://www.yeelight.com
 */
class ProductModelValidator extends LaravelValidator
{
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
        'title'	     => '	required',
        'model_name'	=> '	required|max:150',
        'code'	      => '	required|max:50',
    ],
        ValidatorInterface::RULE_UPDATE => [
        'title'	     => '	required',
        'model_name'	=> '	required|max:150',
        'code'	      => '	required|max:50',
    ],
   ];
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
        'user_id'	         => 'required',
        'provider'	        => 'required',
        'provider_user_id'	=> 'required',
    ],
        ValidatorInterface::RULE_UPDATE => [
        'user_id'	         => 'required',
        'provider'	        => 'required',
        'provider_user_id'	=> 'required',
    ],
   ];
    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ],
        ValidatorInterface::RULE_UPDATE => [
            'password' => 'required|min:8',
        ],
    ];
}
