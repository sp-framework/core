<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="applications")
 */
class Applications extends BaseModel
{
    /**
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    public $name;

    /**
     * @ORM\Column(name="route", type="string", length=50, nullable=true)
     */
    public $route;

    /**
     * @ORM\Column(name="display_name", type="string", length=50, nullable=true)
     */
    public $display_name;

    /**
     * @ORM\Column(name="description", type="string", length=2048, nullable=true)
     */
    public $description;

    /**
     * @ORM\Column(name="version", type="string", length=15)
     */
    public $version;

    /**
     * @ORM\Column(name="repo", type="string", length=2048)
     */
    public $repo;

    /**
     * @ORM\Column(name="settings", type="text", length=65535)
     */
    public $settings;

    /**
     * @ORM\Column(name="dependencies", type="text", length=65535)
     */
    public $dependencies;

    /**
     * @ORM\Column(name="is_default", type="integer")
     */
    public $is_default;

    /**
     * @ORM\Column(name="installed", type="integer")
     */
    public $installed;

    /**
     * @ORM\Column(name="files", type="text", length=65535, nullable=true)
     */
    public $files;

    /**
     * @ORM\Column(name="update_available", type="integer", nullable=true)
     */
    public $update_available;

    /**
     * @ORM\Column(name="update_version", type="string", length=15, nullable=true)
     */
    public $update_version;

    /**
     * @ORM\Column(name="mode", type="integer")
     */
    public $mode;
}