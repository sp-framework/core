<?php

namespace Apps\Dash\Components\System\Core;

use System\Base\BaseComponent;

class CoreComponent extends BaseComponent
{
    protected $coreSettings;

    public function initialize()
    {
        // $this->coreSettings = $this->core->getBarcodesSettings();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $availableCaches = [];

        foreach ($this->cacheTools->getAvailableCaches() as $key => $value) {
            $availableCaches[$value]['id'] = $value;
            $availableCaches[$value]['name'] = $value;
        }

        $this->view->core = $this->core->core;
        $this->view->availableCaches = $availableCaches;
        $this->view->logLevels = $this->logger->getLogLevels();
        $this->useStorage('public');
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

            $this->core->update($this->postData());

            $this->addResponse(
                $this->core->packagesData->responseMessage,
                $this->core->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function resetAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->core->reset()) {
                $this->addResponse(
                    $this->core->packagesData->responseMessage,
                    $this->core->packagesData->responseCode
                );
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}