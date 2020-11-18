<?php

namespace Applications\Admin\Components\Filter;

use Applications\Admin\Packages\Filter\Filter;
use System\Base\BaseComponent;

class FilterComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        //
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $filterPackage = $this->usePackage(Filter::class);

        $add = $filterPackage->addFilter($this->postData());

        if ($add) {
            $this->view->filters = $filterPackage->packagesData->filters;
        }

        $this->view->responseCode = $filterPackage->packagesData->responseCode;

        $this->view->responseMessage = $filterPackage->packagesData->responseMessage;
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $filterPackage = $this->usePackage(Filter::class);

        $update = $filterPackage->updateFilter($this->postData());

        if ($update) {
            $this->view->filters = $filterPackage->packagesData->filters;
        }

        $this->view->responseCode = $filterPackage->packagesData->responseCode;

        $this->view->responseMessage = $filterPackage->packagesData->responseMessage;
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $filterPackage = $this->usePackage(Filter::class);

        $remove = $filterPackage->removeFilter($this->postData());

        if ($remove) {
            $this->view->filters = $filterPackage->packagesData->filters;
        }

        $this->view->responseCode = $filterPackage->packagesData->responseCode;

        $this->view->responseMessage = $filterPackage->packagesData->responseMessage;
    }

    /**
     * @acl(name=clone)
     */
    public function cloneAction()
    {
        $filterPackage = $this->usePackage(Filter::class);

        $clone = $filterPackage->cloneFilter($this->postData());

        if ($clone) {
            $this->view->filters = $filterPackage->packagesData->filters;
        }

        $this->view->filter = $filterPackage->packagesData->last;

        $this->view->responseCode = $filterPackage->packagesData->responseCode;

        $this->view->responseMessage = $filterPackage->packagesData->responseMessage;
    }

    //need to move to view
    public function getDefaultFilterAction()
    {
        $filterPackage = $this->usePackage(Filter::class);

        $defaultFilter = $filterPackage->getDefaultFilter($this->postData()['component_id']);

        if ($defaultFilter) {
            $this->view->defaultFilter = $filterPackage->packagesData->defaultFilter;
        }

        $this->view->responseCode = $filterPackage->packagesData->responseCode;

        $this->view->responseMessage = $filterPackage->packagesData->responseMessage;
    }
}