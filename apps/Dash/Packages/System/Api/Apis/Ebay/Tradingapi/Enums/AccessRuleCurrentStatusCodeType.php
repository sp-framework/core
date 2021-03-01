<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Enums;

class AccessRuleCurrentStatusCodeType
{
    const C_CUSTOM_CODE = 'CustomCode';
    const C_DAILY_LIMIT_EXCEEDED = 'DailyLimitExceeded';
    const C_DAILY_SOFT_LIMIT_EXCEEDED = 'DailySoftLimitExceeded';
    const C_HOURLY_LIMIT_EXCEEDED = 'HourlyLimitExceeded';
    const C_HOURLY_SOFT_LIMIT_EXCEEDED = 'HourlySoftLimitExceeded';
    const C_NOT_SET = 'NotSet';
    const C_PERIODIC_LIMIT_EXCEEDED = 'PeriodicLimitExceeded';
    const C_PERIODIC_SOFT_LIMIT_EXCEEDED = 'PeriodicSoftLimitExceeded';
}
