<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="components")
 */
class Components extends BaseModel
{
    /**
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\Column(name="display_name", type="string", length=50, nullable=true)
     */
    protected $display_name;

    /**
     * @ORM\Column(name="description", type="string", length=2048, nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="version", type="string", length=15)
     */
    protected $version;

    /**
     * @ORM\Column(name="repo", type="string", length=2048)
     */
    protected $repo;

    /**
     * @ORM\Column(name="path", type="string", length=2048)
     */
    protected $path;

    /**
     * @ORM\Column(name="settings", type="text", length=65535, nullable=true)
     */
    protected $settings;

    /**
     * @ORM\Column(name="dependencies", type="text", length=65535)
     */
    protected $dependencies;

    /**
     * @ORM\Column(name="application_id", type="integer")
     */
    protected $application_id;

    /**
     * @ORM\Column(name="installed", type="integer")
     */
    protected $installed;

    /**
     * @ORM\Column(name="files", type="text", length=65535, nullable=true)
     */
    protected $files;

    /**
     * @ORM\Column(name="update_available", type="integer", nullable=true)
     */
    protected $update_available;

    /**
     * @ORM\Column(name="update_version", type="string", length=15, nullable=true)
     */
    protected $update_version;
}