<?php

namespace System\Base\Providers\ModulesServiceProvider\Modules\Model;

use System\Base\BaseModel;

/**
 * @Entity
 * @Table(name="repositories")
 */
class Menus extends BaseModel
{
    /**
     * @GeneratedValue(strategy="AUTO")
     * @Id
     * @Column(type="integer")
     */
    public $id;
}