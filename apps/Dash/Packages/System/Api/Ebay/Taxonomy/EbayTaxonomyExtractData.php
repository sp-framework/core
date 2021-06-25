<?php

namespace Apps\Dash\Packages\System\Api\Ebay\Taxonomy;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetCategoryTreeRestRequest;
use Apps\Dash\Packages\System\Api\Apis\Ebay\Taxonomyapi\Operations\GetDefaultCategoryTreeIdRestRequest;
use Apps\Dash\Packages\System\Api\Ebay\Taxonomy\EbayTaxonomy;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use System\Base\BasePackage;

class EbayTaxonomyExtractData extends BasePackage
{
    protected $apiId;

    protected $taxonomyPackage;

    protected $taxonomyService;

    protected $taxonomies;

    protected $dataPath = 'apps/Dash/Packages/System/Api/Ebay/Taxonomy/Data/';

    protected $taxonomyVersion;

    protected $versionFile;

    protected $versionFilePath = 'apps/Dash/Packages/System/Api/Ebay/Taxonomy/Data/Version.json';

    protected $taxonomyFilePath = 'apps/Dash/Packages/System/Api/Ebay/Taxonomy/Data/Taxonomy.json';

    public function extractData($apiId)
    {
        $this->apiId = $apiId;

        try {
            $this->versionFile = $this->localContent->read($this->versionFilePath);
        } catch (\Exception $e) {
            $this->versionFile = false;
        }

        $this->initApi();

        if (!$this->versionFile) {

            $this->fetchTaxonomies();

            $this->createRootTaxonomyFilters();

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Downloaded latest taxonomy data from eBay';
        } else {
            if ($this->needsUpdate()) {

                $this->fetchTaxonomies(true);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Taxonomy up to date!';
        }
    }

    protected function initApi()
    {
        $apiPackage = $this->usePackage(Api::class);

        $api = $apiPackage->useApi(['api_id' => $this->apiId]);

        $this->taxonomyService = $api->useService('Taxonomyapi');

        $this->taxonomyPackage = $this->usePackage(EbayTaxonomy::class);
    }

    protected function fetchTaxonomies($update = false)
    {
        $this->getDefaultTaxonomyTree();

        $version = $this->writeVersionFile();

        $this->getTaxonomies();

        $this->writeTaxonomyFile();

        $this->writeRootTaxonomyFiles($update, $version);
    }

    protected function needsUpdate()
    {
        $this->getDefaultTaxonomyTree();

        $versionFile = Json::decode($this->versionFile, true);

        if ($versionFile['categoryTreeId'] == $this->taxonomies['categoryTreeId'] &&
            $versionFile['categoryTreeVersion'] != $this->taxonomies['categoryTreeVersion']
        ) {
            return true;
        }

        return false;
    }

    protected function getDefaultTaxonomyTree()
    {
        $defaultTaxonomyTreeRequest = new GetDefaultCategoryTreeIdRestRequest;

        $defaultTaxonomyTreeRequest->marketplace_id = $this->taxonomyService->getConfig()['marketplace_id'];

        $defaultTaxonomyTreeResponse = $this->taxonomyService->getDefaultCategoryTreeId($defaultTaxonomyTreeRequest);

        $this->taxonomies = $defaultTaxonomyTreeResponse->toArray();

        $this->taxonomyVersion = $this->taxonomies['categoryTreeVersion'];
    }

    protected function getTaxonomies()
    {
        $taxonomyTreeRequest = new GetCategoryTreeRestRequest;

        $taxonomyTreeRequest->category_tree_id = $this->taxonomies['categoryTreeId'];

        $categoryTreeResponse = $this->taxonomyService->getCategoryTree($taxonomyTreeRequest);

        $this->taxonomies = $categoryTreeResponse->toArray();
    }

    protected function writeVersionFile()
    {
        $taxonomy = [];
        $taxonomy['categoryTreeId'] = $this->taxonomies['categoryTreeId'];
        $taxonomy['categoryTreeVersion'] = $this->taxonomies['categoryTreeVersion'];

        $this->localContent->write($this->versionFilePath, Json::encode($taxonomy));

        return $taxonomy;
    }

    protected function writeTaxonomyFile()
    {
        $this->localContent->write($this->taxonomyFilePath, Json::encode($this->taxonomies));
    }

    protected function writeRootTaxonomyFiles($update, $version)
    {
        foreach ($this->taxonomies['rootCategoryNode']['childCategoryTreeNodes'] as $rootCategoryKey => $rootCategory) {
            $rootCategory['category'] = array_merge($rootCategory['category'], $version);

            $this->localContent->write(
                $this->dataPath . $rootCategory['category']['categoryId'] . '.json',
                Json::encode($rootCategory)
            );

            if (isset($rootCategory['childCategoryTreeNodes']) &&
                count($rootCategory['childCategoryTreeNodes']) > 0
            ) {
                $hasChilds = true;
            } else {
                $hasChilds = false;
            }

            if ($update) {
                $this->updateRootTaxonomy(
                    $rootCategory['category']['categoryId'],
                    $rootCategory['category']['categoryName'],
                    $hasChilds
                );
            } else {
                $this->registerRootTaxonomy(
                    $rootCategory['category']['categoryId'],
                    $rootCategory['category']['categoryName'],
                    $hasChilds
                );
            }
        }
    }

    protected function registerRootTaxonomy($id, $name, $hasChilds)
    {
        $this->taxonomyPackage->addTaxonomy($this->dataArr($id, $name, $hasChilds));
    }

    protected function updateRootTaxonomy($id, $name, $hasChilds)
    {
        $this->taxonomyPackage->updateTaxonomy($this->dataArr($id, $name, $hasChilds));
    }

    protected function dataArr($id, $name, $hasChilds)
    {
        return
            [
                'id'                => $id,
                'name'              => $name,
                'hierarchy'         => $this->getRootHierarchy($name),
                'hierarchy_str'     => $name,
                'installed'         => 0,
                'enabled'           => 0,
                'root_id'           => 0,
                'parent_id'         => 0,
                'product_count'     => 0,
                'has_childs'        => $hasChilds,
                'taxonomy_version'  => $this->taxonomyVersion
            ];
    }

    protected function getRootHierarchy($name)
    {
        $categoryId = Str::underscore(strtolower($name));

        $newCategory[$categoryId] = [
            'title' => $name
        ];

        return Json::encode($newCategory);
    }

    protected function createRootTaxonomyFilters()
    {
        $componentId =
            $this->modules->components->getRouteComponentForApp(
                'system/api/ebay/taxonomy',
                $this->apps->getAppInfo()['id']
            )['id'];

            $this->basepackages->filters->addFilter(
                [
                    'name'              =>  'Show All Root',
                    'component_id'      =>  $componentId,
                    'conditions'        =>  '-:root_id:equals:0&'
                ]
            );

            $this->basepackages->filters->addFilter(
                [
                    'name'              =>  'Show All Installed',
                    'component_id'      =>  $componentId,
                    'conditions'        =>  '-:installed:equals:1&'
                ]
            );

            $this->basepackages->filters->addFilter(
                [
                    'name'              =>  'Show All Enabled',
                    'component_id'      =>  $componentId,
                    'conditions'        =>  '-:enabled:equals:1&'
                ]
            );

        foreach ($this->taxonomies['rootCategoryNode']['childCategoryTreeNodes'] as $rootCategoryKey => $rootCategory) {
            $data =
                [
                    'name'              =>  'Show All ' . $rootCategory['category']['categoryName'],
                    'component_id'      =>  $componentId,
                    'conditions'        =>  '-:id:equals:' .
                                            $rootCategory['category']['categoryId'] .
                                            '&or:root_id:equals:' .
                                            $rootCategory['category']['categoryId'] .
                                            '&'
                ];

            $this->basepackages->filters->addFilter($data);
        }
    }
}