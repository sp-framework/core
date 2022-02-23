<?php

namespace Apps\Dash\Packages\Ims\Categories\Model;

use System\Base\BaseModel;

class ImsCategories extends BaseModel
{
    public $id;

    public $image;

    public $name;

    public $hierarchy;

    public $hierarchy_str;

    public $description;

    public $parent;

    public $has_childs;

    public $product_count;

    public $sequence;

    public $need_auth;

    public $visible_to_role_ids;

    public $visible_on_channel_ids;

    // public $seo_title;

    // public $seo_meta_keywords;

    // public $seo_meta_description;

    // public $seo_heading;
}