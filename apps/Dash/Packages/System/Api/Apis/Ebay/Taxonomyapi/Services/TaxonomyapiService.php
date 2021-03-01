<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Services;

use Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Services\TaxonomyapiBaseService;

class TaxonomyapiService extends TaxonomyapiBaseService
{
    const API_VERSION = 'v1';

    protected static $operations =
        [
        'FetchItemAspects' => [
          'method' => 'GET',
          'resource' => 'category_tree/{category_tree_id}/fetch_item_aspects',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\FetchItemAspectsRestResponse',
          'params' => [
            'category_tree_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetDefaultCategoryTreeId' => [
          'method' => 'GET',
          'resource' => 'get_default_category_tree_id',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetDefaultCategoryTreeIdRestResponse',
          'params' => [
            'marketplace_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCategoryTree' => [
          'method' => 'GET',
          'resource' => 'category_tree/{category_tree_id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategoryTreeRestResponse',
          'params' => [
            'category_tree_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCategorySubtree' => [
          'method' => 'GET',
          'resource' => 'category_tree/{category_tree_id}/get_category_subtree',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategorySubtreeRestResponse',
          'params' => [
            'category_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'category_tree_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCategorySuggestions' => [
          'method' => 'GET',
          'resource' => 'category_tree/{category_tree_id}/get_category_suggestions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategorySuggestionsRestResponse',
          'params' => [
            'category_tree_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'q' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetItemAspectsForCategory' => [
          'method' => 'GET',
          'resource' => 'category_tree/{category_tree_id}/get_item_aspects_for_category',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetItemAspectsForCategoryRestResponse',
          'params' => [
            'category_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'category_tree_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCompatibilityProperties' => [
          'method' => 'GET',
          'resource' => 'category_tree/{category_tree_id}/get_compatibility_properties',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCompatibilityPropertiesRestResponse',
          'params' => [
            'category_tree_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'category_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetCompatibilityPropertyValues' => [
          'method' => 'GET',
          'resource' => 'category_tree/{category_tree_id}/get_compatibility_property_values',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCompatibilityPropertyValuesRestResponse',
          'params' => [
            'category_tree_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'compatibility_property' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'category_id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'filter' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function fetchItemAspects(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\FetchItemAspectsRestRequest $request)
    {
        return $this->fetchItemAspectsAsync($request)->wait();
    }

    public function fetchItemAspectsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\FetchItemAspectsRestRequest $request)
    {
        return $this->callOperationAsync('FetchItemAspects', $request);
    }

    public function getDefaultCategoryTreeId(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetDefaultCategoryTreeIdRestRequest $request)
    {
        return $this->getDefaultCategoryTreeIdAsync($request)->wait();
    }

    public function getDefaultCategoryTreeIdAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetDefaultCategoryTreeIdRestRequest $request)
    {
        return $this->callOperationAsync('GetDefaultCategoryTreeId', $request);
    }

    public function getCategoryTree(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategoryTreeRestRequest $request)
    {
        return $this->getCategoryTreeAsync($request)->wait();
    }

    public function getCategoryTreeAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategoryTreeRestRequest $request)
    {
        return $this->callOperationAsync('GetCategoryTree', $request);
    }

    public function getCategorySubtree(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategorySubtreeRestRequest $request)
    {
        return $this->getCategorySubtreeAsync($request)->wait();
    }

    public function getCategorySubtreeAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategorySubtreeRestRequest $request)
    {
        return $this->callOperationAsync('GetCategorySubtree', $request);
    }

    public function getCategorySuggestions(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategorySuggestionsRestRequest $request)
    {
        return $this->getCategorySuggestionsAsync($request)->wait();
    }

    public function getCategorySuggestionsAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategorySuggestionsRestRequest $request)
    {
        return $this->callOperationAsync('GetCategorySuggestions', $request);
    }

    public function getItemAspectsForCategory(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetItemAspectsForCategoryRestRequest $request)
    {
        return $this->getItemAspectsForCategoryAsync($request)->wait();
    }

    public function getItemAspectsForCategoryAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetItemAspectsForCategoryRestRequest $request)
    {
        return $this->callOperationAsync('GetItemAspectsForCategory', $request);
    }

    public function getCompatibilityProperties(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCompatibilityPropertiesRestRequest $request)
    {
        return $this->getCompatibilityPropertiesAsync($request)->wait();
    }

    public function getCompatibilityPropertiesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCompatibilityPropertiesRestRequest $request)
    {
        return $this->callOperationAsync('GetCompatibilityProperties', $request);
    }

    public function getCompatibilityPropertyValues(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCompatibilityPropertyValuesRestRequest $request)
    {
        return $this->getCompatibilityPropertyValuesAsync($request)->wait();
    }

    public function getCompatibilityPropertyValuesAsync(\Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCompatibilityPropertyValuesRestRequest $request)
    {
        return $this->callOperationAsync('GetCompatibilityPropertyValues', $request);
    }
}