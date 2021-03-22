<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Enums;

class PaymentStatusCodeType
{
    const C_BUYERE_CHECK_BOUNCED = 'BuyerECheckBounced';
    const C_BUYER_CREDIT_CARD_FAILED = 'BuyerCreditCardFailed';
    const C_BUYER_FAILED_PAYMENT_REPORTED_BY_SELLER = 'BuyerFailedPaymentReportedBySeller';
    const C_CUSTOM_CODE = 'CustomCode';
    const C_NO_PAYMENT_FAILURE = 'NoPaymentFailure';
    const C_PAYMENT_IN_PROCESS = 'PaymentInProcess';
    const C_PAY_PAL_PAYMENT_IN_PROCESS = 'PayPalPaymentInProcess';
}
