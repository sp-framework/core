<?php

namespace Apps\Dash\Packages\Ims\Categories\Model;

use System\Base\BaseModel;

class ImsCategories extends BaseModel
{
    public $id;

    public $image;

    public $channel_id;

    public $name;

    public $type;

    public $description;

    public $parent;

    public $has_childs;

    public $product_count;

    public $category;

    public $sequence;

    public $need_auth;

    public $visible_to_role_ids;

    // public $seo_title;

    // public $seo_meta_keywords;

    // public $seo_meta_description;

    // public $seo_heading;

}