<?php

namespace Apps\Dash\Components\System\Tools\Qrcodes;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\System\Tools\Qrcodes\Qrcodes;
use System\Base\BaseComponent;

class QcodesComponent extends BaseComponent
{
    use DynamicTable;

    protected $qrcodes;

    public function initialize()
    {
        $this->qrcodes = $this->usePackage(Qrcodes::class);
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        //
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

            $this->qrcodes->updateBarcode($this->postData());

            $this->view->responseCode = $this->qrcodes->packagesData->responseCode;

            $this->view->responseMessage = $this->qrcodes->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}