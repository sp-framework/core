<?php

namespace Apps\Dash\Components\System\Tools\Qrcodes;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\System\Tools\Qrcodes\Qrcodes;
use System\Base\BaseComponent;

class QrcodesComponent extends BaseComponent
{
    use DynamicTable;

    protected $qrcodes;

    protected $qrcodesSettings;

    public function initialize()
    {
        $this->qrcodes = $this->usePackage(Qrcodes::class);

        $this->qrcodesSettings = $this->qrcodes->getQrcodesSettings();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $storages = $this->basepackages->storages;

        if ($this->qrcodesSettings['logo'] && $this->qrcodesSettings['logo'] !== '') {
            $this->view->logoLink = $storages->getPublicLink($this->qrcodesSettings['logo'], 200);
        } else {
            $this->view->logoLink = '';
        }

        $storages = $this->basepackages->storages->getAppStorages();

        if ($storages && isset($storages['public'])) {
            $this->view->storages = $storages['public'];
        } else {
            $this->view->storages = [];
        }

        $this->qrcodesSettings['codeForegroundColor'] = 'rgb(' . implode(',', $this->qrcodesSettings['codeForegroundColor']) . ')';
        $this->qrcodesSettings['codeBackgroundColor'] = 'rgb(' . implode(',', $this->qrcodesSettings['codeBackgroundColor']) . ')';
        $this->qrcodesSettings['labelColor'] = 'rgb(' . implode(',', $this->qrcodesSettings['labelColor']) . ')';

        $this->view->qrcodesSettings = $this->qrcodesSettings;

        $this->getNewToken();
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

            $this->qrcodes->updateQrcodesSettings($this->postData());

            $this->view->responseCode = $this->qrcodes->packagesData->responseCode;

            $this->view->responseMessage = $this->qrcodes->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function testQrcodeAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->generateQrcodeAction();

            if ($this->qrcodes->packagesData->responseCode === 0) {
                $this->view->qrcode = $this->qrcodes->packagesData->qrcode;
            }

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function generateQrcodeAction()
    {
        $this->qrcodes->generateQrcode($this->postData()['qrcode'], $this->postData());

        $this->view->responseCode = $this->qrcodes->packagesData->responseCode;

        $this->view->responseMessage = $this->qrcodes->packagesData->responseMessage;
    }
}