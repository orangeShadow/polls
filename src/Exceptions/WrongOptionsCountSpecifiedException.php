<?php

namespace OrangeShadow\Polls\Exceptions;


class WrongOptionsCountSpecifiedException extends Exception
{
    public function __construct($message = "")
    {
        parent::__construct(trans('poll.WrongOptionsCountSpecified'));
    }
}