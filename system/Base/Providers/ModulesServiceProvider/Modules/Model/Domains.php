<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

/**
 * @Entity
 * @Table(name="domains")
 */
class Domains extends BaseModel
{
    /**
     * @GeneratedValue(strategy="AUTO")
     * @Id
     * @Column(type="integer")
     */
    public $id;

    /**
     * @Column(name="domain", type="string", length=50)
     */
    public $domain;

    /**
     * @Column(name="description", type="string", length=2048, nullable=true)
     */
    public $description;

    /**
     * Column(name="settings", type="text", length=65535)
     */
    public $settings;
}