<?php

namespace OrangeShadow\Polls\Exceptions;

class OptionTypeException extends Exception
{
    public function __construct()
    {
        parent::__construct(trans('polls.WrongOptionType'));
    }
}