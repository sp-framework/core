<?php

namespace Apps\Dash\Packages\Devtools\Api\Contracts\Model;

use System\Base\BaseModel;

class DevtoolsApiContracts extends BaseModel
{
    public $id;

    public $name;

    public $link;

    public $filename;

    public $content;
}