<?php

namespace System\Base\Providers\ModulesServiceProvider\Model;

use Doctrine\ORM\Mapping as ORM;
use System\Base\Providers\DatabaseServiceProvider\Model;

/**
 * @ORM\Entity
 * @ORM\Table(name="applications")
 */
class Applications extends Model
{
    /**
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=50, unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="route", type="string", length=50, nullable=true)
     */
    protected $route;

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
     * @ORM\Column(name="settings", type="text", length=65535)
     */
    protected $settings;

    /**
     * @ORM\Column(name="dependencies", type="text", length=65535)
     */
    protected $dependencies;

    /**
     * @ORM\Column(name="is_default", type="integer")
     */
    protected $is_default;

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

    /**
     * @ORM\Column(name="mode", type="integer")
     */
    protected $mode;
}