<?php

namespace Applications\Admin\Components\Repositories;

use System\Base\BaseComponent;

class RepositoriesComponent extends BaseComponent
{
    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->view->thisApplication = $this->modules->applications->getApplicationInfo();
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->isPost()) {

            if ($this->modules->repositories->add($this->postData())) {

                $this->flashSession->clear();

                $this->view->responseCode =
                    $this->modules->repositories->packagesData->responseCode;

                $this->flashSession->success(
                    $this->modules->repositories->packagesData->responseMessage);

            } else {

                $this->view->responseMessage = 'Error! Could not add repository.';

                $this->view->responseCode = 1;
            }
        } else {

            $this->view->responseMessage = 'Request method not allowed.';

            $this->view->responseCode = 1;
        }
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            if ($this->modules->repositories->update($this->postData())) {

                $this->flashSession->clear();

                $this->view->responseCode =
                    $this->modules->repositories->packagesData->responseCode;

                $this->flashSession->success(
                    $this->modules->repositories->packagesData->responseMessage
                );
            } else {
                $this->view->responseCode =
                    $this->modules->repositories->packagesData->responseCode;

                $this->view->responseMessage =
                    $this->modules->repositories->packagesData->responseMessage;
            }
        } else {

            $this->view->responseMessage = 'Request method not allowed.';

            $this->view->responseCode = 1;
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {
            if ($this->modules->repositories->remove($this->postData()['id'])) {

                $this->view->responseCode =
                    $this->modules->repositories->packagesData->responseCode;

                $this->flashSession->success(
                    $this->modules->repositories->packagesData->responseMessage
                );
            } else {
                $this->view->responseCode =
                    $this->modules->repositories->packagesData->responseCode;

                $this->view->responseMessage =
                    $this->modules->repositories->packagesData->responseMessage;
            }
        } else {

            $this->view->responseMessage = 'Request method not allowed.';

            $this->view->responseCode = 1;
        }
    }
}