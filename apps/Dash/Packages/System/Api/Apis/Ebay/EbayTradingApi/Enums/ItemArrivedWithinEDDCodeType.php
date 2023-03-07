<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\EbayTradingApi\Enums;

class ItemArrivedWithinEDDCodeType
{
    const C_BUYER_DIDNT_PROVIDE_ANSWER = 'BuyerDidntProvideAnswer';
    const C_BUYER_INDICATED_ITEM_ARRIVED_WITHINEDD_RANGE = 'BuyerIndicatedItemArrivedWithinEDDRange';
    const C_BUYER_INDICATED_ITEM_NOT_ARRIVED_WITHINEDD_RANGE = 'BuyerIndicatedItemNotArrivedWithinEDDRange';
    const C_CUSTOM_CODE = 'CustomCode';
    const C_EDD_QUESTION_WAS_NOT_ASKED = 'EddQuestionWasNotAsked';
}
