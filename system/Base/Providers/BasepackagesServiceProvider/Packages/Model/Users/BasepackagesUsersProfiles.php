<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users;

use System\Base\BaseModel;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesAddressBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesContactBook;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\BasepackagesUsersAccounts;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Users\Profiles;

class BasepackagesUsersProfiles extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $account_id;

    public $locale_country_id;

    public $locale_timezone_id;

    public $settings;

    public function initialize()
    {
        $this->modelRelations['account']['relationObj'] = $this->belongsTo(
            'account_id',
            BasepackagesUsersAccounts::class,
            'id',
            [
                'alias' => 'account'
            ]
        );

        $this->modelRelations['contact']['relationObj'] = $this->hasOne(
            'id',
            BasepackagesContactBook::class,
            'package_row_id',
            [
                'alias'                 => 'contact',
                'params'                => [
                    'conditions'        => 'package_class = :package_class:',
                    'bind'              => [
                        'package_class'  => str_replace('\\', '_', Profiles::class)
                    ]
                ]
            ]
        );

        $this->modelRelations['addresses']['relationObj'] = $this->hasMany(
            'id',
            BasepackagesAddressBook::class,
            'package_row_id',
            [
                'alias'                 => 'addresses',
                'params'                => [
                    'conditions'        => 'package_class = :package_class:',
                    'bind'              => [
                        'package_class'  => str_replace('\\', '_', Profiles::class)
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