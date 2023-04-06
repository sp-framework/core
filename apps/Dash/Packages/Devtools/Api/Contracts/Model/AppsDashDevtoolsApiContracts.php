<?php

namespace Apps\Dash\Packages\Devtools\Api\Contracts\Model;

use System\Base\BaseModel;

class AppsDashDevtoolsApiContracts extends BaseModel
{
    public $id;

    public $name;

    public $api_type;

    public $link;

    public $filename;

    public $wsdl_convert;

    public $content;
}