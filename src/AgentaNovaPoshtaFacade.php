<?php

namespace Agenta\AgentaNovaPoshta;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Agenta\AgentaNovaPoshta\Skeleton\SkeletonClass
 */
class AgentaNovaPoshtaFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'agentanovaposhta';
    }
}
