<?php

namespace OrangeShadow\Polls;

use Illuminate\Support\Facades\Facade;

class PollProxyFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'PollProxy';
    }
}