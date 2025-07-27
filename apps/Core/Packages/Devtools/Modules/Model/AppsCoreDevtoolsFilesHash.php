<?php

namespace Apps\Core\Packages\Devtools\Modules\Model;

use System\Base\BaseModel;

class AppsCoreDevtoolsFilesHash extends BaseModel
{
    public $id;

    public $module_type;

    public $module_id;

    public $files_hash;

    public $release_pending;
}