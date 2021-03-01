<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Services\TradingapiBaseService;

class TradingapiService extends TradingapiBaseService
{
    const API_VERSION = '1193';

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function addDispute(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddDisputeRequest $request)
    {
        return $this->addDisputeAsync($request)->wait();
    }

    public function addDisputeAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddDisputeRequest $request)
    {
        return $this->callOperationAsync(
            'AddDispute',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddDisputeResponse'
        );
    }

    public function addDisputeResponse(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddDisputeResponseRequest $request)
    {
        return $this->addDisputeResponseAsync($request)->wait();
    }

    public function addDisputeResponseAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddDisputeResponseRequest $request)
    {
        return $this->callOperationAsync(
            'AddDisputeResponse',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddDisputeResponseResponse'
        );
    }

    public function addFixedPriceItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddFixedPriceItemRequest $request)
    {
        return $this->addFixedPriceItemAsync($request)->wait();
    }

    public function addFixedPriceItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddFixedPriceItemRequest $request)
    {
        return $this->callOperationAsync(
            'AddFixedPriceItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddFixedPriceItemResponse'
        );
    }

    public function addItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemRequest $request)
    {
        return $this->addItemAsync($request)->wait();
    }

    public function addItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemRequest $request)
    {
        return $this->callOperationAsync(
            'AddItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemResponse'
        );
    }

    public function addItemFromSellingManagerTemplate(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemFromSellingManagerTemplateRequest $request)
    {
        return $this->addItemFromSellingManagerTemplateAsync($request)->wait();
    }

    public function addItemFromSellingManagerTemplateAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemFromSellingManagerTemplateRequest $request)
    {
        return $this->callOperationAsync(
            'AddItemFromSellingManagerTemplate',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemFromSellingManagerTemplateResponse'
        );
    }

    public function addItems(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemsRequest $request)
    {
        return $this->addItemsAsync($request)->wait();
    }

    public function addItemsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemsRequest $request)
    {
        return $this->callOperationAsync(
            'AddItems',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddItemsResponse'
        );
    }

    public function addMemberMessageAAQToPartner(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessageAAQToPartnerRequest $request)
    {
        return $this->addMemberMessageAAQToPartnerAsync($request)->wait();
    }

    public function addMemberMessageAAQToPartnerAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessageAAQToPartnerRequest $request)
    {
        return $this->callOperationAsync(
            'AddMemberMessageAAQToPartner',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessageAAQToPartnerResponse'
        );
    }

    public function addMemberMessageRTQ(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessageRTQRequest $request)
    {
        return $this->addMemberMessageRTQAsync($request)->wait();
    }

    public function addMemberMessageRTQAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessageRTQRequest $request)
    {
        return $this->callOperationAsync(
            'AddMemberMessageRTQ',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessageRTQResponse'
        );
    }

    public function addMemberMessagesAAQToBidder(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessagesAAQToBidderRequest $request)
    {
        return $this->addMemberMessagesAAQToBidderAsync($request)->wait();
    }

    public function addMemberMessagesAAQToBidderAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessagesAAQToBidderRequest $request)
    {
        return $this->callOperationAsync(
            'AddMemberMessagesAAQToBidder',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddMemberMessagesAAQToBidderResponse'
        );
    }

    public function addOrder(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddOrderRequest $request)
    {
        return $this->addOrderAsync($request)->wait();
    }

    public function addOrderAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddOrderRequest $request)
    {
        return $this->callOperationAsync(
            'AddOrder',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddOrderResponse'
        );
    }

    public function addSecondChanceItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSecondChanceItemRequest $request)
    {
        return $this->addSecondChanceItemAsync($request)->wait();
    }

    public function addSecondChanceItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSecondChanceItemRequest $request)
    {
        return $this->callOperationAsync(
            'AddSecondChanceItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSecondChanceItemResponse'
        );
    }

    public function addSellingManagerInventoryFolder(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerInventoryFolderRequest $request)
    {
        return $this->addSellingManagerInventoryFolderAsync($request)->wait();
    }

    public function addSellingManagerInventoryFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerInventoryFolderRequest $request)
    {
        return $this->callOperationAsync(
            'AddSellingManagerInventoryFolder',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerInventoryFolderResponse'
        );
    }

    public function addSellingManagerProduct(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerProductRequest $request)
    {
        return $this->addSellingManagerProductAsync($request)->wait();
    }

    public function addSellingManagerProductAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerProductRequest $request)
    {
        return $this->callOperationAsync(
            'AddSellingManagerProduct',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerProductResponse'
        );
    }

    public function addSellingManagerTemplate(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerTemplateRequest $request)
    {
        return $this->addSellingManagerTemplateAsync($request)->wait();
    }

    public function addSellingManagerTemplateAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerTemplateRequest $request)
    {
        return $this->callOperationAsync(
            'AddSellingManagerTemplate',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddSellingManagerTemplateResponse'
        );
    }

    public function addToItemDescription(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddToItemDescriptionRequest $request)
    {
        return $this->addToItemDescriptionAsync($request)->wait();
    }

    public function addToItemDescriptionAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddToItemDescriptionRequest $request)
    {
        return $this->callOperationAsync(
            'AddToItemDescription',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddToItemDescriptionResponse'
        );
    }

    public function addToWatchList(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddToWatchListRequest $request)
    {
        return $this->addToWatchListAsync($request)->wait();
    }

    public function addToWatchListAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddToWatchListRequest $request)
    {
        return $this->callOperationAsync(
            'AddToWatchList',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddToWatchListResponse'
        );
    }

    public function addTransactionConfirmationItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddTransactionConfirmationItemRequest $request)
    {
        return $this->addTransactionConfirmationItemAsync($request)->wait();
    }

    public function addTransactionConfirmationItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddTransactionConfirmationItemRequest $request)
    {
        return $this->callOperationAsync(
            'AddTransactionConfirmationItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\AddTransactionConfirmationItemResponse'
        );
    }

    public function completeSale(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\CompleteSaleRequest $request)
    {
        return $this->completeSaleAsync($request)->wait();
    }

    public function completeSaleAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\CompleteSaleRequest $request)
    {
        return $this->callOperationAsync(
            'CompleteSale',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\CompleteSaleResponse'
        );
    }

    public function confirmIdentity(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ConfirmIdentityRequest $request)
    {
        return $this->confirmIdentityAsync($request)->wait();
    }

    public function confirmIdentityAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ConfirmIdentityRequest $request)
    {
        return $this->callOperationAsync(
            'ConfirmIdentity',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ConfirmIdentityResponse'
        );
    }

    public function deleteMyMessages(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteMyMessagesRequest $request)
    {
        return $this->deleteMyMessagesAsync($request)->wait();
    }

    public function deleteMyMessagesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteMyMessagesRequest $request)
    {
        return $this->callOperationAsync(
            'DeleteMyMessages',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteMyMessagesResponse'
        );
    }

    public function deleteSellingManagerInventoryFolder(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerInventoryFolderRequest $request)
    {
        return $this->deleteSellingManagerInventoryFolderAsync($request)->wait();
    }

    public function deleteSellingManagerInventoryFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerInventoryFolderRequest $request)
    {
        return $this->callOperationAsync(
            'DeleteSellingManagerInventoryFolder',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerInventoryFolderResponse'
        );
    }

    public function deleteSellingManagerItemAutomationRule(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerItemAutomationRuleRequest $request)
    {
        return $this->deleteSellingManagerItemAutomationRuleAsync($request)->wait();
    }

    public function deleteSellingManagerItemAutomationRuleAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerItemAutomationRuleRequest $request)
    {
        return $this->callOperationAsync(
            'DeleteSellingManagerItemAutomationRule',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerItemAutomationRuleResponse'
        );
    }

    public function deleteSellingManagerProduct(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerProductRequest $request)
    {
        return $this->deleteSellingManagerProductAsync($request)->wait();
    }

    public function deleteSellingManagerProductAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerProductRequest $request)
    {
        return $this->callOperationAsync(
            'DeleteSellingManagerProduct',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerProductResponse'
        );
    }

    public function deleteSellingManagerTemplate(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerTemplateRequest $request)
    {
        return $this->deleteSellingManagerTemplateAsync($request)->wait();
    }

    public function deleteSellingManagerTemplateAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerTemplateRequest $request)
    {
        return $this->callOperationAsync(
            'DeleteSellingManagerTemplate',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerTemplateResponse'
        );
    }

    public function deleteSellingManagerTemplateAutomationRule(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerTemplateAutomationRuleRequest $request)
    {
        return $this->deleteSellingManagerTemplateAutomationRuleAsync($request)->wait();
    }

    public function deleteSellingManagerTemplateAutomationRuleAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerTemplateAutomationRuleRequest $request)
    {
        return $this->callOperationAsync(
            'DeleteSellingManagerTemplateAutomationRule',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DeleteSellingManagerTemplateAutomationRuleResponse'
        );
    }

    public function disableUnpaidItemAssistance(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DisableUnpaidItemAssistanceRequest $request)
    {
        return $this->disableUnpaidItemAssistanceAsync($request)->wait();
    }

    public function disableUnpaidItemAssistanceAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DisableUnpaidItemAssistanceRequest $request)
    {
        return $this->callOperationAsync(
            'DisableUnpaidItemAssistance',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\DisableUnpaidItemAssistanceResponse'
        );
    }

    public function endFixedPriceItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndFixedPriceItemRequest $request)
    {
        return $this->endFixedPriceItemAsync($request)->wait();
    }

    public function endFixedPriceItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndFixedPriceItemRequest $request)
    {
        return $this->callOperationAsync(
            'EndFixedPriceItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndFixedPriceItemResponse'
        );
    }

    public function endItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndItemRequest $request)
    {
        return $this->endItemAsync($request)->wait();
    }

    public function endItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndItemRequest $request)
    {
        return $this->callOperationAsync(
            'EndItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndItemResponse'
        );
    }

    public function endItems(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndItemsRequest $request)
    {
        return $this->endItemsAsync($request)->wait();
    }

    public function endItemsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndItemsRequest $request)
    {
        return $this->callOperationAsync(
            'EndItems',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\EndItemsResponse'
        );
    }

    public function extendSiteHostedPictures(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ExtendSiteHostedPicturesRequest $request)
    {
        return $this->extendSiteHostedPicturesAsync($request)->wait();
    }

    public function extendSiteHostedPicturesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ExtendSiteHostedPicturesRequest $request)
    {
        return $this->callOperationAsync(
            'ExtendSiteHostedPictures',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ExtendSiteHostedPicturesResponse'
        );
    }

    public function fetchToken(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\FetchTokenRequest $request)
    {
        return $this->fetchTokenAsync($request)->wait();
    }

    public function fetchTokenAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\FetchTokenRequest $request)
    {
        return $this->callOperationAsync(
            'FetchToken',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\FetchTokenResponse'
        );
    }

    public function getAccount(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAccountRequest $request)
    {
        return $this->getAccountAsync($request)->wait();
    }

    public function getAccountAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAccountRequest $request)
    {
        return $this->callOperationAsync(
            'GetAccount',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAccountResponse'
        );
    }

    public function getAdFormatLeads(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAdFormatLeadsRequest $request)
    {
        return $this->getAdFormatLeadsAsync($request)->wait();
    }

    public function getAdFormatLeadsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAdFormatLeadsRequest $request)
    {
        return $this->callOperationAsync(
            'GetAdFormatLeads',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAdFormatLeadsResponse'
        );
    }

    public function getAllBidders(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAllBiddersRequest $request)
    {
        return $this->getAllBiddersAsync($request)->wait();
    }

    public function getAllBiddersAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAllBiddersRequest $request)
    {
        return $this->callOperationAsync(
            'GetAllBidders',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetAllBiddersResponse'
        );
    }

    public function getApiAccessRules(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetApiAccessRulesRequest $request)
    {
        return $this->getApiAccessRulesAsync($request)->wait();
    }

    public function getApiAccessRulesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetApiAccessRulesRequest $request)
    {
        return $this->callOperationAsync(
            'GetApiAccessRules',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetApiAccessRulesResponse'
        );
    }

    public function getBestOffers(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetBestOffersRequest $request)
    {
        return $this->getBestOffersAsync($request)->wait();
    }

    public function getBestOffersAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetBestOffersRequest $request)
    {
        return $this->callOperationAsync(
            'GetBestOffers',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetBestOffersResponse'
        );
    }

    public function getBidderList(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetBidderListRequest $request)
    {
        return $this->getBidderListAsync($request)->wait();
    }

    public function getBidderListAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetBidderListRequest $request)
    {
        return $this->callOperationAsync(
            'GetBidderList',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetBidderListResponse'
        );
    }

    public function getCategories(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoriesRequest $request)
    {
        return $this->getCategoriesAsync($request)->wait();
    }

    public function getCategoriesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoriesRequest $request)
    {
        return $this->callOperationAsync(
            'GetCategories',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoriesResponse'
        );
    }

    public function getCategoryFeatures(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoryFeaturesRequest $request)
    {
        return $this->getCategoryFeaturesAsync($request)->wait();
    }

    public function getCategoryFeaturesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoryFeaturesRequest $request)
    {
        return $this->callOperationAsync(
            'GetCategoryFeatures',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoryFeaturesResponse'
        );
    }

    public function getCategoryMappings(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoryMappingsRequest $request)
    {
        return $this->getCategoryMappingsAsync($request)->wait();
    }

    public function getCategoryMappingsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoryMappingsRequest $request)
    {
        return $this->callOperationAsync(
            'GetCategoryMappings',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategoryMappingsResponse'
        );
    }

    public function getCategorySpecifics(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategorySpecificsRequest $request)
    {
        return $this->getCategorySpecificsAsync($request)->wait();
    }

    public function getCategorySpecificsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategorySpecificsRequest $request)
    {
        return $this->callOperationAsync(
            'GetCategorySpecifics',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCategorySpecificsResponse'
        );
    }

    public function getChallengeToken(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetChallengeTokenRequest $request)
    {
        return $this->getChallengeTokenAsync($request)->wait();
    }

    public function getChallengeTokenAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetChallengeTokenRequest $request)
    {
        return $this->callOperationAsync(
            'GetChallengeToken',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetChallengeTokenResponse'
        );
    }

    public function getCharities(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCharitiesRequest $request)
    {
        return $this->getCharitiesAsync($request)->wait();
    }

    public function getCharitiesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCharitiesRequest $request)
    {
        return $this->callOperationAsync(
            'GetCharities',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetCharitiesResponse'
        );
    }

    public function getClientAlertsAuthToken(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetClientAlertsAuthTokenRequest $request)
    {
        return $this->getClientAlertsAuthTokenAsync($request)->wait();
    }

    public function getClientAlertsAuthTokenAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetClientAlertsAuthTokenRequest $request)
    {
        return $this->callOperationAsync(
            'GetClientAlertsAuthToken',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetClientAlertsAuthTokenResponse'
        );
    }

    public function getContextualKeywords(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetContextualKeywordsRequest $request)
    {
        return $this->getContextualKeywordsAsync($request)->wait();
    }

    public function getContextualKeywordsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetContextualKeywordsRequest $request)
    {
        return $this->callOperationAsync(
            'GetContextualKeywords',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetContextualKeywordsResponse'
        );
    }

    public function getDescriptionTemplates(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetDescriptionTemplatesRequest $request)
    {
        return $this->getDescriptionTemplatesAsync($request)->wait();
    }

    public function getDescriptionTemplatesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetDescriptionTemplatesRequest $request)
    {
        return $this->callOperationAsync(
            'GetDescriptionTemplates',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetDescriptionTemplatesResponse'
        );
    }

    public function getDispute(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetDisputeRequest $request)
    {
        return $this->getDisputeAsync($request)->wait();
    }

    public function getDisputeAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetDisputeRequest $request)
    {
        return $this->callOperationAsync(
            'GetDispute',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetDisputeResponse'
        );
    }

    public function getFeedback(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetFeedbackRequest $request)
    {
        return $this->getFeedbackAsync($request)->wait();
    }

    public function getFeedbackAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetFeedbackRequest $request)
    {
        return $this->callOperationAsync(
            'GetFeedback',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetFeedbackResponse'
        );
    }

    public function getItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemRequest $request)
    {
        return $this->getItemAsync($request)->wait();
    }

    public function getItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemRequest $request)
    {
        return $this->callOperationAsync(
            'GetItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemResponse'
        );
    }

    public function getItemShipping(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemShippingRequest $request)
    {
        return $this->getItemShippingAsync($request)->wait();
    }

    public function getItemShippingAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemShippingRequest $request)
    {
        return $this->callOperationAsync(
            'GetItemShipping',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemShippingResponse'
        );
    }

    public function getItemTransactions(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemTransactionsRequest $request)
    {
        return $this->getItemTransactionsAsync($request)->wait();
    }

    public function getItemTransactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemTransactionsRequest $request)
    {
        return $this->callOperationAsync(
            'GetItemTransactions',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemTransactionsResponse'
        );
    }

    public function getItemsAwaitingFeedback(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemsAwaitingFeedbackRequest $request)
    {
        return $this->getItemsAwaitingFeedbackAsync($request)->wait();
    }

    public function getItemsAwaitingFeedbackAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemsAwaitingFeedbackRequest $request)
    {
        return $this->callOperationAsync(
            'GetItemsAwaitingFeedback',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetItemsAwaitingFeedbackResponse'
        );
    }

    public function getMemberMessages(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMemberMessagesRequest $request)
    {
        return $this->getMemberMessagesAsync($request)->wait();
    }

    public function getMemberMessagesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMemberMessagesRequest $request)
    {
        return $this->callOperationAsync(
            'GetMemberMessages',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMemberMessagesResponse'
        );
    }

    public function getMessagePreferences(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMessagePreferencesRequest $request)
    {
        return $this->getMessagePreferencesAsync($request)->wait();
    }

    public function getMessagePreferencesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMessagePreferencesRequest $request)
    {
        return $this->callOperationAsync(
            'GetMessagePreferences',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMessagePreferencesResponse'
        );
    }

    public function getMyMessages(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyMessagesRequest $request)
    {
        return $this->getMyMessagesAsync($request)->wait();
    }

    public function getMyMessagesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyMessagesRequest $request)
    {
        return $this->callOperationAsync(
            'GetMyMessages',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyMessagesResponse'
        );
    }

    public function getMyeBayBuying(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBayBuyingRequest $request)
    {
        return $this->getMyeBayBuyingAsync($request)->wait();
    }

    public function getMyeBayBuyingAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBayBuyingRequest $request)
    {
        return $this->callOperationAsync(
            'GetMyeBayBuying',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBayBuyingResponse'
        );
    }

    public function getMyeBayReminders(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBayRemindersRequest $request)
    {
        return $this->getMyeBayRemindersAsync($request)->wait();
    }

    public function getMyeBayRemindersAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBayRemindersRequest $request)
    {
        return $this->callOperationAsync(
            'GetMyeBayReminders',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBayRemindersResponse'
        );
    }

    public function getMyeBaySelling(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBaySellingRequest $request)
    {
        return $this->getMyeBaySellingAsync($request)->wait();
    }

    public function getMyeBaySellingAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBaySellingRequest $request)
    {
        return $this->callOperationAsync(
            'GetMyeBaySelling',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetMyeBaySellingResponse'
        );
    }

    public function getNotificationPreferences(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetNotificationPreferencesRequest $request)
    {
        return $this->getNotificationPreferencesAsync($request)->wait();
    }

    public function getNotificationPreferencesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetNotificationPreferencesRequest $request)
    {
        return $this->callOperationAsync(
            'GetNotificationPreferences',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetNotificationPreferencesResponse'
        );
    }

    public function getNotificationsUsage(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetNotificationsUsageRequest $request)
    {
        return $this->getNotificationsUsageAsync($request)->wait();
    }

    public function getNotificationsUsageAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetNotificationsUsageRequest $request)
    {
        return $this->callOperationAsync(
            'GetNotificationsUsage',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetNotificationsUsageResponse'
        );
    }

    public function getOrderTransactions(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetOrderTransactionsRequest $request)
    {
        return $this->getOrderTransactionsAsync($request)->wait();
    }

    public function getOrderTransactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetOrderTransactionsRequest $request)
    {
        return $this->callOperationAsync(
            'GetOrderTransactions',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetOrderTransactionsResponse'
        );
    }

    public function getOrders(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetOrdersRequest $request)
    {
        return $this->getOrdersAsync($request)->wait();
    }

    public function getOrdersAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetOrdersRequest $request)
    {
        return $this->callOperationAsync(
            'GetOrders',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetOrdersResponse'
        );
    }

    public function getPromotionalSaleDetails(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetPromotionalSaleDetailsRequest $request)
    {
        return $this->getPromotionalSaleDetailsAsync($request)->wait();
    }

    public function getPromotionalSaleDetailsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetPromotionalSaleDetailsRequest $request)
    {
        return $this->callOperationAsync(
            'GetPromotionalSaleDetails',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetPromotionalSaleDetailsResponse'
        );
    }

    public function getSellerDashboard(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerDashboardRequest $request)
    {
        return $this->getSellerDashboardAsync($request)->wait();
    }

    public function getSellerDashboardAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerDashboardRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellerDashboard',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerDashboardResponse'
        );
    }

    public function getSellerEvents(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerEventsRequest $request)
    {
        return $this->getSellerEventsAsync($request)->wait();
    }

    public function getSellerEventsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerEventsRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellerEvents',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerEventsResponse'
        );
    }

    public function getSellerList(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerListRequest $request)
    {
        return $this->getSellerListAsync($request)->wait();
    }

    public function getSellerListAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerListRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellerList',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerListResponse'
        );
    }

    public function getSellerTransactions(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerTransactionsRequest $request)
    {
        return $this->getSellerTransactionsAsync($request)->wait();
    }

    public function getSellerTransactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerTransactionsRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellerTransactions',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellerTransactionsResponse'
        );
    }

    public function getSellingManagerAlerts(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerAlertsRequest $request)
    {
        return $this->getSellingManagerAlertsAsync($request)->wait();
    }

    public function getSellingManagerAlertsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerAlertsRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerAlerts',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerAlertsResponse'
        );
    }

    public function getSellingManagerEmailLog(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerEmailLogRequest $request)
    {
        return $this->getSellingManagerEmailLogAsync($request)->wait();
    }

    public function getSellingManagerEmailLogAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerEmailLogRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerEmailLog',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerEmailLogResponse'
        );
    }

    public function getSellingManagerInventory(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerInventoryRequest $request)
    {
        return $this->getSellingManagerInventoryAsync($request)->wait();
    }

    public function getSellingManagerInventoryAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerInventoryRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerInventory',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerInventoryResponse'
        );
    }

    public function getSellingManagerInventoryFolder(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerInventoryFolderRequest $request)
    {
        return $this->getSellingManagerInventoryFolderAsync($request)->wait();
    }

    public function getSellingManagerInventoryFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerInventoryFolderRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerInventoryFolder',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerInventoryFolderResponse'
        );
    }

    public function getSellingManagerItemAutomationRule(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerItemAutomationRuleRequest $request)
    {
        return $this->getSellingManagerItemAutomationRuleAsync($request)->wait();
    }

    public function getSellingManagerItemAutomationRuleAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerItemAutomationRuleRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerItemAutomationRule',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerItemAutomationRuleResponse'
        );
    }

    public function getSellingManagerSaleRecord(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerSaleRecordRequest $request)
    {
        return $this->getSellingManagerSaleRecordAsync($request)->wait();
    }

    public function getSellingManagerSaleRecordAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerSaleRecordRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerSaleRecord',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerSaleRecordResponse'
        );
    }

    public function getSellingManagerSoldListings(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerSoldListingsRequest $request)
    {
        return $this->getSellingManagerSoldListingsAsync($request)->wait();
    }

    public function getSellingManagerSoldListingsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerSoldListingsRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerSoldListings',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerSoldListingsResponse'
        );
    }

    public function getSellingManagerTemplateAutomationRule(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerTemplateAutomationRuleRequest $request)
    {
        return $this->getSellingManagerTemplateAutomationRuleAsync($request)->wait();
    }

    public function getSellingManagerTemplateAutomationRuleAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerTemplateAutomationRuleRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerTemplateAutomationRule',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerTemplateAutomationRuleResponse'
        );
    }

    public function getSellingManagerTemplates(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerTemplatesRequest $request)
    {
        return $this->getSellingManagerTemplatesAsync($request)->wait();
    }

    public function getSellingManagerTemplatesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerTemplatesRequest $request)
    {
        return $this->callOperationAsync(
            'GetSellingManagerTemplates',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSellingManagerTemplatesResponse'
        );
    }

    public function getSessionID(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSessionIDRequest $request)
    {
        return $this->getSessionIDAsync($request)->wait();
    }

    public function getSessionIDAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSessionIDRequest $request)
    {
        return $this->callOperationAsync(
            'GetSessionID',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSessionIDResponse'
        );
    }

    public function getShippingDiscountProfiles(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetShippingDiscountProfilesRequest $request)
    {
        return $this->getShippingDiscountProfilesAsync($request)->wait();
    }

    public function getShippingDiscountProfilesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetShippingDiscountProfilesRequest $request)
    {
        return $this->callOperationAsync(
            'GetShippingDiscountProfiles',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetShippingDiscountProfilesResponse'
        );
    }

    public function getStore(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreRequest $request)
    {
        return $this->getStoreAsync($request)->wait();
    }

    public function getStoreAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreRequest $request)
    {
        return $this->callOperationAsync(
            'GetStore',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreResponse'
        );
    }

    public function getStoreCategoryUpdateStatus(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreCategoryUpdateStatusRequest $request)
    {
        return $this->getStoreCategoryUpdateStatusAsync($request)->wait();
    }

    public function getStoreCategoryUpdateStatusAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreCategoryUpdateStatusRequest $request)
    {
        return $this->callOperationAsync(
            'GetStoreCategoryUpdateStatus',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreCategoryUpdateStatusResponse'
        );
    }

    public function getStoreCustomPage(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreCustomPageRequest $request)
    {
        return $this->getStoreCustomPageAsync($request)->wait();
    }

    public function getStoreCustomPageAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreCustomPageRequest $request)
    {
        return $this->callOperationAsync(
            'GetStoreCustomPage',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreCustomPageResponse'
        );
    }

    public function getStoreOptions(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreOptionsRequest $request)
    {
        return $this->getStoreOptionsAsync($request)->wait();
    }

    public function getStoreOptionsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreOptionsRequest $request)
    {
        return $this->callOperationAsync(
            'GetStoreOptions',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStoreOptionsResponse'
        );
    }

    public function getStorePreferences(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStorePreferencesRequest $request)
    {
        return $this->getStorePreferencesAsync($request)->wait();
    }

    public function getStorePreferencesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStorePreferencesRequest $request)
    {
        return $this->callOperationAsync(
            'GetStorePreferences',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetStorePreferencesResponse'
        );
    }

    public function getSuggestedCategories(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSuggestedCategoriesRequest $request)
    {
        return $this->getSuggestedCategoriesAsync($request)->wait();
    }

    public function getSuggestedCategoriesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSuggestedCategoriesRequest $request)
    {
        return $this->callOperationAsync(
            'GetSuggestedCategories',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetSuggestedCategoriesResponse'
        );
    }

    public function getTaxTable(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetTaxTableRequest $request)
    {
        return $this->getTaxTableAsync($request)->wait();
    }

    public function getTaxTableAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetTaxTableRequest $request)
    {
        return $this->callOperationAsync(
            'GetTaxTable',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetTaxTableResponse'
        );
    }

    public function getTokenStatus(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetTokenStatusRequest $request)
    {
        return $this->getTokenStatusAsync($request)->wait();
    }

    public function getTokenStatusAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetTokenStatusRequest $request)
    {
        return $this->callOperationAsync(
            'GetTokenStatus',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetTokenStatusResponse'
        );
    }

    public function getUser(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserRequest $request)
    {
        return $this->getUserAsync($request)->wait();
    }

    public function getUserAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserRequest $request)
    {
        return $this->callOperationAsync(
            'GetUser',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserResponse'
        );
    }

    public function getUserContactDetails(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserContactDetailsRequest $request)
    {
        return $this->getUserContactDetailsAsync($request)->wait();
    }

    public function getUserContactDetailsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserContactDetailsRequest $request)
    {
        return $this->callOperationAsync(
            'GetUserContactDetails',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserContactDetailsResponse'
        );
    }

    public function getUserDisputes(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserDisputesRequest $request)
    {
        return $this->getUserDisputesAsync($request)->wait();
    }

    public function getUserDisputesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserDisputesRequest $request)
    {
        return $this->callOperationAsync(
            'GetUserDisputes',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserDisputesResponse'
        );
    }

    public function getUserPreferences(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserPreferencesRequest $request)
    {
        return $this->getUserPreferencesAsync($request)->wait();
    }

    public function getUserPreferencesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserPreferencesRequest $request)
    {
        return $this->callOperationAsync(
            'GetUserPreferences',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetUserPreferencesResponse'
        );
    }

    public function getVeROReasonCodeDetails(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetVeROReasonCodeDetailsRequest $request)
    {
        return $this->getVeROReasonCodeDetailsAsync($request)->wait();
    }

    public function getVeROReasonCodeDetailsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetVeROReasonCodeDetailsRequest $request)
    {
        return $this->callOperationAsync(
            'GetVeROReasonCodeDetails',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetVeROReasonCodeDetailsResponse'
        );
    }

    public function getVeROReportStatus(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetVeROReportStatusRequest $request)
    {
        return $this->getVeROReportStatusAsync($request)->wait();
    }

    public function getVeROReportStatusAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetVeROReportStatusRequest $request)
    {
        return $this->callOperationAsync(
            'GetVeROReportStatus',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GetVeROReportStatusResponse'
        );
    }

    public function geteBayDetails(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GeteBayDetailsRequest $request)
    {
        return $this->geteBayDetailsAsync($request)->wait();
    }

    public function geteBayDetailsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GeteBayDetailsRequest $request)
    {
        return $this->callOperationAsync(
            'GeteBayDetails',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GeteBayDetailsResponse'
        );
    }

    public function geteBayOfficialTime(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GeteBayOfficialTimeRequest $request)
    {
        return $this->geteBayOfficialTimeAsync($request)->wait();
    }

    public function geteBayOfficialTimeAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GeteBayOfficialTimeRequest $request)
    {
        return $this->callOperationAsync(
            'GeteBayOfficialTime',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\GeteBayOfficialTimeResponse'
        );
    }

    public function leaveFeedback(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\LeaveFeedbackRequest $request)
    {
        return $this->leaveFeedbackAsync($request)->wait();
    }

    public function leaveFeedbackAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\LeaveFeedbackRequest $request)
    {
        return $this->callOperationAsync(
            'LeaveFeedback',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\LeaveFeedbackResponse'
        );
    }

    public function moveSellingManagerInventoryFolder(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\MoveSellingManagerInventoryFolderRequest $request)
    {
        return $this->moveSellingManagerInventoryFolderAsync($request)->wait();
    }

    public function moveSellingManagerInventoryFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\MoveSellingManagerInventoryFolderRequest $request)
    {
        return $this->callOperationAsync(
            'MoveSellingManagerInventoryFolder',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\MoveSellingManagerInventoryFolderResponse'
        );
    }

    public function placeOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\PlaceOfferRequest $request)
    {
        return $this->placeOfferAsync($request)->wait();
    }

    public function placeOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\PlaceOfferRequest $request)
    {
        return $this->callOperationAsync(
            'PlaceOffer',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\PlaceOfferResponse'
        );
    }

    public function relistFixedPriceItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RelistFixedPriceItemRequest $request)
    {
        return $this->relistFixedPriceItemAsync($request)->wait();
    }

    public function relistFixedPriceItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RelistFixedPriceItemRequest $request)
    {
        return $this->callOperationAsync(
            'RelistFixedPriceItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RelistFixedPriceItemResponse'
        );
    }

    public function relistItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RelistItemRequest $request)
    {
        return $this->relistItemAsync($request)->wait();
    }

    public function relistItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RelistItemRequest $request)
    {
        return $this->callOperationAsync(
            'RelistItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RelistItemResponse'
        );
    }

    public function removeFromWatchList(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RemoveFromWatchListRequest $request)
    {
        return $this->removeFromWatchListAsync($request)->wait();
    }

    public function removeFromWatchListAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RemoveFromWatchListRequest $request)
    {
        return $this->callOperationAsync(
            'RemoveFromWatchList',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RemoveFromWatchListResponse'
        );
    }

    public function respondToBestOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RespondToBestOfferRequest $request)
    {
        return $this->respondToBestOfferAsync($request)->wait();
    }

    public function respondToBestOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RespondToBestOfferRequest $request)
    {
        return $this->callOperationAsync(
            'RespondToBestOffer',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RespondToBestOfferResponse'
        );
    }

    public function respondToFeedback(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RespondToFeedbackRequest $request)
    {
        return $this->respondToFeedbackAsync($request)->wait();
    }

    public function respondToFeedbackAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RespondToFeedbackRequest $request)
    {
        return $this->callOperationAsync(
            'RespondToFeedback',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RespondToFeedbackResponse'
        );
    }

    public function reviseCheckoutStatus(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseCheckoutStatusRequest $request)
    {
        return $this->reviseCheckoutStatusAsync($request)->wait();
    }

    public function reviseCheckoutStatusAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseCheckoutStatusRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseCheckoutStatus',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseCheckoutStatusResponse'
        );
    }

    public function reviseFixedPriceItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseFixedPriceItemRequest $request)
    {
        return $this->reviseFixedPriceItemAsync($request)->wait();
    }

    public function reviseFixedPriceItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseFixedPriceItemRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseFixedPriceItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseFixedPriceItemResponse'
        );
    }

    public function reviseInventoryStatus(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseInventoryStatusRequest $request)
    {
        return $this->reviseInventoryStatusAsync($request)->wait();
    }

    public function reviseInventoryStatusAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseInventoryStatusRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseInventoryStatus',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseInventoryStatusResponse'
        );
    }

    public function reviseItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseItemRequest $request)
    {
        return $this->reviseItemAsync($request)->wait();
    }

    public function reviseItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseItemRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseItemResponse'
        );
    }

    public function reviseMyMessages(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseMyMessagesRequest $request)
    {
        return $this->reviseMyMessagesAsync($request)->wait();
    }

    public function reviseMyMessagesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseMyMessagesRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseMyMessages',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseMyMessagesResponse'
        );
    }

    public function reviseMyMessagesFolders(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseMyMessagesFoldersRequest $request)
    {
        return $this->reviseMyMessagesFoldersAsync($request)->wait();
    }

    public function reviseMyMessagesFoldersAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseMyMessagesFoldersRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseMyMessagesFolders',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseMyMessagesFoldersResponse'
        );
    }

    public function reviseSellingManagerInventoryFolder(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerInventoryFolderRequest $request)
    {
        return $this->reviseSellingManagerInventoryFolderAsync($request)->wait();
    }

    public function reviseSellingManagerInventoryFolderAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerInventoryFolderRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseSellingManagerInventoryFolder',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerInventoryFolderResponse'
        );
    }

    public function reviseSellingManagerProduct(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerProductRequest $request)
    {
        return $this->reviseSellingManagerProductAsync($request)->wait();
    }

    public function reviseSellingManagerProductAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerProductRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseSellingManagerProduct',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerProductResponse'
        );
    }

    public function reviseSellingManagerSaleRecord(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerSaleRecordRequest $request)
    {
        return $this->reviseSellingManagerSaleRecordAsync($request)->wait();
    }

    public function reviseSellingManagerSaleRecordAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerSaleRecordRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseSellingManagerSaleRecord',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerSaleRecordResponse'
        );
    }

    public function reviseSellingManagerTemplate(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerTemplateRequest $request)
    {
        return $this->reviseSellingManagerTemplateAsync($request)->wait();
    }

    public function reviseSellingManagerTemplateAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerTemplateRequest $request)
    {
        return $this->callOperationAsync(
            'ReviseSellingManagerTemplate',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ReviseSellingManagerTemplateResponse'
        );
    }

    public function revokeToken(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RevokeTokenRequest $request)
    {
        return $this->revokeTokenAsync($request)->wait();
    }

    public function revokeTokenAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RevokeTokenRequest $request)
    {
        return $this->callOperationAsync(
            'RevokeToken',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\RevokeTokenResponse'
        );
    }

    public function saveItemToSellingManagerTemplate(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SaveItemToSellingManagerTemplateRequest $request)
    {
        return $this->saveItemToSellingManagerTemplateAsync($request)->wait();
    }

    public function saveItemToSellingManagerTemplateAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SaveItemToSellingManagerTemplateRequest $request)
    {
        return $this->callOperationAsync(
            'SaveItemToSellingManagerTemplate',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SaveItemToSellingManagerTemplateResponse'
        );
    }

    public function sellerReverseDispute(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SellerReverseDisputeRequest $request)
    {
        return $this->sellerReverseDisputeAsync($request)->wait();
    }

    public function sellerReverseDisputeAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SellerReverseDisputeRequest $request)
    {
        return $this->callOperationAsync(
            'SellerReverseDispute',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SellerReverseDisputeResponse'
        );
    }

    public function sendInvoice(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SendInvoiceRequest $request)
    {
        return $this->sendInvoiceAsync($request)->wait();
    }

    public function sendInvoiceAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SendInvoiceRequest $request)
    {
        return $this->callOperationAsync(
            'SendInvoice',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SendInvoiceResponse'
        );
    }

    public function setMessagePreferences(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetMessagePreferencesRequest $request)
    {
        return $this->setMessagePreferencesAsync($request)->wait();
    }

    public function setMessagePreferencesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetMessagePreferencesRequest $request)
    {
        return $this->callOperationAsync(
            'SetMessagePreferences',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetMessagePreferencesResponse'
        );
    }

    public function setNotificationPreferences(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetNotificationPreferencesRequest $request)
    {
        return $this->setNotificationPreferencesAsync($request)->wait();
    }

    public function setNotificationPreferencesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetNotificationPreferencesRequest $request)
    {
        return $this->callOperationAsync(
            'SetNotificationPreferences',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetNotificationPreferencesResponse'
        );
    }

    public function setPromotionalSale(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetPromotionalSaleRequest $request)
    {
        return $this->setPromotionalSaleAsync($request)->wait();
    }

    public function setPromotionalSaleAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetPromotionalSaleRequest $request)
    {
        return $this->callOperationAsync(
            'SetPromotionalSale',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetPromotionalSaleResponse'
        );
    }

    public function setPromotionalSaleListings(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetPromotionalSaleListingsRequest $request)
    {
        return $this->setPromotionalSaleListingsAsync($request)->wait();
    }

    public function setPromotionalSaleListingsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetPromotionalSaleListingsRequest $request)
    {
        return $this->callOperationAsync(
            'SetPromotionalSaleListings',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetPromotionalSaleListingsResponse'
        );
    }

    public function setSellingManagerFeedbackOptions(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerFeedbackOptionsRequest $request)
    {
        return $this->setSellingManagerFeedbackOptionsAsync($request)->wait();
    }

    public function setSellingManagerFeedbackOptionsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerFeedbackOptionsRequest $request)
    {
        return $this->callOperationAsync(
            'SetSellingManagerFeedbackOptions',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerFeedbackOptionsResponse'
        );
    }

    public function setSellingManagerItemAutomationRule(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerItemAutomationRuleRequest $request)
    {
        return $this->setSellingManagerItemAutomationRuleAsync($request)->wait();
    }

    public function setSellingManagerItemAutomationRuleAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerItemAutomationRuleRequest $request)
    {
        return $this->callOperationAsync(
            'SetSellingManagerItemAutomationRule',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerItemAutomationRuleResponse'
        );
    }

    public function setSellingManagerTemplateAutomationRule(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerTemplateAutomationRuleRequest $request)
    {
        return $this->setSellingManagerTemplateAutomationRuleAsync($request)->wait();
    }

    public function setSellingManagerTemplateAutomationRuleAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerTemplateAutomationRuleRequest $request)
    {
        return $this->callOperationAsync(
            'SetSellingManagerTemplateAutomationRule',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetSellingManagerTemplateAutomationRuleResponse'
        );
    }

    public function setShippingDiscountProfiles(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetShippingDiscountProfilesRequest $request)
    {
        return $this->setShippingDiscountProfilesAsync($request)->wait();
    }

    public function setShippingDiscountProfilesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetShippingDiscountProfilesRequest $request)
    {
        return $this->callOperationAsync(
            'SetShippingDiscountProfiles',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetShippingDiscountProfilesResponse'
        );
    }

    public function setStore(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreRequest $request)
    {
        return $this->setStoreAsync($request)->wait();
    }

    public function setStoreAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreRequest $request)
    {
        return $this->callOperationAsync(
            'SetStore',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreResponse'
        );
    }

    public function setStoreCategories(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreCategoriesRequest $request)
    {
        return $this->setStoreCategoriesAsync($request)->wait();
    }

    public function setStoreCategoriesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreCategoriesRequest $request)
    {
        return $this->callOperationAsync(
            'SetStoreCategories',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreCategoriesResponse'
        );
    }

    public function setStoreCustomPage(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreCustomPageRequest $request)
    {
        return $this->setStoreCustomPageAsync($request)->wait();
    }

    public function setStoreCustomPageAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreCustomPageRequest $request)
    {
        return $this->callOperationAsync(
            'SetStoreCustomPage',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStoreCustomPageResponse'
        );
    }

    public function setStorePreferences(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStorePreferencesRequest $request)
    {
        return $this->setStorePreferencesAsync($request)->wait();
    }

    public function setStorePreferencesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStorePreferencesRequest $request)
    {
        return $this->callOperationAsync(
            'SetStorePreferences',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetStorePreferencesResponse'
        );
    }

    public function setTaxTable(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetTaxTableRequest $request)
    {
        return $this->setTaxTableAsync($request)->wait();
    }

    public function setTaxTableAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetTaxTableRequest $request)
    {
        return $this->callOperationAsync(
            'SetTaxTable',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetTaxTableResponse'
        );
    }

    public function setUserNotes(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetUserNotesRequest $request)
    {
        return $this->setUserNotesAsync($request)->wait();
    }

    public function setUserNotesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetUserNotesRequest $request)
    {
        return $this->callOperationAsync(
            'SetUserNotes',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetUserNotesResponse'
        );
    }

    public function setUserPreferences(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetUserPreferencesRequest $request)
    {
        return $this->setUserPreferencesAsync($request)->wait();
    }

    public function setUserPreferencesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetUserPreferencesRequest $request)
    {
        return $this->callOperationAsync(
            'SetUserPreferences',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\SetUserPreferencesResponse'
        );
    }

    public function uploadSiteHostedPictures(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\UploadSiteHostedPicturesRequest $request)
    {
        return $this->uploadSiteHostedPicturesAsync($request)->wait();
    }

    public function uploadSiteHostedPicturesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\UploadSiteHostedPicturesRequest $request)
    {
        return $this->callOperationAsync(
            'UploadSiteHostedPictures',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\UploadSiteHostedPicturesResponse'
        );
    }

    public function validateChallengeInput(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ValidateChallengeInputRequest $request)
    {
        return $this->validateChallengeInputAsync($request)->wait();
    }

    public function validateChallengeInputAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ValidateChallengeInputRequest $request)
    {
        return $this->callOperationAsync(
            'ValidateChallengeInput',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ValidateChallengeInputResponse'
        );
    }

    public function validateTestUserRegistration(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ValidateTestUserRegistrationRequest $request)
    {
        return $this->validateTestUserRegistrationAsync($request)->wait();
    }

    public function validateTestUserRegistrationAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ValidateTestUserRegistrationRequest $request)
    {
        return $this->callOperationAsync(
            'ValidateTestUserRegistration',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\ValidateTestUserRegistrationResponse'
        );
    }

    public function veROReportItems(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VeROReportItemsRequest $request)
    {
        return $this->veROReportItemsAsync($request)->wait();
    }

    public function veROReportItemsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VeROReportItemsRequest $request)
    {
        return $this->callOperationAsync(
            'VeROReportItems',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VeROReportItemsResponse'
        );
    }

    public function verifyAddFixedPriceItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddFixedPriceItemRequest $request)
    {
        return $this->verifyAddFixedPriceItemAsync($request)->wait();
    }

    public function verifyAddFixedPriceItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddFixedPriceItemRequest $request)
    {
        return $this->callOperationAsync(
            'VerifyAddFixedPriceItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddFixedPriceItemResponse'
        );
    }

    public function verifyAddItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddItemRequest $request)
    {
        return $this->verifyAddItemAsync($request)->wait();
    }

    public function verifyAddItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddItemRequest $request)
    {
        return $this->callOperationAsync(
            'VerifyAddItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddItemResponse'
        );
    }

    public function verifyAddSecondChanceItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddSecondChanceItemRequest $request)
    {
        return $this->verifyAddSecondChanceItemAsync($request)->wait();
    }

    public function verifyAddSecondChanceItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddSecondChanceItemRequest $request)
    {
        return $this->callOperationAsync(
            'VerifyAddSecondChanceItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyAddSecondChanceItemResponse'
        );
    }

    public function verifyRelistItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyRelistItemRequest $request)
    {
        return $this->verifyRelistItemAsync($request)->wait();
    }

    public function verifyRelistItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyRelistItemRequest $request)
    {
        return $this->callOperationAsync(
            'VerifyRelistItem',
            $request,
            '\Apps\Dash\Packages\System\Api\Apis\Ebay\Tradingapi\Operations\VerifyRelistItemResponse'
        );
    }
}