<?php

namespace OrangeShadow\Polls\Exceptions;


class Exception extends  \Exception
{
    public function __construct($message="")
    {
        \Exception::__construct($message);
    }
}