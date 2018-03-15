<?php
namespace OrangeShadow\Polls;

class PollProxy
{

    protected $writer;

    function __construct(Poll $poll)
    {
        $this->writer = new $poll->type($poll);
    }


    public function __call($method,$arguments)
    {
        return call_user_func_array(array($this->writer, $method), $arguments);
    }

}