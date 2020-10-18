<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

class Core extends BaseModel
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
     * @Column(column="display_name", type="string", length=50, nullable=true)
     */
    public $display_name;

    /**
     * @Column(column="description", type="string", length=2048, nullable=true)
     */
    public $description;

    /**
     * @Column(column="version", type="string", length=15)
     */
    public $version;

    /**
     * @Column(column="repo", type="string", length=2048)
     */
    public $repo;

    /**
     * @Column(column="installed", type="integer")
     */
    public $installed;

    /**
     * Column(name="settings", type="text", length=65535, nullable=true)
     */
    public $settings;

    /**
     * @Column(column="files", type="text", length=65535, nullable=true)
     */
    public $files;

    /**
     * @Column(column="update_available", type="integer", nullable=true)
     */
    public $update_available;

    /**
     * @Column(column="update_version", type="string", length=15, nullable=true)
     */
    public $update_version;
}