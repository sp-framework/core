<?php

namespace Apps\Dash\Components\System\Tools\Barcodes;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Apps\Dash\Packages\System\Tools\Barcodes\Barcodes;
use System\Base\BaseComponent;

class BarcodesComponent extends BaseComponent
{
    use DynamicTable;

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

            $this->view->responseCode = $this->barcodes->packagesData->responseCode;

            $this->view->responseMessage = $this->barcodes->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
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
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
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

        $this->view->responseCode = $this->barcodes->packagesData->responseCode;

        $this->view->responseMessage = $this->barcodes->packagesData->responseMessage;
    }
}