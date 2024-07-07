<?php

namespace Apps\Core\Components\System\Tools\Barcodes;

use System\Base\BaseComponent;

class BarcodesComponent extends BaseComponent
{
    protected $barcodes;

    protected $barcodesSettings;

    public function initialize()
    {
        $this->barcodes = $this->basepackages->barcodes;

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
        $this->requestIsPost();

        $this->barcodes->updateBarcodesSettings($this->postData());

        $this->addResponse(
            $this->barcodes->packagesData->responseMessage,
            $this->barcodes->packagesData->responseCode
        );
    }

    public function testBarcodeAction()
    {
        $this->requestIsPost();

        $this->generateBarcodeAction(false);

        if ($this->barcodes->packagesData->responseCode === 0) {
            $this->view->barcode = $this->barcodes->packagesData->barcode;
        }
    }

    public function generateBarcodeAction($test = false)
    {
        $this->requestIsPost();

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
}