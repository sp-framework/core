<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Services\InventoryapiBaseService;

class InventoryapiService extends InventoryapiBaseService
{
    const API_VERSION = 'v1';

    protected static $operations =
        [
        'BulkCreateOrReplaceInventoryItem' => [
          'method' => 'POST',
          'resource' => 'bulk_create_or_replace_inventory_item',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkCreateOrReplaceInventoryItemRestResponse',
          'params' => [
          ],
        ],
        'BulkGetInventoryItem' => [
          'method' => 'POST',
          'resource' => 'bulk_get_inventory_item',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkGetInventoryItemRestResponse',
          'params' => [
          ],
        ],
        'BulkUpdatePriceQuantity' => [
          'method' => 'POST',
          'resource' => 'bulk_update_price_quantity',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkUpdatePriceQuantityRestResponse',
          'params' => [
          ],
        ],
        'GetInventoryItem' => [
          'method' => 'GET',
          'resource' => 'inventory_item/{sku}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemRestResponse',
          'params' => [
            'sku' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateOrReplaceInventoryItem' => [
          'method' => 'PUT',
          'resource' => 'inventory_item/{sku}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceInventoryItemRestResponse',
          'params' => [
            'sku' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteInventoryItem' => [
          'method' => 'DELETE',
          'resource' => 'inventory_item/{sku}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryItemRestResponse',
          'params' => [
            'sku' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInventoryItems' => [
          'method' => 'GET',
          'resource' => 'inventory_item',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemsRestResponse',
          'params' => [
            'limit' => [
              'valid' => [
                'string',
              ],
            ],
            'offset' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'GetProductCompatibility' => [
          'method' => 'GET',
          'resource' => 'inventory_item/{sku}/product_compatibility',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetProductCompatibilityRestResponse',
          'params' => [
            'sku' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateOrReplaceProductCompatibility' => [
          'method' => 'PUT',
          'resource' => 'inventory_item/{sku}/product_compatibility',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceProductCompatibilityRestResponse',
          'params' => [
            'sku' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteProductCompatibility' => [
          'method' => 'DELETE',
          'resource' => 'inventory_item/{sku}/product_compatibility',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteProductCompatibilityRestResponse',
          'params' => [
            'sku' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInventoryItemGroup' => [
          'method' => 'GET',
          'resource' => 'inventory_item_group/{inventoryItemGroupKey}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemGroupRestResponse',
          'params' => [
            'inventoryItemGroupKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateOrReplaceInventoryItemGroup' => [
          'method' => 'PUT',
          'resource' => 'inventory_item_group/{inventoryItemGroupKey}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceInventoryItemGroupRestResponse',
          'params' => [
            'inventoryItemGroupKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteInventoryItemGroup' => [
          'method' => 'DELETE',
          'resource' => 'inventory_item_group/{inventoryItemGroupKey}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryItemGroupRestResponse',
          'params' => [
            'inventoryItemGroupKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'BulkMigrateListing' => [
          'method' => 'POST',
          'resource' => 'bulk_migrate_listing',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkMigrateListingRestResponse',
          'params' => [
          ],
        ],
        'BulkCreateOffer' => [
          'method' => 'POST',
          'resource' => 'bulk_create_offer',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkCreateOfferRestResponse',
          'params' => [
          ],
        ],
        'BulkPublishOffer' => [
          'method' => 'POST',
          'resource' => 'bulk_publish_offer',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkPublishOfferRestResponse',
          'params' => [
          ],
        ],
        'GetOffers' => [
          'method' => 'GET',
          'resource' => 'offer',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetOffersRestResponse',
          'params' => [
            'format' => [
              'valid' => [
                'string',
              ],
            ],
            'limit' => [
              'valid' => [
                'string',
              ],
            ],
            'marketplace_id' => [
              'valid' => [
                'string',
              ],
            ],
            'offset' => [
              'valid' => [
                'string',
              ],
            ],
            'sku' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateOffer' => [
          'method' => 'POST',
          'resource' => 'offer',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOfferRestResponse',
          'params' => [
          ],
        ],
        'GetOffer' => [
          'method' => 'GET',
          'resource' => 'offer/{offerId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetOfferRestResponse',
          'params' => [
            'offerId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UpdateOffer' => [
          'method' => 'PUT',
          'resource' => 'offer/{offerId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\UpdateOfferRestResponse',
          'params' => [
            'offerId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteOffer' => [
          'method' => 'DELETE',
          'resource' => 'offer/{offerId}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteOfferRestResponse',
          'params' => [
            'offerId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetListingFees' => [
          'method' => 'POST',
          'resource' => 'offer/get_listing_fees',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetListingFeesRestResponse',
          'params' => [
          ],
        ],
        'PublishOffer' => [
          'method' => 'POST',
          'resource' => 'offer/{offerId}/publish/',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\PublishOfferRestResponse',
          'params' => [
            'offerId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'PublishOfferByInventoryItemGroup' => [
          'method' => 'POST',
          'resource' => 'offer/publish_by_inventory_item_group/',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\PublishOfferByInventoryItemGroupRestResponse',
          'params' => [
          ],
        ],
        'WithdrawOffer' => [
          'method' => 'POST',
          'resource' => 'offer/{offerId}/withdraw',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\WithdrawOfferRestResponse',
          'params' => [
            'offerId' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'WithdrawOfferByInventoryItemGroup' => [
          'method' => 'POST',
          'resource' => 'offer/withdraw_by_inventory_item_group',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\WithdrawOfferByInventoryItemGroupRestResponse',
          'params' => [
          ],
        ],
        'GetInventoryLocation' => [
          'method' => 'GET',
          'resource' => 'location/{merchantLocationKey}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryLocationRestResponse',
          'params' => [
            'merchantLocationKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'CreateInventoryLocation' => [
          'method' => 'POST',
          'resource' => 'location/{merchantLocationKey}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateInventoryLocationRestResponse',
          'params' => [
            'merchantLocationKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DeleteInventoryLocation' => [
          'method' => 'DELETE',
          'resource' => 'location/{merchantLocationKey}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryLocationRestResponse',
          'params' => [
            'merchantLocationKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'DisableInventoryLocation' => [
          'method' => 'POST',
          'resource' => 'location/{merchantLocationKey}/disable',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DisableInventoryLocationRestResponse',
          'params' => [
            'merchantLocationKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'EnableInventoryLocation' => [
          'method' => 'POST',
          'resource' => 'location/{merchantLocationKey}/enable',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\EnableInventoryLocationRestResponse',
          'params' => [
            'merchantLocationKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetInventoryLocations' => [
          'method' => 'GET',
          'resource' => 'location',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryLocationsRestResponse',
          'params' => [
            'limit' => [
              'valid' => [
                'string',
              ],
            ],
            'offset' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'UpdateInventoryLocation' => [
          'method' => 'POST',
          'resource' => 'location/{merchantLocationKey}/update_location_details',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\UpdateInventoryLocationRestResponse',
          'params' => [
            'merchantLocationKey' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function bulkCreateOrReplaceInventoryItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkCreateOrReplaceInventoryItemRestRequest $request)
    {
        return $this->bulkCreateOrReplaceInventoryItemAsync($request)->wait();
    }

    public function bulkCreateOrReplaceInventoryItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkCreateOrReplaceInventoryItemRestRequest $request)
    {
        return $this->callOperationAsync('BulkCreateOrReplaceInventoryItem', $request);
    }

    public function bulkGetInventoryItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkGetInventoryItemRestRequest $request)
    {
        return $this->bulkGetInventoryItemAsync($request)->wait();
    }

    public function bulkGetInventoryItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkGetInventoryItemRestRequest $request)
    {
        return $this->callOperationAsync('BulkGetInventoryItem', $request);
    }

    public function bulkUpdatePriceQuantity(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkUpdatePriceQuantityRestRequest $request)
    {
        return $this->bulkUpdatePriceQuantityAsync($request)->wait();
    }

    public function bulkUpdatePriceQuantityAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkUpdatePriceQuantityRestRequest $request)
    {
        return $this->callOperationAsync('BulkUpdatePriceQuantity', $request);
    }

    public function getInventoryItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemRestRequest $request)
    {
        return $this->getInventoryItemAsync($request)->wait();
    }

    public function getInventoryItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemRestRequest $request)
    {
        return $this->callOperationAsync('GetInventoryItem', $request);
    }

    public function createOrReplaceInventoryItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceInventoryItemRestRequest $request)
    {
        return $this->createOrReplaceInventoryItemAsync($request)->wait();
    }

    public function createOrReplaceInventoryItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceInventoryItemRestRequest $request)
    {
        return $this->callOperationAsync('CreateOrReplaceInventoryItem', $request);
    }

    public function deleteInventoryItem(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryItemRestRequest $request)
    {
        return $this->deleteInventoryItemAsync($request)->wait();
    }

    public function deleteInventoryItemAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryItemRestRequest $request)
    {
        return $this->callOperationAsync('DeleteInventoryItem', $request);
    }

    public function getInventoryItems(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemsRestRequest $request)
    {
        return $this->getInventoryItemsAsync($request)->wait();
    }

    public function getInventoryItemsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemsRestRequest $request)
    {
        return $this->callOperationAsync('GetInventoryItems', $request);
    }

    public function getProductCompatibility(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetProductCompatibilityRestRequest $request)
    {
        return $this->getProductCompatibilityAsync($request)->wait();
    }

    public function getProductCompatibilityAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetProductCompatibilityRestRequest $request)
    {
        return $this->callOperationAsync('GetProductCompatibility', $request);
    }

    public function createOrReplaceProductCompatibility(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceProductCompatibilityRestRequest $request)
    {
        return $this->createOrReplaceProductCompatibilityAsync($request)->wait();
    }

    public function createOrReplaceProductCompatibilityAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceProductCompatibilityRestRequest $request)
    {
        return $this->callOperationAsync('CreateOrReplaceProductCompatibility', $request);
    }

    public function deleteProductCompatibility(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteProductCompatibilityRestRequest $request)
    {
        return $this->deleteProductCompatibilityAsync($request)->wait();
    }

    public function deleteProductCompatibilityAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteProductCompatibilityRestRequest $request)
    {
        return $this->callOperationAsync('DeleteProductCompatibility', $request);
    }

    public function getInventoryItemGroup(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemGroupRestRequest $request)
    {
        return $this->getInventoryItemGroupAsync($request)->wait();
    }

    public function getInventoryItemGroupAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryItemGroupRestRequest $request)
    {
        return $this->callOperationAsync('GetInventoryItemGroup', $request);
    }

    public function createOrReplaceInventoryItemGroup(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceInventoryItemGroupRestRequest $request)
    {
        return $this->createOrReplaceInventoryItemGroupAsync($request)->wait();
    }

    public function createOrReplaceInventoryItemGroupAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOrReplaceInventoryItemGroupRestRequest $request)
    {
        return $this->callOperationAsync('CreateOrReplaceInventoryItemGroup', $request);
    }

    public function deleteInventoryItemGroup(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryItemGroupRestRequest $request)
    {
        return $this->deleteInventoryItemGroupAsync($request)->wait();
    }

    public function deleteInventoryItemGroupAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryItemGroupRestRequest $request)
    {
        return $this->callOperationAsync('DeleteInventoryItemGroup', $request);
    }

    public function bulkMigrateListing(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkMigrateListingRestRequest $request)
    {
        return $this->bulkMigrateListingAsync($request)->wait();
    }

    public function bulkMigrateListingAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkMigrateListingRestRequest $request)
    {
        return $this->callOperationAsync('BulkMigrateListing', $request);
    }

    public function bulkCreateOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkCreateOfferRestRequest $request)
    {
        return $this->bulkCreateOfferAsync($request)->wait();
    }

    public function bulkCreateOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkCreateOfferRestRequest $request)
    {
        return $this->callOperationAsync('BulkCreateOffer', $request);
    }

    public function bulkPublishOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkPublishOfferRestRequest $request)
    {
        return $this->bulkPublishOfferAsync($request)->wait();
    }

    public function bulkPublishOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\BulkPublishOfferRestRequest $request)
    {
        return $this->callOperationAsync('BulkPublishOffer', $request);
    }

    public function getOffers(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetOffersRestRequest $request)
    {
        return $this->getOffersAsync($request)->wait();
    }

    public function getOffersAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetOffersRestRequest $request)
    {
        return $this->callOperationAsync('GetOffers', $request);
    }

    public function createOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOfferRestRequest $request)
    {
        return $this->createOfferAsync($request)->wait();
    }

    public function createOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateOfferRestRequest $request)
    {
        return $this->callOperationAsync('CreateOffer', $request);
    }

    public function getOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetOfferRestRequest $request)
    {
        return $this->getOfferAsync($request)->wait();
    }

    public function getOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetOfferRestRequest $request)
    {
        return $this->callOperationAsync('GetOffer', $request);
    }

    public function updateOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\UpdateOfferRestRequest $request)
    {
        return $this->updateOfferAsync($request)->wait();
    }

    public function updateOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\UpdateOfferRestRequest $request)
    {
        return $this->callOperationAsync('UpdateOffer', $request);
    }

    public function deleteOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteOfferRestRequest $request)
    {
        return $this->deleteOfferAsync($request)->wait();
    }

    public function deleteOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteOfferRestRequest $request)
    {
        return $this->callOperationAsync('DeleteOffer', $request);
    }

    public function getListingFees(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetListingFeesRestRequest $request)
    {
        return $this->getListingFeesAsync($request)->wait();
    }

    public function getListingFeesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetListingFeesRestRequest $request)
    {
        return $this->callOperationAsync('GetListingFees', $request);
    }

    public function publishOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\PublishOfferRestRequest $request)
    {
        return $this->publishOfferAsync($request)->wait();
    }

    public function publishOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\PublishOfferRestRequest $request)
    {
        return $this->callOperationAsync('PublishOffer', $request);
    }

    public function publishOfferByInventoryItemGroup(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\PublishOfferByInventoryItemGroupRestRequest $request)
    {
        return $this->publishOfferByInventoryItemGroupAsync($request)->wait();
    }

    public function publishOfferByInventoryItemGroupAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\PublishOfferByInventoryItemGroupRestRequest $request)
    {
        return $this->callOperationAsync('PublishOfferByInventoryItemGroup', $request);
    }

    public function withdrawOffer(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\WithdrawOfferRestRequest $request)
    {
        return $this->withdrawOfferAsync($request)->wait();
    }

    public function withdrawOfferAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\WithdrawOfferRestRequest $request)
    {
        return $this->callOperationAsync('WithdrawOffer', $request);
    }

    public function withdrawOfferByInventoryItemGroup(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\WithdrawOfferByInventoryItemGroupRestRequest $request)
    {
        return $this->withdrawOfferByInventoryItemGroupAsync($request)->wait();
    }

    public function withdrawOfferByInventoryItemGroupAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\WithdrawOfferByInventoryItemGroupRestRequest $request)
    {
        return $this->callOperationAsync('WithdrawOfferByInventoryItemGroup', $request);
    }

    public function getInventoryLocation(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryLocationRestRequest $request)
    {
        return $this->getInventoryLocationAsync($request)->wait();
    }

    public function getInventoryLocationAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryLocationRestRequest $request)
    {
        return $this->callOperationAsync('GetInventoryLocation', $request);
    }

    public function createInventoryLocation(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateInventoryLocationRestRequest $request)
    {
        return $this->createInventoryLocationAsync($request)->wait();
    }

    public function createInventoryLocationAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\CreateInventoryLocationRestRequest $request)
    {
        return $this->callOperationAsync('CreateInventoryLocation', $request);
    }

    public function deleteInventoryLocation(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryLocationRestRequest $request)
    {
        return $this->deleteInventoryLocationAsync($request)->wait();
    }

    public function deleteInventoryLocationAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DeleteInventoryLocationRestRequest $request)
    {
        return $this->callOperationAsync('DeleteInventoryLocation', $request);
    }

    public function disableInventoryLocation(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DisableInventoryLocationRestRequest $request)
    {
        return $this->disableInventoryLocationAsync($request)->wait();
    }

    public function disableInventoryLocationAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\DisableInventoryLocationRestRequest $request)
    {
        return $this->callOperationAsync('DisableInventoryLocation', $request);
    }

    public function enableInventoryLocation(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\EnableInventoryLocationRestRequest $request)
    {
        return $this->enableInventoryLocationAsync($request)->wait();
    }

    public function enableInventoryLocationAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\EnableInventoryLocationRestRequest $request)
    {
        return $this->callOperationAsync('EnableInventoryLocation', $request);
    }

    public function getInventoryLocations(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryLocationsRestRequest $request)
    {
        return $this->getInventoryLocationsAsync($request)->wait();
    }

    public function getInventoryLocationsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\GetInventoryLocationsRestRequest $request)
    {
        return $this->callOperationAsync('GetInventoryLocations', $request);
    }

    public function updateInventoryLocation(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\UpdateInventoryLocationRestRequest $request)
    {
        return $this->updateInventoryLocationAsync($request)->wait();
    }

    public function updateInventoryLocationAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Inventoryapi\Operations\UpdateInventoryLocationRestRequest $request)
    {
        return $this->callOperationAsync('UpdateInventoryLocation', $request);
    }
}