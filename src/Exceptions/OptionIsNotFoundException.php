<?php

namespace OrangeShadow\Polls\Exceptions;

class OptionIsNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct(trans('polls.OptionIsNotFound'));
    }
}