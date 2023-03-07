<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Enums;

class PaidStatusCodeType
{
    const C_BUYER_HAS_NOT_COMPLETED_CHECKOUT = 'BuyerHasNotCompletedCheckout';
    const C_CUSTOM_CODE = 'CustomCode';
    const C_ESCROW_PAYMENT_CANCELLED = 'EscrowPaymentCancelled';
    const C_MARKED_AS_PAID = 'MarkedAsPaid';
    const C_NOT_PAID = 'NotPaid';
    const C_PAID = 'Paid';
    const C_PAIDCOD = 'PaidCOD';
    const C_PAID_WITH_ESCROW = 'PaidWithEscrow';
    const C_PAID_WITH_PAISA_PAY = 'PaidWithPaisaPay';
    const C_PAID_WITH_PAISA_PAY_ESCROW = 'PaidWithPaisaPayEscrow';
    const C_PAID_WITH_PAY_PAL = 'PaidWithPayPal';
    const C_PAISA_PAY_NOT_PAID = 'PaisaPayNotPaid';
    const C_PAYMENT_PENDING = 'PaymentPending';
    const C_PAYMENT_PENDING_WITH_ESCROW = 'PaymentPendingWithEscrow';
    const C_PAYMENT_PENDING_WITH_PAISA_PAY = 'PaymentPendingWithPaisaPay';
    const C_PAYMENT_PENDING_WITH_PAISA_PAY_ESCROW = 'PaymentPendingWithPaisaPayEscrow';
    const C_PAYMENT_PENDING_WITH_PAY_PAL = 'PaymentPendingWithPayPal';
    const C_PAY_UPON_INVOICE = 'PayUponInvoice';
    const C_REFUNDED = 'Refunded';
    const C_WAITING_FORCOD_PAYMENT = 'WaitingForCODPayment';
}
