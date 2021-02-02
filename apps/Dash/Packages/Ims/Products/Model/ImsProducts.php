<?php

namespace Apps\Dash\Packages\Ims\Products\Model;

use System\Base\BaseModel;

class ImsProducts extends BaseModel
{
    public $id;

    public $code_mpn;

    public $code_sku;

    public $code_upc;

    public $title;

    public $subtitle;

    public $supplier;

    public $brand;

    public $description;

    public $images;

    public $attachments;
}