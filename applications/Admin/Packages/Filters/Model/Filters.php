<?php

namespace Applications\Admin\Packages\Filters\Model;

use System\Base\BaseModel;

class Filters extends BaseModel
{
    /**
     * @Primary
     * @Identity
     * @Column(type='integer', nullable=false)
     */
    public $id;

    /**
     * @Column(column="name", type="string", length=50)
     */
    public $name;

    /**
     * Column(name="conditions", type="text", length=65535)
     */
    public $conditions;

    /**
     * @Column(column="component_id", type="integer")
     */
    public $component_id;

    /**
     * @Column(column="permission", type="integer")
     */
    public $permission;

    /**
     * @Column(column="shared_ids", type="text", length=4096, nullable=true)
     */
    public $shared_ids;
}