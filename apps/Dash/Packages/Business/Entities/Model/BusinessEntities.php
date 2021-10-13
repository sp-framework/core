<?php

namespace Apps\Dash\Packages\Business\Entities\Model;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;

class BusinessEntities extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $logo;

    public $abn;

    public $business_name;

    public $entity_type;

    public $api_id;

    public $contact_phone;

    public $contact_phone_ext;

    public $contact_fax;

    public $contact_other;

    public $website;

    public $email;

    public $acn;

    public $tfn;

    public $currency;

    public $bsb;

    public $account_number;

    public $swift_code;

    public $accountant_vendor_id;

    public $settings;

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
                        'package_name'  => 'entities'
                    ]
                ]
            ]
        );

        parent::initialize();
    }

    public function getModelRelations()
    {
        return $this->modelRelations;
    }
}