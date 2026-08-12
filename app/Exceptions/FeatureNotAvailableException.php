<?php

namespace App\Exceptions;

use Exception;

class FeatureNotAvailableException extends Exception
{
    public string $feature;

    public function __construct(string $feature, string $message = '')
    {
        $this->feature = $feature;
        parent::__construct($message ?: __('exceptions.feature_not_available', ['feature' => $feature]));
    }
}
