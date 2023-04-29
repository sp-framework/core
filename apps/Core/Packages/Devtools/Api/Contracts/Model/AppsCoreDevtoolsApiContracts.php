<?php

namespace Apps\Core\Packages\Devtools\Api\Contracts\Model;

use System\Base\BaseModel;

class AppsCoreDevtoolsApiContracts extends BaseModel
{
    public $id;

    public $provider_name;

    public $category;

    public $type;

    public $link;

    public $filename;

    public $wsdl_convert;

    public $content;
}