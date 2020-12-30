<?php

namespace Applications\Ecom\Dashboard\Components\Storages;

use Applications\Ecom\Common\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class StoragesComponent extends BaseComponent
{
    use DynamicTable;

    protected $storages;

    public function initialize()
    {
        $this->storages = $this->basepackages->storages;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['uuid']) && $this->getData()['uuid'] !== '') {
            return $this->storages->getFile($this->getData());
        }
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->hasFiles()) {

            if ($this->storages->storeFile()) {
                $this->view->storageData = $this->storages->packagesData->storageData;
            }

            $this->view->responseCode = $this->storages->packagesData->responseCode;

            $this->view->responseMessage = $this->storages->packagesData->responseMessage;

            return;
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost() && isset($this->postData()['uuid'])) {

            $this->storages->removeFile($this->postData()['uuid']);

            $this->view->responseCode = $this->storages->packagesData->responseCode;

            $this->view->responseMessage = $this->storages->packagesData->responseMessage;

            return;
        }
    }
}