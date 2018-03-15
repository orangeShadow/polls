<?php

namespace OrangeShadow\Polls\Exceptions;

class PollIsClosedException extends Exception
{
    public function __construct()
    {
        parent::__construct(trans('polls.PollIsClosed'));
    }
}