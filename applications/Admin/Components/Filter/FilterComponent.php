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
        $add = $this->basepackages->filters->addFilter($this->postData());

        if ($add) {
            $this->view->filters = $this->basepackages->filters->packagesData->filters;
        }

        $this->view->responseCode = $this->basepackages->filters->packagesData->responseCode;

        $this->view->responseMessage = $this->basepackages->filters->packagesData->responseMessage;
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $update = $this->basepackages->filters->updateFilter($this->postData());

        if ($update) {
            $this->view->filters = $this->basepackages->filters->packagesData->filters;
        }

        $this->view->responseCode = $this->basepackages->filters->packagesData->responseCode;

        $this->view->responseMessage = $this->basepackages->filters->packagesData->responseMessage;
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $remove = $this->basepackages->filters->removeFilter($this->postData());

        if ($remove) {
            $this->view->filters = $this->basepackages->filters->packagesData->filters;
        }

        $this->view->responseCode = $this->basepackages->filters->packagesData->responseCode;

        $this->view->responseMessage = $this->basepackages->filters->packagesData->responseMessage;
    }

    /**
     * @acl(name=clone)
     */
    public function cloneAction()
    {
        $clone = $this->basepackages->filters->cloneFilter($this->postData());

        if ($clone) {
            $this->view->filters = $this->basepackages->filters->packagesData->filters;
        }

        $this->view->filter = $this->basepackages->filters->packagesData->last;

        $this->view->responseCode = $this->basepackages->filters->packagesData->responseCode;

        $this->view->responseMessage = $this->basepackages->filters->packagesData->responseMessage;
    }

    //need to move to view
    public function getDefaultFilterAction()
    {
        $defaultFilter = $this->basepackages->filters->getDefaultFilter($this->postData()['component_id']);

        if ($defaultFilter) {
            $this->view->defaultFilter = $this->basepackages->filters->packagesData->defaultFilter;
        }

        $this->view->responseCode = $this->basepackages->filters->packagesData->responseCode;

        $this->view->responseMessage = $this->basepackages->filters->packagesData->responseMessage;
    }
}