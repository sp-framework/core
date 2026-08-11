<?php

namespace System\Base\Providers\ApiServiceProvider\Model;

use League\OAuth2\Server\Entities\ScopeEntityInterface;
use System\Base\BaseModel;
use System\Base\Providers\ApiServiceProvider\Model\ServiceProviderApi;

class ServiceProviderApiScopes extends BaseModel implements ScopeEntityInterface
{
    protected $modelRelations = [];

    public $id;

    public $name;

    public $scope_name;

    public $description;

    public $permissions;

    public function getIdentifier() :string
    {
        return $this->scope_name;
    }

    public function initialize()
    {
        $this->modelRelations['api']['relationObj'] = $this->belongsTo(
            'id',
            ServiceProviderApi::class,
            'scope_id',
            [
                'alias'         => 'api'
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
