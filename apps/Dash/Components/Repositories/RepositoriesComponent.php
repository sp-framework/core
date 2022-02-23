<?php

namespace Apps\Dash\Components\Repositories;

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

            $this->addResponse(
                $this->modules->repositories->packagesData->responseMessage,
                $this->modules->repositories->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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

            $this->addResponse(
                $this->modules->repositories->packagesData->responseMessage,
                $this->modules->repositories->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
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

            $this->addResponse(
                $this->modules->repositories->packagesData->responseMessage,
                $this->modules->repositories->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function syncAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if (isset($this->postData()['repoId'])) {
                $counter = null;

                if ($this->modules->manager->syncRemoteWithLocal($this->postData()['repoId'])) {
                    $counter = $this->modules->manager->packagesData->counter;
                }

                $this->addResponse(
                    $this->modules->manager->packagesData->responseMessage,
                    $this->modules->manager->packagesData->responseCode,
                    $counter
                );

                return $this->sendJson();

                // if ($modulesData === true) {

                //     $this->view->responseCode = $this->modules->manager->packagesData->responseCode;

                //     $this->view->responseMessage = $this->modules->manager->packagesData->responseMessage;

                //     // $this->view->modulesData = $this->modules->manager->packagesData->modulesData;

                //     $this->view->counter = $this->modules->manager->packagesData->counter;

                //     // $this->view->thisApp = $this->modules->manager->packagesData->appInfo;

                //     $this->setDefaultViewData();

                //     // $this->view->pick('../modules/modulesdata');
                // } else {

                //     $this->view->responseCode = $modulesData->packagesData->responseCode;

                //     $this->view->responseMessage = $modulesData->packagesData->responseMessage;

                //     return $this->sendJson();
            } else {
                $this->addResponse('Repo id not provided', 1);
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}