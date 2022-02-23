<?php

namespace Apps\Dash\Packages\System\Api\Ebay\Taxonomy\Model;

use System\Base\BaseModel;

class SystemApiEbayTaxonomy extends BaseModel
{
    public $id;

    public $name;

    public $hierarchy;

    public $hierarchy_str;

    public $installed;

    public $enabled;

    public $root_id;

    public $parent_id;

    public $has_childs;

    public $taxonomy_version;

    public $product_count;
}