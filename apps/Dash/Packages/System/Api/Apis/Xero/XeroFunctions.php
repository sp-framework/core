<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero;

use Apps\Dash\Packages\System\Api\Apis\Xero\XeroDebugger;
use Apps\Dash\Packages\System\Api\Base\BaseFunctions;

class XeroFunctions extends BaseFunctions
{
    public static $STRICT_PROPERTY_TYPES = true;

    /**
     * Applies the default debugger if required.
     *
     * @param mixed $value XeroDebugger options.
     * @param array &$configuration The configuration array where the resolved debugger will be stored.
     */
    public static function applyDebug($value, array &$configuration)
    {
        if ($value !== false) {
            $configuration['debug'] = new XeroDebugger($value === true ? [] : $value);
        } else {
            $configuration['debug'] = false;
        }
    }

    /**
     * Helper function that returns true if the property type should be checked.
     *
     * @param string $type The type name.
     *
     * @return bool
     */
    public static function checkPropertyType($type)
    {
        if (self::$STRICT_PROPERTY_TYPES) {
            return true;
        }

        parent::checkPropertyType($type);
    }
}