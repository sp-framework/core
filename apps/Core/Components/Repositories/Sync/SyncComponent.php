<?php

namespace Apps\Core\Components\Repositories\Sync;

use System\Base\BaseComponent;

class SyncComponent extends BaseComponent
{
    public function viewAction()
    {
        if (isset($this->getData()['repoId'])) {

            $synced = $this->modules->manager->syncRemoteWithLocal($this->getData()['repoId']);

            if ($synced === true) {

                $modulesData = $this->modules->manager->getModulesData(true);

            } else {
                $this->view->responseCode = $this->modules->manager->packagesData->responseCode;

                $this->view->responseMessage = $this->modules->manager->packagesData->responseMessage;

                return $this->sendJson();
            }

            if ($modulesData === true) {

                $this->view->responseCode = $this->modules->manager->packagesData->responseCode;

                $this->view->responseMessage = $this->modules->manager->packagesData->responseMessage;

                $this->view->modulesData = $this->modules->manager->packagesData->modulesData;

                $this->view->counter = $this->modules->manager->packagesData->counter;

                $this->view->thisApp = $this->modules->manager->packagesData->appInfo;

                $this->view->pick('../modules');

                return;
            } else {

                $this->view->responseCode = $modulesData->packagesData->responseCode;

                $this->view->responseMessage = $modulesData->packagesData->responseMessage;

                return $this->sendJson();
            }
        }

        return false;
    }
}