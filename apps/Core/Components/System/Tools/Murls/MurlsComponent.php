<?php

namespace Apps\Core\Components\System\Tools\Murls;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class MurlsComponent extends BaseComponent
{
    use DynamicTable;

    protected $murls;

    public function initialize()
    {
        $this->murls = $this->basepackages->murls;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $murl = $this->murl->getById($this->getData()['id']);

                if (!$murl) {
                    return $this->throwIdNotFound();
                }

                $this->view->murl = $murl;
            }

            $this->view->domains = $this->domains->domains;

            $this->view->apps = $this->apps->apps;

            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/tools/murls',
                    'remove'    => 'system/tools/murls/remove'
                ]
            ];

        $this->generateDTContent(
            $this->murls,
            'system/tools/murls/view',
            null,
            ['murl', 'hits', 'valid_till'],
            false,
            ['murl', 'hits', 'valid_till'],
            $controlActions,
            null,
            null,
            'murl'
        );

        $this->view->pick('murls/list');
    }

    /**
     * @acl(name="add")
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->murls->addMurl($this->postData());

        $this->addResponse($this->murls->packagesData->responseMessage, $this->murls->packagesData->responseCode);
    }

    /**
     * @acl(name="update")
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->murls->updateMurl($this->postData());

        $this->addResponse($this->murls->packagesData->responseMessage, $this->murls->packagesData->responseCode);
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->murls->removeMurl($this->postData());

        $this->addResponse($this->murls->packagesData->responseMessage, $this->murls->packagesData->responseCode);
    }

    public function generateMurlAction()
    {
        $this->requestIsPost();

        $this->murls->generateMurl($this->postData());

        $this->addResponse($this->murls->packagesData->responseMessage, $this->murls->packagesData->responseCode);
    }
}