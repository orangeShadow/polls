<?php

namespace OrangeShadow\Polls;

use OrangeShadow\Polls\Poll;

abstract class PollTypeAbstract
{

    protected $poll = null;

    public function __construct(Poll $poll)
    {
        $this->poll = $poll;
    }

    public abstract function voting(int $user_id, $option);

    public abstract function getResult();



}