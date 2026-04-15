<?php

namespace Apps\Core\Components\Pages;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToCheckExistence;
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

            $this->view->contentSources =
                [
                    'file' => [
                        'source'=> 'file',
                        'name'  => 'HTML File'
                    ],
                    'code' => [
                        'source'=> 'code',
                        'name'  => 'HTML Code'
                    ],
                ];

            $appTypesArr = $this->apps->types->types;
            $appTypes = [];

            foreach ($appTypesArr as $value) {
                $appTypes[$value['app_type']]['id'] = $value['app_type'];
                $appTypes[$value['app_type']]['name'] = $value['name'];
            }

            $this->view->appTypes = $appTypes;

            $appsArr = $this->apps->apps;
            $apps = [];

            foreach ($appsArr as $app) {
                $apps[$app['id']]['id'] = $app['id'];
                $apps[$app['id']]['name'] = $app['name'];
                $apps[$app['id']]['route'] = $app['route'];
                $apps[$app['id']]['data']['app_type'] = $app['app_type'];
            }

            $this->view->apps = $apps;

            if ($this->getData()['id'] != 0) {
                $page = $this->pages->getById($this->getData()['id']);

                if (!$page) {
                    return $this->throwIdNotFound();
                }

                if (!isset($this->getData()['edit'])) {
                    if (!in_array($this->apps->getAppInfo()['route'], $page['visible_on_apps'])) {
                        return $this->throwIdNotFound();
                    }
                    if ($this->apps->getAppInfo()['app_type'] !== $page['app_type']) {
                        return $this->throwIdNotFound();
                    }

                    $this->getQueryArr['id'] = null;//Add this to disable token generation on page view.

                    $this->view->mode = 'view';

                    unset($this->view->contentSources);
                    unset($this->view->apps);

                    if ($page['content_source'] === 'file') {
                        //Check file existence
                        try {
                            $path = str_replace(base_path(), '', $this->view->getViewsDir());

                            if ($this->localContent->fileExists($path . 'pages/files/' . $page['html_file'] . '.html')) {
                                $this->view->pick('pages/files/' . $page['html_file']);

                                return;
                            }

                            $this->setErrorDispatcher('templateError');

                            return;
                        } catch (\throwable | FilesystemException | UnableToCheckExistence $e) {
                            throw $e;
                        }
                    }

                    $page = $this->pages->processWidgets($page);

                    $this->view->setViewsDir($this->modules->views->getPhalconViewPath());
                }

                $this->view->page = $page;
            }

            $this->view->pick('pages/view');

            return;
        } else {
            if ($this->dispatcher->wasForwarded()) {
                $pageId = 0;

                if ($this->access->auth->account()) {
                    if (isset($this->app['settings']['defaultUserPage'])) {
                        $pageId = (int) $this->app['settings']['defaultUserPage'];
                    }
                } else {
                    if (isset($this->app['settings']['defaultGuestPage'])) {
                        $pageId = (int) $this->app['settings']['defaultGuestPage'];
                    }
                }

                $this->getQueryArr['id'] = $pageId;

                return $this->viewAction();
            }
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
            ['name', 'app_type', 'visible_on_apps'],
            true,
            ['name', 'app_type', 'visible_on_apps'],
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