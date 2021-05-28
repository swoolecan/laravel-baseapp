<?php

namespace Framework\Baseapp\Exceptions;

use Throwable;

class BusinessException extends \Exception
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = $code;
        }

        parent::__construct($message, $code, $previous);
    }
}
