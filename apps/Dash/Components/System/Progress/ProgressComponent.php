<?php

namespace Apps\Dash\Components\System\Progress;

use System\Base\BaseComponent;

class ProgressComponent extends BaseComponent
{
    public function getProgressAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            if ($this->basepackages->progress->getProgress()) {
                $this->addResponse(
                    $this->basepackages->progress->packagesData->responseMessage,
                    $this->basepackages->progress->packagesData->responseCode,
                    $this->basepackages->progress->packagesData->responseData,
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