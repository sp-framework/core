<?php

namespace Apps\Dash\Packages\System\Api\Ebay\Taxonomy;

use Apps\Dash\Packages\System\Api\Ebay\Taxonomy\Model\SystemApiEbayTaxonomy;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use System\Base\BasePackage;

class EbayTaxonomy extends BasePackage
{
    protected $modelToUse = SystemApiEbayTaxonomy::class;

    protected $packageName = 'taxonomy';

    public $taxonomy;

    public function addTaxonomy(array $data)
    {
        if ($this->add($data)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' taxonomy';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error adding new taxonomy.';
        }
    }

    public function updateTaxonomy(array $data)
    {
        $taxonomy = $this->getById($data['id']);

        $taxonomy = array_merge($taxonomy, $data);

        if ($this->update($taxonomy)) {
            if ($taxonomy['enabled'] == 1) {
                $this->toggleSubTaxomony($taxonomy['id'], true);
            } if ($taxonomy['enabled'] == 0) {
                $this->toggleSubTaxomony($taxonomy['id']);
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' taxonomy';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error updating taxonomy.';
        }
    }

    protected function toggleSubTaxomony($rootId, $enabled = false)
    {
        if ($enabled) {
            $subTaxonomy = $this->modelsManager->executeQuery('UPDATE ' . $this->modelToUse . ' SET enabled = 1 WHERE root_id = ' . $rootId);
        } else {
            $subTaxonomy = $this->modelsManager->executeQuery('UPDATE ' . $this->modelToUse . ' SET enabled = 0 WHERE root_id = ' . $rootId);
        }

        return $subTaxonomy->success();
    }

    public function installTaxonomy(array $data)
    {
        $taxonomyData =
            Json::decode(
                $this->localContent->read(
                    'apps/Dash/Packages/System/Api/Ebay/Taxonomy/Data/' . $data['taxonomy_id'] . '.json'
                ),
                true
            );

        if ($taxonomyData['category']['categoryId'] == $data['taxonomy_id']) {
            $this->registerTaxonomy(
                $taxonomyData,
                $taxonomyData['category']['categoryId'],
                $taxonomyData['category']['categoryTreeVersion']
            );
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Provided taxonomy id and file incorrect. Extract data again.';
        }

        $taxonomy = $this->getById($data['taxonomy_id']);

        $taxonomy['installed'] = 1;

        if ($this->update($taxonomy)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Installed ' . $taxonomy['name'] . ' taxonomy';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error installing ' . $taxonomy['name'] . ' taxonomy.';
        }
    }

    protected function registerTaxonomy($taxonomyData, $rootId, $version)
    {
        foreach ($taxonomyData['childCategoryTreeNodes'] as $childKey => $child) {
            $data = $this->dataArr(
                $child['category']['categoryId'],
                $child['category']['categoryName'],
                $rootId,
                $taxonomyData['category']['categoryId'],
                $version
            );

            if (isset($child['childCategoryTreeNodes'])) {
                $data['has_childs'] = 1;
            } else {
                $data['has_childs'] = 0;
            }
            $data = $this->buildHierarchy($data);
            $data['hierarchy_str'] = $this->buildHierarchyStr($data['hierarchy']);
            $data['hierarchy'] = Json::encode($data['hierarchy']);
            $data['installed'] = 1;
            $this->addTaxonomy($data);

            if (isset($child['childCategoryTreeNodes'])) {
                $this->registerTaxonomy($child, $rootId, $version);
            }
        }
    }

    protected function dataArr($id, $name, $rootId, $parent, $version)
    {
        return
            [
                'id'                => $id,
                'name'              => $name,
                'installed'         => 0,
                'enabled'           => 0,
                'root_id'           => $rootId,
                'parent'            => $parent,
                'product_count'     => 0,
                'taxonomy_version'  => $version
            ];
    }

    protected function buildHierarchy(array $data)
    {
        $categoryId = Str::underscore(strtolower($data['name']));

        $newCategory[$categoryId] = [
            'title' => $data['name'],
        ];

        if ($data['parent'] != 0) {
            $parent = $this->getById($data['parent']);

            $parentCategoryId = Str::underscore(strtolower($parent['name']));
            $parentCategory = Json::decode($parent['hierarchy'], true);

            $this->traverseCategoriesArray($parentCategoryId, $parentCategory, $newCategory);

            $data['hierarchy'] = $parentCategory;
        } else {
            $data['hierarchy'] = $newCategory;
        }

        return $data;
    }

    protected function traverseCategoriesArray($parentCategoryId, &$parentCategory, $newCategory)
    {
        foreach($parentCategory as $key => &$value) {
            if ($key === $parentCategoryId) {
                $value["childs"] = $newCategory;
                return;
            }
            // var_dump($value);
            if ($value['childs']) {
                $this->traverseCategoriesArray($parentCategoryId, $value['childs'], $newCategory);
            }
        }
    }

    protected function buildHierarchyStr($hierarchy)
    {
        $strArr = $this->buildHierarchyStrArr($hierarchy);

        return implode(' / ', $strArr);
    }

    protected function buildHierarchyStrArr($hierarchy)
    {
        $arr = [];

        foreach ($hierarchy as $hierarchyKey => $hierarchyValue) {
            array_push($arr, $hierarchyValue['title']);

            if (isset($hierarchyValue['childs'])) {
                $arr = array_merge($arr, $this->buildHierarchyStrArr($hierarchyValue['childs']));
            }
        }

        return $arr;
    }

    public function isEnabled()
    {
        $searchEnabledTaxonomy =
            $this->getByParams(
                [
                    'conditions'    => 'enabled = :cEnabled:',
                    'bind'          => [
                        'cEnabled'  => 1
                    ]
                ]
            );

        if ($searchEnabledTaxonomy) {
            return true;
        }
    }
}