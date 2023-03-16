<?php

namespace Apps\Dash\Components\System\Tools\Barcodes;

use Apps\Dash\Packages\System\Tools\Barcodes\Barcodes;
use System\Base\BaseComponent;
use System\Base\Interfaces\ComponentInterface;

class BarcodesComponent extends BaseComponent implements ComponentInterface
{
    protected $barcodes;

    protected $barcodesSettings;

    public function initialize()
    {
        $this->barcodes = $this->usePackage(Barcodes::class);

        $this->barcodesSettings = $this->barcodes->getBarcodesSettings();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $this->view->barcodesSettings = $this->barcodesSettings;

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

            $this->barcodes->updateBarcodesSettings($this->postData());

            $this->addResponse(
                $this->barcodes->packagesData->responseMessage,
                $this->barcodes->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function testBarcodeAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->generateBarcodeAction(false);

            if ($this->barcodes->packagesData->responseCode === 0) {
                $this->view->barcode = $this->barcodes->packagesData->barcode;
            }

        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function generateBarcodeAction($test = false)
    {
        $this->barcodes->generateBarcode(
            $this->postData()['barcode'],
            $this->postData()['barcodeType'],
            $this->postData(),
            $test
        );

        $this->addResponse(
            $this->barcodes->packagesData->responseMessage,
            $this->barcodes->packagesData->responseCode
        );
    }

    public function addAction()
    {
        //
    }

    public function removeAction()
    {
        //
    }
}