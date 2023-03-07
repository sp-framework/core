<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Enums;

class ReturnStatusCodeType
{
    const C_CUSTOM_CODE = 'CustomCode';
    const C_INVALID = 'Invalid';
    const C_NOT_APPLICABLE = 'NotApplicable';
    const C_RETURN_CLOSED_ESCALATED = 'ReturnClosedEscalated';
    const C_RETURN_CLOSED_NO_REFUND = 'ReturnClosedNoRefund';
    const C_RETURN_CLOSED_WITH_REFUND = 'ReturnClosedWithRefund';
    const C_RETURN_DELIVERED = 'ReturnDelivered';
    const C_RETURN_ESCALATED = 'ReturnEscalated';
    const C_RETURN_ESCALATED_CLOSED_NO_REFUND = 'ReturnEscalatedClosedNoRefund';
    const C_RETURN_ESCALATED_CLOSED_WITH_REFUND = 'ReturnEscalatedClosedWithRefund';
    const C_RETURN_ESCALATED_PENDINGCS = 'ReturnEscalatedPendingCS';
    const C_RETURN_ESCALATED_PENDING_BUYER = 'ReturnEscalatedPendingBuyer';
    const C_RETURN_ESCALATED_PENDING_SELLER = 'ReturnEscalatedPendingSeller';
    const C_RETURN_OPEN = 'ReturnOpen';
    const C_RETURN_REQUEST_CLOSED_NO_REFUND = 'ReturnRequestClosedNoRefund';
    const C_RETURN_REQUEST_CLOSED_WITH_REFUND = 'ReturnRequestClosedWithRefund';
    const C_RETURN_REQUEST_PENDING = 'ReturnRequestPending';
    const C_RETURN_REQUEST_PENDING_APPROVAL = 'ReturnRequestPendingApproval';
    const C_RETURN_REQUEST_REJECTED = 'ReturnRequestRejected';
    const C_RETURN_SHIPPED = 'ReturnShipped';
}
