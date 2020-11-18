<?php

namespace Applications\Admin\Components\Home;

use Applications\Admin\Components\Role\RoleComponent;
use Applications\Admin\Packages\Filters\Install\Package;
use Phalcon\Annotations\Reader;
use Phalcon\Annotations\Reflection;
use System\Base\BaseComponent;

class HomeComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {

        // $modules = $this->usePackage(ModulesPackage::class);
        // var_dump($this->modules->menus->getMenusForApplication($this->application['id']));
        // $this->view->disable();

        //For installing filters
        // $filterPackage = new Package();
        // $filterPackage->installPackage();

    }
}