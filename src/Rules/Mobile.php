<?php

namespace Framework\Baseapp\Rules;

use Illuminate\Contracts\Validation\Rule;

class Mobile implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $pattern = '/^1[3456789]{1}\d{9}$/';
        $res = preg_match($pattern, $value);

        return $res > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '手机号有误';
    }
}
