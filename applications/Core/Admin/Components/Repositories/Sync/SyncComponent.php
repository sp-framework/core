<?php

namespace Applications\Core\Admin\Components\Repositories\Sync;

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

                $this->view->thisApplication = $this->modules->manager->packagesData->applicationInfo;

                $this->view->pick('../modules');
            } else {

                $this->view->responseCode = $modulesData->packagesData->responseCode;

                $this->view->responseMessage = $modulesData->packagesData->responseMessage;

                return $this->sendJson();
            }
        }
                // var_dump($this->view);

        $this->view->disable();
        // $this->view->repositories = $this->packages->use(Repositories::class)->getAllRepositories();

        // $this->view->setup = isset($this->getData['setup']) ? $this->getData['setup'] : false;

    }
}