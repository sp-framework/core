<?php

namespace System\Base\Providers\AppsServiceProvider\Model;

use System\Base\BaseModel;
use System\Base\Providers\AccessServiceProvider\Model\ServiceProviderAccessIpFilter;

class ServiceProviderApps extends BaseModel
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

    public $recover_password;

    public $approve_accounts_manually;

    public $enforce_2fa;

    public $registration_role_id;

    public $guest_role_id;

    public $can_login_role_ids;

    public $acceptable_usernames;

    public $incorrect_login_attempt_block_ip;

    public $auto_unblock_ip_minutes;

    public $ip_filter_default_action;

    public $menu_structure;

    public $settings;

    public function initialize()
    {
        $clientAddress = $this->getDi()->getRequest()->getClientAddress();

        $this->modelRelations['ipFilters']['relationObj'] = $this->hasMany(
            'id',
            ServiceProviderAccessIpFilter::class,
            'app_id',
            [
                'alias'         => 'ipFilters'
            ]
        );

        $this->modelRelations['monitorlist']['relationObj'] = $this->hasOne(
            'id',
            ServiceProviderAccessIpFilter::class,
            'app_id',
            [
                'alias'         => 'monitorlist',
                'params'        => function() use ($clientAddress) {
                    return
                    [
                        'conditions'        => 'ip_address = :ipaddress: AND added_by = :addedby:',
                        'bind'              => [
                            'ipaddress'     => $clientAddress,
                            'addedby'       => 0
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