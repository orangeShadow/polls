<?php

namespace OrangeShadow\Polls\Exceptions;

class AlreadyCastYourVoteException extends Exception
{
    public function __construct()
    {
        parent::__construct(trans('polls.AlreadyCastYourVote'));
    }
}