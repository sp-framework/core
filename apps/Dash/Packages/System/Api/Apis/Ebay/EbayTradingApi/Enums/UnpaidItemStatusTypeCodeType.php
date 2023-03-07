<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Enums;

class UnpaidItemStatusTypeCodeType
{
    const C_AWAITING_BUYER_RESPONSE = 'AwaitingBuyerResponse';
    const C_AWAITING_SELLER_RESPONSE = 'AwaitingSellerResponse';
    const C_CUSTOM_CODE = 'CustomCode';
    const C_FINAL_VALUE_FEE_CREDITED = 'FinalValueFeeCredited';
    const C_FINAL_VALUE_FEE_DENIED = 'FinalValueFeeDenied';
    const C_FINAL_VALUE_FEE_ELIGIBLE = 'FinalValueFeeEligible';
    const C_UNPAID_ITEM_ELIGIBLE = 'UnpaidItemEligible';
    const C_UNPAID_ITEM_FILED = 'UnpaidItemFiled';
}
