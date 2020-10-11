<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

/**
 * Entity
 * Table(name="applications")
 */
class Applications extends BaseModel
{
    /**
     * GeneratedValue(strategy="AUTO")
     * Id
     * Column(type="integer")
     */
    public $id;

    /**
     * Column(name="name", type="string", length=50, unique=true)
     */
    public $name;

    /**
     * Column(name="route", type="string", length=50, nullable=true)
     */
    public $route;

    /**
     * Column(name="display_name", type="string", length=50, nullable=true)
     */
    public $display_name;

    /**
     * Column(name="description", type="string", length=2048, nullable=true)
     */
    public $description;

    /**
     * Column(name="version", type="string", length=15)
     */
    public $version;

    /**
     * Column(name="repo", type="string", length=2048)
     */
    public $repo;

    /**
     * Column(name="settings", type="text", length=65535)
     */
    public $settings;

    /**
     * Column(name="dependencies", type="text", length=65535)
     */
    public $dependencies;

    /**
     * Column(name="is_default", type="integer")
     */
    public $is_default;

    /**
     * Column(name="installed", type="integer")
     */
    public $installed;

    /**
     * Column(name="files", type="text", length=65535, nullable=true)
     */
    public $files;

    /**
     * Column(name="update_available", type="integer", nullable=true)
     */
    public $update_available;

    /**
     * Column(name="update_version", type="string", length=15, nullable=true)
     */
    public $update_version;

    /**
     * Column(name="mode", type="integer")
     */
    public $mode;
}