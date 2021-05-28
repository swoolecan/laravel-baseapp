<?php

namespace Framework\Baseapp\Rules;

use Illuminate\Contracts\Validation\Rule;

class AbstractRule implements Rule
{
    public function passes($attribute, $value)
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return false;
        }

        return preg_match('/^(?:[\pL\pN\pM]+[\pZ\'_-])*[\pL\pN\pM]+$/u', $value) > 0;
    }

    public function message(): string
    {
        return trans('validation.alpha_name');
    }

    /*public function passes($attribute, $value)
    {
        $author = User::find($value);
        return $author->canBeAuthor();
    }*/
    /*public function passes($attribute, $value)
    {
        return Hash::check($value, auth()->user()->password);
    }*/
}
