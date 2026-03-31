<?php

namespace Apps\Core\Components\Pages;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class PagesComponent extends BaseComponent
{
    use DynamicTable;

    protected $pages;

    public function initialize()
    {
        $this->pages = $this->basepackages->pages;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            $this->view->mode = 'edit';

            if ($this->getData()['id'] != 0) {
                $page = $this->pages->getById($this->getData()['id']);

                if (!$page) {
                    return $this->throwIdNotFound();
                }

                if (!isset($this->getData()['edit'])) {
                    $this->getQueryArr['id'] = null;//Add this to disable token generation on page view.

                    $this->view->mode = 'view';
                }

                $this->view->page = $page;
            }

            $this->view->pick('pages/view');

            return;
        }

        $controlActions =
            [
                'includeQ'              => true,
                'actionsToEnable'       =>
                [
                    'view'      => 'pages/q/',
                    'edit'      => 'pages/q/edit/true/',
                    'remove'    => 'pages/remove/q/'
                ]
            ];

        $this->generateDTContent(
            $this->pages,
            'pages/view',
            null,
            ['name'],
            true,
            ['name'],
            $controlActions,
            [],
            null,
            'name'
        );

        $this->view->pick('pages/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->pages->addPage($this->postData());

        $this->addResponse(
            $this->pages->packagesData->responseMessage,
            $this->pages->packagesData->responseCode,
            $this->pages->packagesData->responseData
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->pages->updatePage($this->postData());

        $this->addResponse(
            $this->pages->packagesData->responseMessage,
            $this->pages->packagesData->responseCode,
            $this->pages->packagesData->responseData
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->pages->removePage($this->postData());

        $this->addResponse(
            $this->pages->packagesData->responseMessage,
            $this->pages->packagesData->responseCode
        );
    }
}