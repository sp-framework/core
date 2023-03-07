<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Enums;

class PaymentHoldStatusCodeType
{
    const C_CUSTOM_CODE = 'CustomCode';
    const C_MERCHANT_HOLD = 'MerchantHold';
    const C_NEW_SELLER_HOLD = 'NewSellerHold';
    const C_NONE = 'None';
    const C_PAYMENT_HOLD = 'PaymentHold';
    const C_PAYMENT_REVIEW = 'PaymentReview';
    const C_RELEASED = 'Released';
    const C_RELEASE_CONFIRMED = 'ReleaseConfirmed';
    const C_RELEASE_FAILED = 'ReleaseFailed';
    const C_RELEASE_PENDING = 'ReleasePending';
}
