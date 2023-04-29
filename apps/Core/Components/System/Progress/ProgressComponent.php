<?php

namespace Apps\Core\Components\System\Progress;

use System\Base\BaseComponent;

class ProgressComponent extends BaseComponent
{
    public function getProgressAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $session = $this->session->getId();

            if (isset($this->postData()['session'])) {
                $session = $this->postData()['session'];
            }

            $progress = $this->basepackages->progress->getProgress($session);

            if ($progress) {
                $this->addResponse(
                    $this->basepackages->progress->packagesData->responseMessage,
                    $this->basepackages->progress->packagesData->responseCode,
                    $progress,
                );
            } else {
                $this->addResponse(
                    $this->basepackages->progress->packagesData->responseMessage,
                    $this->basepackages->progress->packagesData->responseCode
                );
            }
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}