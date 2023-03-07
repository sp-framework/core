<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Enums;

class DisputeExplanationCodeType
{
    const C_BUYER_HAS_NOT_RESPONDED = 'BuyerHasNotResponded';
    const C_BUYER_NOT_CLEARED_TO_PAY = 'BuyerNotClearedToPay';
    const C_BUYER_NOT_PAID = 'BuyerNotPaid';
    const C_BUYER_NO_LONGER_REGISTERED = 'BuyerNoLongerRegistered';
    const C_BUYER_NO_LONGER_WANTS_ITEM = 'BuyerNoLongerWantsItem';
    const C_BUYER_PAYMENT_NOT_RECEIVED_OR_CLEARED = 'BuyerPaymentNotReceivedOrCleared';
    const C_BUYER_PURCHASING_MISTAKE = 'BuyerPurchasingMistake';
    const C_BUYER_REFUSED_TO_PAY = 'BuyerRefusedToPay';
    const C_BUYER_RETURNED_ITEM_FOR_REFUND = 'BuyerReturnedItemForRefund';
    const C_CUSTOM_CODE = 'CustomCode';
    const C_OTHER_EXPLANATION = 'OtherExplanation';
    const C_PAYMENT_METHOD_NOT_SUPPORTED = 'PaymentMethodNotSupported';
    const C_SELLER_DOESNT_SHIP_TO_COUNTRY = 'SellerDoesntShipToCountry';
    const C_SELLER_RAN_OUT_OF_STOCK = 'SellerRanOutOfStock';
    const C_SHIPPING_ADDRESS_NOT_CONFIRMED = 'ShippingAddressNotConfirmed';
    const C_SHIP_COUNTRY_NOT_SUPPORTED = 'ShipCountryNotSupported';
    const C_UNABLE_TO_RESOLVE_TERMS = 'UnableToResolveTerms';
    const C_UNSPECIFIED = 'Unspecified';
    const C_UPI_ASSISTANCE = 'UPIAssistance';
    const C_UPI_ASSISTANCE_DISABLED = 'UPIAssistanceDisabled';
}
