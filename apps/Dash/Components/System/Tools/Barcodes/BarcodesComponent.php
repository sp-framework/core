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

            $this->generateBarcodeAction(
                $this->postData()['barcode'],
                $this->postData()['barcodeType'],
                $this->postData()['generatorName'],
                $this->postData()['width'],
                $this->postData()['height'],
                $this->postData()['foreground'],
                $this->postData()['showText'],
                $this->postData()['textPlacement'],
                true
            );

            if ($this->barcodes->packagesData->responseCode === 0) {
                $this->view->barcode = $this->barcodes->packagesData->barcode;
            }

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function generateBarcodeAction(
        $barcode,
        $barcodeType = "C128",
        $generatorName = "HTML",
        $scale = 2,
        $height = 30,
        $foreground = '#000000',
        $showText = false,
        $textPlacement = 'BOTTOM',
        $test = false
    ) {
        $this->barcodes->generateBarcode($barcode, $barcodeType, $generatorName, $scale, $height, $foreground, $showText, $textPlacement, $test);

        $this->view->responseCode = $this->barcodes->packagesData->responseCode;

        $this->view->responseMessage = $this->barcodes->packagesData->responseMessage;
    }
}