<?php

namespace Apps\Core\Components\System\Tools\Importexport;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use Apps\Core\Packages\System\Tools\ImportExport\ImportExport;
use System\Base\BaseComponent;

class ImportexportComponent extends BaseComponent
{
    use DynamicTable;

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if (!isset($this->getData()['type'])) {
                $importexport['type'] = 'export';
            } else {
                $importexport['type'] = $this->getData()['type'];
            }

            if ($importexport['type'] === 'export') {
                $this->view->components = $this->modules->components->getExportComponents();
            } else if ($importexport['type'] === 'import') {
                $this->view->components = $this->modules->components->getImportComponents();
            }

            $this->view->fields = [];
            $this->view->filters = [];

            if ($this->getData()['id'] != 0) {
                $importexport = $this->basepackages->importexport->getById($this->getData()['id']);

                if ($importexport['type'] === 'export' && isset($importexport['component_id'])) {
                    $componentData = $this->getPackageFieldsAction($importexport['component_id']);

                    if (isset($componentData['fields'])) {
                        foreach ($componentData['fields'] as $fieldKey => $field) {
                            $fields[$fieldKey]['id'] = $fieldKey;
                            $fields[$fieldKey]['name'] = ucfirst($field);
                        }

                        $this->view->fields = $fields;
                    }

                    if (isset($componentData['filters'])) {
                        $this->view->filters = $componentData['filters'];
                    }

                    $importexport['fields'] = $this->helper->decode($importexport['fields'], true);

                    $importexport['email_to'] = implode(',', $this->helper->decode($importexport['email_to'], true));

                    if ($importexport['file'] && $importexport['file'] !== '') {
                        $importexport['file'] = $this->links->url('system/storages/q/uuid/' . $importexport['file']);
                    }
                } else if ($importexport['type'] === 'import' && isset($importexport['component_id'])) {
                    $importexport['email_to'] = implode(',', $this->helper->decode($importexport['email_to'], true));

                    if ($importexport['file'] && $importexport['file'] !== '') {
                        try {
                            $importexport['file'] = $this->helper->decode($importexport['file'], true);
                            $importexport['file'] = $importexport['file'][0];

                            $this->view->file = $this->basepackages->storages->getFileInfo($importexport['file']);

                            $importexport['file'] = $this->links->url('system/storages/q/uuid/' . $importexport['file']);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                    }
                }

                if (!$importexport) {
                    return $this->throwIdNotFound();
                }
            }

            $this->view->importexport = $importexport;

            $this->view->pick('importexport/view');

            $this->useStorage('private', ['allowed_file_mime_types' => ['text/csv']]);

            return;
        }

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

        $controlActions =
            [
                'actionsToEnable'       =>
                [
                    'view'      => 'system/tools/importexport',
                ]
            ];

        $this->generateDTContent(
            $this->basepackages->importexport,
            'system/tools/importexport/view',
            null,
            ['type', 'status', 'component_id', 'app_id', 'account_id', 'email_to', 'file'],
            true,
            ['type', 'status', 'component_id', 'app_id', 'account_id', 'email_to'],
            $controlActions,
            ['status'=>'status (ID)','component_id' => 'Component (ID)', 'app_id'=>' via app (ID)', 'account_id'=>'by user (ID)'],
            $replaceColumns,
            'id'
        );

        $this->view->pick('importexport/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            if ($data['status'] == '0') {
                $badge = 'secondary';
                $status = 'Scheduled';
            } else if ($data['status'] == '1') {
                $badge = 'info';
                $status = 'Running';
            } else if ($data['status'] == '2') {
                $badge = 'success';
                $status = 'Complete';
            } else if ($data['status'] == '3') {
                $badge = 'danger';
                $status = 'Error';
            }

            $data['status'] = '<span class="badge badge-' . $badge . ' text-uppercase">' . $status . '</span>';
            $data['type'] = '<span class="badge badge-primary text-uppercase">' . $data['type'] . '</span>';

            $component = $this->modules->components->getComponentById($data['component_id']);

            if ($component) {
                $data['component_id'] = $component['name'] . ' (' . $data['component_id'] . ')';
            }

            $app = $this->apps->getAppById($data['app_id']);

            if ($app) {
                $data['app_id'] = $app['name'] . ' (' . $data['app_id'] . ')';
            }

            if ($data['account_id'] != '0') {
                $account = $this->basepackages->accounts->getAccountById($data['account_id']);

                if ($account) {
                    $data['account_id'] = $account['profile']['full_name'] . ' (' . $data['account_id'] . ')';
                }
            } else {
                $data['account_id'] = 'System (' . $data['account_id'] . ')';
            }

            if ($data['email_to'] && $data['email_to'] !== '') {
                $data['email_to'] = $this->helper->decode($data['email_to'], true);

                $data['email_to'] = implode(',', $data['email_to']);
            }

            if ($data['file'] && $data['file'] !== '') {
                $data['file'] =
                    '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-access-' . $data['id'] . '" href="' .  $this->links->url('system/storages/q/uuid/' . $data['file']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $data['id'] . '" class="text-white btn btn-primary btn-xs rowAccess text-uppercase">
                        <i class="fas fa-fw fa-xs fa-download"></i>
                    </a>';
            } else {
                $data['file'] = '-';
            }
        }

        return $dataArr;
    }

    /**
     * @acl(name=add)
     */
    public function addAction()
    {
        $this->requestIsPost();

        $this->basepackages->importexport->addImportExport($this->postData());

        $this->addResponse(
            $this->basepackages->importexport->packagesData->responseMessage,
            $this->basepackages->importexport->packagesData->responseCode
        );
    }

    /**
     * @acl(name=update)
     */
    public function updateAction()
    {
        $this->requestIsPost();

        $this->basepackages->importexport->updateImportExport($this->postData());

        $this->addResponse(
            $this->basepackages->importexport->packagesData->responseMessage,
            $this->basepackages->importexport->packagesData->responseCode
        );
    }

    public function getPackageFieldsAction($componentId = null)
    {
        if ($this->request->isPost() || $componentId) {
            if ($this->request->isPost()) {
                $this->requestIsPost();
            }

            if (!$componentId && $this->postData()['id']) {
                $componentId = $this->postData()['id'];
            }

            if ($this->basepackages->importexport->getPackageFields($componentId)) {
                $fields = $this->basepackages->importexport->packagesData->fields;
            } else {
                $fields = null;
            }

            $account = $this->access->auth->account();

            if ($account) {
                $filters = $this->basepackages->filters->getFiltersForAccountAndComponent($account, $componentId);
            } else {
                $filters = $this->basepackages->filters->getFiltersForComponent($componentId);
            }

            $responseData = ['fields' => $fields, 'filters' => $filters];

            $this->addResponse(
                $this->basepackages->importexport->packagesData->responseMessage,
                $this->basepackages->importexport->packagesData->responseCode,
                $responseData
            );

            return $responseData;
        }
    }

    public function getStructureFileLinkAction()
    {
        $this->requestIsPost();

        $this->basepackages->importexport->getStructureFileLink($this->postData());

        $this->addResponse(
            $this->basepackages->importexport->packagesData->responseMessage,
            $this->basepackages->importexport->packagesData->responseCode,
            $this->basepackages->importexport->packagesData->responseData,
        );
    }

    public function readFileAction()
    {
        $this->requestIsPost();

        $this->basepackages->importexport->readFile($this->postData());

        $this->addResponse(
            $this->basepackages->importexport->packagesData->responseMessage,
            $this->basepackages->importexport->packagesData->responseCode,
            $this->basepackages->importexport->packagesData->responseData,
        );
    }

    public function processFileAction()
    {
        $this->requestIsPost();

        $this->basepackages->importexport->processFile($this->postData());

        $this->addResponse(
            $this->basepackages->importexport->packagesData->responseMessage,
            $this->basepackages->importexport->packagesData->responseCode,
            $this->basepackages->importexport->packagesData->responseData,
        );
    }
}