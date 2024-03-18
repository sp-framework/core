<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Email;

use System\Base\BaseModel;

class BasepackagesEmailQueue extends BaseModel
{
    public $id;

    public $app_id;

    public $domain_id;

    public $status;

    public $priority;

    public $sent_on;

    public $confidential;

    public $to_addresses;

    public $cc_addresses;

    public $bcc_addresses;

    public $attachments;

    public $subject;

    public $body;

    public $logs;
}