<?php

namespace Apps\Dash\Packages\Ims\Products\Model;

use System\Base\BaseModel;

class ImsProducts extends BaseModel
{
    public $id;

    public $code_mpn;

    public $code_sku;

    public $code_ean;

    public $code_ean_barcode;

    public $title;

    public $subtitle;

    public $supplier;

    public $brand;

    public $description;

    public $images;

    public $attachments;
}