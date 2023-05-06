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

            $fileName = $this->session->getId();

            if (isset($this->postData()['session'])) {
                $fileName = $this->postData()['session'];
            } else if (isset($this->postData()['file_name'])) {
                $fileName = $this->postData()['file_name'];
            }

            $progress = $this->basepackages->progress->getProgress($fileName);

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