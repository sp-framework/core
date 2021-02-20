<?php

/**
 * A property was get/set that doesn't exist.
 */

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Exceptions;

class UnknownPropertyException extends \Exception
{
    /**
     * @param string $property The property name that does not exist.
     * @param int $code|0
     * @param \Exception
     */
    public function __construct($property, $code = 0, \Exception $previous = null)
    {
        parent::__construct("Unknown property $property", $code, $previous);
    }
}
