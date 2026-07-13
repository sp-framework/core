<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\ApiClientServices\Apis\Frameworks\Sp;

use System\Base\BasePackage;

class FrameworksSp extends BasePackage
{
    //protected $modelToUse = ::class;

    protected $packageName = 'frameworkssp';

    public $frameworkssp;

    public function init()
    {
        //Note: If you want to use init function, you need to run parent::init as well.
        //It is used by the use app database feature of the app.
        //if you remove the init() function from this class, it is also fine.
        parent::init();

        return $this;
    }

    public function getFrameworksSpById($id)
    {
        $frameworkssp = $this->getById($id);

        if ($frameworkssp) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }

    public function addFrameworksSp($data)
    {
        //
    }

    public function updateFrameworksSp($data)
    {
        $frameworkssp = $this->getById($id);

        if ($frameworkssp) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }

    public function removeFrameworksSp($data)
    {
        $frameworkssp = $this->getById($id);

        if ($frameworkssp) {
            //
            $this->addResponse('Success');

            return;
        }

        $this->addResponse('Error', 1);
    }
}