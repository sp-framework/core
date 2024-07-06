<?php

namespace Apps\Core\Packages\Devtools\Migrator\Model;

use System\Base\BaseModel;

class DevtoolsMigrator extends BaseModel
{
    public $id;

    public $api_id;

    public $repository_id;

    public $source_issue_id;

    public $destination_issue_id;

    public $issue_details;

    public $issue_comments;

    public $issue_timeline;

    public $migrated;
}