<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

/**
 * @Entity
 * @Table(name="repositories")
 */
class Repositories extends BaseModel
{
    /**
     * @GeneratedValue(strategy="AUTO")
     * @Id
     * @Column(type="integer")
     */
    public $id;

    /**
     * @Column(name="name", type="string", length=50)
     */
    public $name;

    /**
     * @Column(name="description", type="string", length=2048, nullable=true)
     */
    public $description;

    /**
     * @Column(name="url", type="string", length=2048, nullable=true)
     */
    public $url;

    /**
     * @Column(name="need_auth", type="integer", nullable=true)
     */
    public $need_auth;

    /**
     * @Column(name="username", type="text", length=50, nullable=true)
     */
    public $username;

    /**
     * @Column(name="token", type="text", length=1024, nullable=true)
     */
    public $token;

    public function initialize()
    {
        $this->setSource('repositories');
    }
}