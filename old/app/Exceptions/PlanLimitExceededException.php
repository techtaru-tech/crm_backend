<?php

namespace App\Exceptions;

use Exception;

class PlanLimitExceededException extends Exception
{
    public string $resourceType;
    public int $limit;
    public int $current;

    public function __construct(string $resourceType, int $limit, int $current, string $message = '')
    {
        $this->resourceType = $resourceType;
        $this->limit        = $limit;
        $this->current      = $current;

        parent::__construct($message ?: __('exceptions.plan_limit_exceeded', [
            'resource' => $resourceType,
            'current'  => $current,
            'limit'    => $limit,
        ]));
    }
}
