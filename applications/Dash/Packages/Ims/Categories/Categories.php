<?php

namespace Applications\Dash\Packages\Ims\Categories;

use Applications\Dash\Packages\Ims\Categories\Model\Categories as CategoriesModel;
use Phalcon\Helper\Json;
use Phalcon\Helper\Str;
use System\Base\BasePackage;

class Categories extends BasePackage
{
    protected $modelToUse = CategoriesModel::class;

    protected $packageName = 'categories';

    public $categories;

    public function addCategory(array $data)
    {
        if (!ctype_alnum(trim(str_replace(' ', '' , $data['name'])))) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage =
                'Category name cannot special characters';

            return false;

        } else {

            $data = $this->buildCategory($data);

            $data['product_count'] = 0;

            if ($this->add($data)) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' category';
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

            $data = $this->buildCategory($data);

            if ($this->update($data)) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' category';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error updating category.';
            }
        }
    }

    protected function buildCategory(array $data)
    {
        $data['visible_to_role_ids'] = Json::decode($data['visible_to_role_ids'], true);
        if (isset($data['visible_to_role_ids']['data'])) {
            $data['visible_to_role_ids'] = Json::encode($data['visible_to_role_ids']['data']);
        } else {
            $data['visible_to_role_ids'] = Json::encode($data['visible_to_role_ids']);
        }

        $data['visible_on_channel_ids'] = Json::decode($data['visible_on_channel_ids'], true);
        if (isset($data['visible_on_channel_ids']['data'])) {
            $data['visible_on_channel_ids'] = Json::encode($data['visible_on_channel_ids']['data']);
        } else {
            $data['visible_on_channel_ids'] = Json::encode($data['visible_on_channel_ids']);
        }

        $categoryId = Str::underscore(strtolower($data['name']));

        $newCategory[$categoryId] = [
            'title' => $data['name'],
            'link'  => '/q/category/' . $categoryId
        ];

        if ($data['parent'] != 0) {
            $parent = $this->getById($data['parent']);

            $parentCategoryId = Str::underscore(strtolower($parent['name']));
            $parentCategory = Json::decode($parent['category'], true);

            $this->traverseCategoriesArray($parentCategoryId, $parentCategory, $newCategory);

            $parent['has_childs'] = 1;

            $this->update($parent);

            $data['category'] = Json::encode($parentCategory);
        } else {
            $data['category'] = Json::encode($newCategory);
        }

        return $data;
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

            $this->packagesData->responseMessage = 'Category has products assgined to it. Error removing category.';

            return;
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed category';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing category.';
        }
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
}