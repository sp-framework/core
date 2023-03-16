<?php

namespace Apps\Dash\Components\System\Templates;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class TemplatesComponent extends BaseComponent
{
    use DynamicTable;

    protected $templates;

    public function initialize()
    {
        $this->templates = $this->basepackages->templates;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $componentsArr = $this->modules->components->components;

        $components = [];

        if ($componentsArr && count($componentsArr) > 0) {
            foreach ($componentsArr as $component) {
                $components[$component['id']]['id'] = $component['id'];
                $components[$component['id']]['name'] = $component['name'];
            }
        }

        $this->view->components = $components;

        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $template = $this->templates->getById($this->getData()['id']);

                if (!$template) {
                    return $this->throwIdNotFound();
                }

                $this->view->template = $template;
            }

            $this->view->pick('templates/view');

            return;
        }

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'edit'      => 'system/templates',
                    'remove'    => 'system/templates/remove'
                ]
            ];

        if ($this->request->isPost()) {
            $replaceColumns =
                function ($dataArr) {
                    if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                        return $this->replaceColumns($dataArr);
                    }

                    return $dataArr;
                };
        } else {
            $replaceColumns = null;
        }

        $this->generateDTContent(
            $this->templates,
            'system/templates/view',
            null,
            ['name', 'component_id', 'in_use'],
            true,
            ['name', 'component_id', 'in_use'],
            $controlActions,
            ['component_id' => 'component'],
            $replaceColumns,
            'name'
        );

        $this->view->pick('templates/list');
    }

    protected function replaceColumns($dataArr)
    {
        $componentsArr = $this->modules->components->components;

        $components = [];

        if ($componentsArr && count($componentsArr) > 0) {
            foreach ($componentsArr as $component) {
                $components[$component['id']] = $component['name'];
            }
        }

        foreach ($dataArr as $dataKey => &$data) {
            if (isset($components[$data['component_id']])) {
                $data['component_id'] = $components[$data['component_id']];
            }

            if ($data['in_use'] == '0') {
                $data['in_use'] = '<span class="badge badge-secondary">No</span>';
            } else if ($data['in_use'] == '1') {
                $data['in_use'] = '<span class="badge badge-success">Yes</span>';
            }
        }

        return $dataArr;
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        if ($this->request->hasFiles()) {

            if ($this->templates->storeFile()) {
                $this->view->templateData = $this->templates->packagesData->templateData;
            }

            $this->addResponse(
                $this->templates->packagesData->responseMessage,
                $this->templates->packagesData->responseCode
            );

            return;
        }

        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->templates->addTemplate($this->postData());

            $this->addResponse(
                $this->templates->packagesData->responseMessage,
                $this->templates->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
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

            $this->templates->updateTemplate($this->postData());

            $this->addResponse(
                $this->templates->packagesData->responseMessage,
                $this->templates->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name=remove)
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->templates->removeTemplate($this->postData());

            $this->addResponse(
                $this->templates->packagesData->responseMessage,
                $this->templates->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function testTemplateAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->templates->testTemplate($this->postData());

            $this->addResponse(
                $this->templates->packagesData->responseMessage,
                $this->templates->packagesData->responseCode,
                $this->templates->packagesData->responseData
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}