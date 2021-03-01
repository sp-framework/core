<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Enums;

class CancelStatusCodeType
{
    const C_CANCEL_CLOSED_FOR_COMMITMENT = 'CancelClosedForCommitment';
    const C_CANCEL_CLOSED_NO_REFUND = 'CancelClosedNoRefund';
    const C_CANCEL_CLOSED_UNKNOWN_REFUND = 'CancelClosedUnknownRefund';
    const C_CANCEL_CLOSED_WITH_REFUND = 'CancelClosedWithRefund';
    const C_CANCEL_COMPLETE = 'CancelComplete';
    const C_CANCEL_FAILED = 'CancelFailed';
    const C_CANCEL_PENDING = 'CancelPending';
    const C_CANCEL_REJECTED = 'CancelRejected';
    const C_CANCEL_REQUESTED = 'CancelRequested';
    const C_CUSTOM_CODE = 'CustomCode';
    const C_INVALID = 'Invalid';
    const C_NOT_APPLICABLE = 'NotApplicable';
}
