<?php

namespace Applications\Ecom\Admin\Components\Repositories;

use System\Base\BaseComponent;

class RepositoriesComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $repository = $this->modules->repositories->getById($this->getData()['id']);

                $this->view->repository = $repository;
            }
        }
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
            $this->modules->repositories->addRepository($this->postData());

            $this->view->responseCode = $this->modules->repositories->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->repositories->packagesData->responseMessage;

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
            $this->modules->repositories->updateRepository($this->postData());

            $this->view->responseCode = $this->modules->repositories->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->repositories->packagesData->responseMessage;

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
            $this->modules->repositories->removeRepository($this->postData());

            $this->view->responseCode = $this->modules->repositories->packagesData->responseCode;

            $this->view->responseMessage = $this->modules->repositories->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}