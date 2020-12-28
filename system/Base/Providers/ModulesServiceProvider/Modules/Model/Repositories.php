<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Repositories extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $repo_url;

    public $site_url;

    public $branch;

    public $auth_token;

    public $username;

    public $password;

    public $token;

    public function initialize()
    {
        $this->setSource('repositories');
        $this->useDynamicUpdate(true);

        // $this->skipAttributes(
        //     [
        //         'inv_total',
        //         'inv_created_at',
        //     ]
        // );

        // $this->skipAttributesOnCreate(
        //     [
        //         'inv_created_at',
        //     ]
        // );

        // $this->skipAttributesOnUpdate(
        //     [
        //         'inv_modified_at',
        //     ]
        // );
    }
}