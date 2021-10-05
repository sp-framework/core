<?php

namespace Apps\Dash\Components\System\Tools\Importexport;

use Apps\Dash\Packages\System\Tools\ImportExport\ImportExport;
use System\Base\BaseComponent;

class ImportexportComponent extends BaseComponent
{
    protected $importexport;

    protected $importexportSettings;

    public function initialize()
    {
        $this->importexport = $this->usePackage(ImportExport::class);

        $this->importexportSettings = $this->importexport->getImportexportSettings();
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $storages = $this->basepackages->storages->getAppStorages();

        if ($storages && isset($storages['public'])) {
            $this->view->storages = $storages['public'];
        } else {
            $this->view->storages = [];
        }

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

            $this->importexport->updateQrcodesSettings($this->postData());

            $this->view->responseCode = $this->importexport->packagesData->responseCode;

            $this->view->responseMessage = $this->importexport->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }
}