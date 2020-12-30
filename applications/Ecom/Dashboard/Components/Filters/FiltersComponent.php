<?php

namespace Applications\Ecom\Dashboard\Components\Filters;

use System\Base\BaseComponent;

class FiltersComponent extends BaseComponent
{
    protected $filters;

    public function initialize()
    {
        $this->filters = $this->basepackages->filters;
    }

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
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->filters->addFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            $this->view->filters = $this->filters->packagesData->filters;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->filters->updateFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            $this->view->filters = $this->filters->packagesData->filters;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function cloneAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->filters->cloneFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            $this->view->filters = $this->filters->packagesData->filters;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            if (!$this->checkCSRF()) {
                return;
            }

            $this->filters->removeFilter($this->postData());

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;

            $this->view->filters = $this->filters->packagesData->filters;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function getdefaultfilterAction()
    {
        if ($this->request->isPost()) {

            if ($this->filters->getDefaultFilter($this->postData()['component_id'])) {
                $this->view->defaultFilter = $this->filters->packagesData->defaultFilter;
            }

            $this->view->responseCode = $this->filters->packagesData->responseCode;

            $this->view->responseMessage = $this->filters->packagesData->responseMessage;
        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}