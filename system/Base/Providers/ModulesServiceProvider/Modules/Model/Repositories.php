<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Repositories extends BaseModel
{
    public $id;

    public $name;

    public $description;

    public $url;

    public $need_auth;

    public $username;

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