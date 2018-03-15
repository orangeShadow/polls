<?php

namespace OrangeShadow\Polls;

use OrangeShadow\Polls\Poll;
use OrangeShadow\Polls\Exceptions\PollIsClosedException;

abstract class PollTypeAbstract
{

    protected $poll = null;

    public function __construct(Poll $poll)
    {
        if ($poll->isClosed())
            throw new PollIsClosedException();

        $this->poll = $poll;
    }

    public abstract function voting(int $user_id, $option);

    public abstract function getResult();



}