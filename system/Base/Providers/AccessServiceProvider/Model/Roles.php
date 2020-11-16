<?php

namespace System\Base\Providers\AccessServiceProvider\Model;

use System\Base\BaseModel;

class Roles extends BaseModel
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
     * @Column(column="description", type="string", length=2048, nullable=true)
     */
    public $description;

    /**
     * @Column(column="parent_id", type="integer")
     */
    public $parent_id;

    /**
     * @Column(column="permissions", type="string", length=65535)
     */
    public $permissions;
}