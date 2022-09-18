<?php

namespace System\Base\Providers\AppsServiceProvider\Model;

use System\Base\BaseModel;
use System\Base\Providers\AppsServiceProvider\Model\AppsIpBlackList;

class Apps extends BaseModel
{
    protected $modelRelations = [];

    public $id;

    public $name;

    public $route;

    public $description;

    public $app_type;

    public $default_component;

    public $errors_component;

    public $registration_allowed;

    public $registration_role_id;

    public $guest_role_id;

    public $can_login_role_ids;

    public $ip_black_list;

    public $incorrect_login_attempt_blacklist;

    public function initialize()
    {
        $clientAddress = $this->getDi()->getRequest()->getClientAddress();

        $this->modelRelations['blacklist']['relationObj'] = $this->hasMany(
            'id',
            AppsIpBlackList::class,
            'app_id',
            [
                'alias'         => 'blacklist',
                'params'        => function() use ($clientAddress) {
                    return
                    [
                        'conditions'        => 'ip_address = :ipaddress:',
                        'bind'              => [
                            'ipaddress'     => $clientAddress
                        ]
                    ];
                }
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