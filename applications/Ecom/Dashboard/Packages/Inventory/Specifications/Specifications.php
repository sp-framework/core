<?php

namespace Applications\Ecom\Dashboard\Packages\Inventory\Specifications;

use Applications\Ecom\Dashboard\Packages\Inventory\Specifications\Model\Specifications as SpecificationsModel;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class Specifications extends BasePackage
{
    protected $modelToUse = SpecificationsModel::class;

    protected $packageName = 'specifications';

    public $specifications;

    public function addSpecification(array $data)
    {
        if (!ctype_alnum(trim(str_replace(' ', '' , $data['name'])))) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Category name cannot special characters';

            return false;

        } else {

            if ($this->checkDuplicate($data)) {
                return false;
            }

            if ($this->add($data)) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Added ' . $data['name'] . ' specification group';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error adding new specification group.';
            }
        }
    }

    public function updateSpecification(array $data)
    {
        if (!ctype_alnum(trim(str_replace(' ', '' , $data['name'])))) {

            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Category name cannot special characters';

            return false;

        } else {

            if ($this->checkDuplicate($data)) {
                return false;
            }

            if ($this->update($data)) {
                $this->packagesData->responseCode = 0;

                $this->packagesData->responseMessage = 'Updated ' . $data['name'] . ' specification group';
            } else {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Error updating specification group.';
            }
        }
    }

    public function removeSpecification(array $data)
    {
        $specification = $this->getById($data['id']);

        if ($specification['product_count'] && (int) $specification['product_count'] > 0) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Specification is assigned to ' . $specification['product_count'] . ' products. Error removing specification.';

            return false;
        }

        if ($specification['is_group'] == 1) {
            $childs = $this->getByParams(
                [
                    'conditions'    => 'group_id = :gid:',
                    'bind'          =>
                        [
                            'gid'        => $specification['id']
                        ]
                ]
            );

            if (count($childs) > 0) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = 'Specification is a group and other specifications are assigned to it. Error removing specification group.';

                return false;
            }
        }

        if ($this->remove($data['id'])) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Removed specification group.';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error removing specification group.';
        }
    }

    public function addProductCount(int $id)
    {
        $specification = $this->getById($id);

        if ($specification['product_count'] && $specification['product_count'] != '') {
            $specification['product_count'] = (int) $specification['product_count'] + 1;
        } else {
            $specification['product_count'] = 1;
        }

        $this->update($specification);
    }

    public function removeProductCount(int $id)
    {
        $specification = $this->getById($id);

        if ($specification['product_count'] && $specification['product_count'] != '') {
            $specification['product_count'] = (int) $specification['product_count'] - 1;
        } else {
            $specification['product_count'] = 0;
        }

        $this->update($specification);
    }

    protected function checkDuplicate(array $data)
    {
        //if specification is group - search for duplicate name.
        if ($data['is_group'] == 1) {
            if ($this->checkDuplicateName($data)) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $data['name'] . ' specification group already exists.';

                return true;
            }
        } else {
            if ($this->checkDuplicateNameAndGroupId($data)) {
                $this->packagesData->responseCode = 1;

                $this->packagesData->responseMessage = $data['name'] . ' specification for this group already exists.';

                return true;
            }
        }

        return false;
        //if specification is not group - Search for duplicate name with groupID
    }

    protected function checkDuplicateName(array $data)
    {
        $params =
            [
                'conditions'    => 'id != :id: AND is_group = :is_group: AND name = :name:',
                'bind'          =>
                    [
                        'id'          => $data['id'],
                        'is_group'    => '1',
                        'name'        => $data['name']
                    ]
            ];

        $specifications = $this->getByParams($params);

        if ($specifications && count($specifications) > 0) {
            return true;
        }

        return false;
    }

    protected function checkDuplicateNameAndGroupId(array $data)
    {
        $params =
            [
                'conditions'    => 'id != :id: AND group_id = :group_id: AND name = :name:',
                'bind'          =>
                    [
                        'id'          => $data['id'],
                        'group_id'    => $data['group_id'],
                        'name'        => $data['name']
                    ]
            ];

        $specifications = $this->getByParams($params);

        if ($specifications && count($specifications) > 0) {
            return true;
        }

        return false;
    }
}