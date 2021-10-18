<?php

namespace Apps\Dash\Packages\Business\Locations\Model;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;

class BusinessLocations extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $name;

    public $entity_id;

    public $description;

    public $inbound_shipping;

    public $delivery_instructions;

    public $outbound_shipping;

    public $can_stock;

    public $total_stock_qty;

    public $total_employees;

    public $employee_ids;

    public function initialize()
    {
        $this->modelRelations['address']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesAddressBook::class,
            'package_row_id',
            [
                'alias'                 => 'address',
                'params'                => [
                    'conditions'        => 'package_name = :package_name:',
                    'bind'              => [
                        'package_name'  => 'locations'
                    ]
                ]
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        if (count($this->modelRelations) === 0) {
            $this->initialize();
        }

        return $this->modelRelations;
    }
}