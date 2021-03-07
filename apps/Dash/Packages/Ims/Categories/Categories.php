<?php

namespace Apps\Dash\Packages\Ims\Categories;

use Apps\Dash\Packages\Ims\Categories\Model\ImsCategories;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use System\Base\BasePackage;

class Categories extends BasePackage
{
    protected $modelToUse = ImsCategories::class;

    protected $packageName = 'categories';

    public $categories;

    const MAX_HIERARCHY_LEVEL = 5;

    public function addCategory(array $data)
    {
        if (!ctype_alnum(trim(str_replace(' ', '' , $data['name'])))) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Category name cannot special characters';

            return false;

        } else {
            if (!isset($data['parent_id'])) {
                $data['parent_id'] = 0;
            }

            $data['product_count'] = 0;

            if ($this->add($data)) {
                $newCategory = $this->packagesData->last;

                $newCategory = $this->buildHierarchy($newCategory);

                $newCategory['hierarchy_str'] = $this->buildHierarchyStr($newCategory['hierarchy']);

                $newCategory['hierarchy'] = Json::encode($newCategory['hierarchy']);

                $this->update($newCategory);

                $this->basepackages->storages->changeOrphanStatus($newCategory['image']);

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Added ' . $newCategory['name'] . ' category';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error adding new category.';
            }
        }
    }

    public function updateCategory(array $data)
    {
        if (!ctype_alnum(trim(str_replace(' ', '' , $data['name'])))) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Category name cannot special characters';

            return false;

        } else {
            if ($data['parent_id'] == $data['id']) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Parent cannot be self!';

                return;
            }

            $category = $this->getById($data['id']);

            $category = array_merge($category, $data);

            $category = $this->buildHierarchy($category);

            $category['hierarchy_str'] = $this->buildHierarchyStr($category['hierarchy']);

            $category['hierarchy'] = Json::encode($category['hierarchy']);

            if ($this->update($category)) {
                $this->basepackages->storages->changeOrphanStatus($data['image'], $category['image']);

                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' category';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error updating category.';
            }
        }
    }

    public function removeCategory(array $data)
    {
        $category = $this->getById($data['id']);

        if ($category['has_childs'] == 1) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Category has childs. Error removing category.';

            return;
        }

        if ($category['product_count'] || $category['product_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Category has products assigned to it. Error removing category.';

            return;
        }

        if ($this->remove($data['id'])) {
            $this->basepackages->storages->changeOrphanStatus(null, $category['image']);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed category';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing category.';
        }
    }

    protected function buildHierarchy(array $data)
    {
        if (isset($data['visible_to_role_ids'])) {
            $data['visible_to_role_ids'] = Json::decode($data['visible_to_role_ids'], true);
            if (isset($data['visible_to_role_ids']['data'])) {
                $data['visible_to_role_ids'] = Json::encode($data['visible_to_role_ids']['data']);
            } else {
                $data['visible_to_role_ids'] = Json::encode($data['visible_to_role_ids']);
            }
        } else {
            $data['visible_to_role_ids'] = Json::encode([]);
        }

        if (isset($data['visible_on_channel_ids'])) {
            $data['visible_on_channel_ids'] = Json::decode($data['visible_on_channel_ids'], true);
            if (isset($data['visible_on_channel_ids']['data'])) {
                $data['visible_on_channel_ids'] = Json::encode($data['visible_on_channel_ids']['data']);
            } else {
                $data['visible_on_channel_ids'] = Json::encode($data['visible_on_channel_ids']);
            }
        } else {
            $data['visible_on_channel_ids'] = Json::encode([]);
        }

        $categoryId = Str::underscore(strtolower($data['name']));

        $newCategory[$categoryId] = [
            'title' => $data['name'],
            'link'  => '/q/category/' . $data['id']
        ];

        if (isset($data['parent_id']) &&
            $data['parent_id'] != 0
        ) {
            $parent = $this->getById($data['parent_id']);

            $parentCategoryId = Str::underscore(strtolower($parent['name']));
            $parentCategory = Json::decode($parent['hierarchy'], true);

            $this->traverseCategoriesArray($parentCategoryId, $parentCategory, $newCategory);

            $data['hierarchy'] = $parentCategory;
            $data['hierarchy_level'] = (int) $parent['hierarchy_level'] + 1;
        } else {
            $data['hierarchy'] = $newCategory;
            $data['hierarchy_level'] = 1;
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

    public function searchCategories(string $categoryQueryString)
    {
        $searchCategories =
            $this->getByParams(
                [
                    'conditions'    => 'name LIKE :cName: AND hierarchy_level < :cHierarchyLevel:',
                    'bind'          => [
                        'cName'                 => '%' . $categoryQueryString . '%',
                        'cHierarchyLevel'       => self::MAX_HIERARCHY_LEVEL
                    ]
                ]
            );

        if (count($searchCategories) > 0) {
            $categories = [];

            foreach ($searchCategories as $employeeKey => $employeeValue) {
                $categories[$employeeKey]['id'] = $employeeValue['id'];
                $categories[$employeeKey]['hierarchy_str'] = $employeeValue['hierarchy_str'];
            }

            $this->packagesData->responseCode = 0;

            $this->packagesData->categories = $categories;

            return true;
        }
    }
}