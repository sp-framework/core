<?php

namespace Apps\Core\Components\Pages\Widgets;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class WidgetsComponent extends BaseComponent
{
    use DynamicTable;

    protected $pageswidgets;

    public function initialize()
    {
        $this->pageswidgets = $this->basepackages->pageswidgets;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        //In case of nested components, you need to disable parsing of main layout else view.html in pages directory will also be parsed.
        $this->view->disableLevel(\Phalcon\Mvc\View::LEVEL_MAIN_LAYOUT);

        if (isset($this->getData()['widgetinfo']) &&
            $this->getData()['widgetinfo'] == 'true'
        ) {
            return $this->basepackages->widgets->getWidget($this->getData()['id'], 'info')['info'];
        }

        if (isset($this->getData()['widgetsettings']) &&
            $this->getData()['widgetsettings'] == 'true'
        ) {
            return $this->basepackages->widgets->getWidget($this->getData()['id'], 'settings')['settings'];
        }

        $this->view->widgetsTree = $this->basepackages->widgets->getWidgetsTree('pages');

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $pagewidget = $this->pageswidgets->getById($this->getData()['id']);

                if (!$pagewidget) {
                    return $this->throwIdNotFound();
                }

                $pagewidget['info'] = $this->basepackages->widgets->getWidget($pagewidget['widget_id'], 'info')['info'];
                $pagewidget['settings'] = $this->basepackages->widgets->getWidget($pagewidget['widget_id'], 'settings', $pagewidget)['settings'];

                $this->view->pagewidget = $pagewidget;
            }

            $this->view->pick('widgets/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'pages/widgets',
                    'remove'    => 'pages/widgets/remove/'
                ]
            ];

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    foreach ($dataArr as &$data) {
                        if (isset($this->view->widgetsTree[$data['widget_id']])) {
                            $data['widget_id'] = $this->view->widgetsTree[$data['widget_id']]['name'];
                        }
                    }
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->pageswidgets,
            'pages/widgets/view',
            null,
            ['name', 'widget_id'],
            true,
            ['name', 'widget_id'],
            $controlActions,
            ['widget_id' => 'widget'],
            $replaceColumns,
            'name'
        );

        $this->view->pick('widgets/list');
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->pageswidgets->addPagesWidget($this->postData());

        $this->addResponse(
            $this->pageswidgets->packagesData->responseMessage,
            $this->pageswidgets->packagesData->responseCode,
            $this->pageswidgets->packagesData->responseData
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->pageswidgets->updatePagesWidget($this->postData());

        $this->addResponse(
            $this->pageswidgets->packagesData->responseMessage,
            $this->pageswidgets->packagesData->responseCode,
            $this->pageswidgets->packagesData->responseData
        );
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->pageswidgets->removePagesWidget($this->postData());

        $this->addResponse(
            $this->pageswidgets->packagesData->responseMessage,
            $this->pageswidgets->packagesData->responseCode
        );
    }
}