<?php

namespace Apps\Core\Components\System\Tools\Qrcodes;

use System\Base\BaseComponent;

class QrcodesComponent extends BaseComponent
{
    protected $qrcodes;

    protected $qrcodesSettings;

    public function initialize()
    {
        $this->qrcodes = $this->basepackages->qrcodes;

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
        $this->requestIsPost();

        $this->qrcodes->updateQrcodesSettings($this->postData());

        $this->addResponse(
            $this->qrcodes->packagesData->responseMessage,
            $this->qrcodes->packagesData->responseCode
        );
    }

    public function testQrcodeAction()
    {
        $this->requestIsPost();

        $this->generateQrcodeAction();

        if ($this->qrcodes->packagesData->responseCode === 0) {
            $this->view->qrcode = $this->qrcodes->packagesData->qrcode;
        }
    }

    public function generateQrcodeAction()
    {
        $this->requestIsPost();

        $this->qrcodes->generateQrcode($this->postData()['qrcode'], $this->postData());

        $this->addResponse(
            $this->qrcodes->packagesData->responseMessage,
            $this->qrcodes->packagesData->responseCode
        );
    }
}