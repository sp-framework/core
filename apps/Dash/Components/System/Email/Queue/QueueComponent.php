<?php

namespace Apps\Dash\Components\System\Email\Queue;

use Apps\Dash\Packages\AdminLTETags\Traits\DynamicTable;
use Phalcon\Helper\Json;
use System\Base\BaseComponent;

class QueueComponent extends BaseComponent
{
    use DynamicTable;

    protected $emailqueue;

    public function initialize()
    {
        $this->emailqueue = $this->basepackages->emailqueue;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        if (isset($this->getData()['id'])) {
            if ($this->getData()['id'] != 0) {
                $email = $this->emailqueue->getById($this->getData()['id']);

                if (!$email) {
                    return $this->throwIdNotFound();
                }

                $email = $this->formatStatus(0, $email);
                $email = $this->formatPriority(0, $email);
                $email = $this->formatSentOn(0, $email);
                $email = $this->formatToAddresses(0, $email);
                $email = $this->formatConfidential(0, $email);

                $this->view->email = $email;
            }
            return;
        }

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'view'        => 'system/email/queue'
                ]
            ];

        $conditions =
            [
                'conditions'    =>
                    '-:status:equals:1&',
                'order'         => 'id desc'
            ];

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    return $this->replaceColumns($dataArr);
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->emailqueue,
            'system/email/queue/view',
            $conditions,
            ['status', 'priority', 'sent_on', 'to_addresses', 'subject'],
            true,
            ['status', 'priority', 'sent_on', 'to_addresses', 'subject'],
            $controlActions,
            null,
            $replaceColumns,
            'name'
        );

        $this->view->pick('queue/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            $data = $this->formatStatus($dataKey, $data);
            $data = $this->formatPriority($dataKey, $data);
            $data = $this->formatSentOn($dataKey, $data);
            $data = $this->formatToAddresses($dataKey, $data);
            $data = $this->addRequeueButton($dataKey, $data);
        }

        return $dataArr;
    }

    protected function formatStatus($rowId, $data)
    {
        if ($data['status'] == '1') {
            $data['status'] = '<span class="badge badge-primary text-uppercase">In Queue</span>';
        } else if ($data['status'] == '2') {
            $data['status'] = '<span class="badge badge-success text-uppercase">Sent</span>';
        } else if ($data['status'] == '3') {
            $data['status'] = '<span class="badge badge-danger text-uppercase">Error</span>';
        }

        return $data;
    }

    protected function formatPriority($rowId, $data)
    {
        if ($data['priority'] == '1') {
            $data['priority'] = '<span class="badge badge-danger text-uppercase">High</span>';
        } else if ($data['priority'] == '2') {
            $data['priority'] = '<span class="badge badge-primary text-uppercase">Medium</span>';
        } else if ($data['priority'] == '3') {
            $data['priority'] = '<span class="badge badge-secondary text-uppercase">Low</span>';
        }

        return $data;
    }

    protected function formatSentOn($rowId, $data)
    {
        if (!$data['sent_on']) {
            $data['sent_on'] = '-';
        }

        return $data;
    }

    protected function formatToAddresses($rowId, $data)
    {
        $data['to_addresses'] = Json::decode($data['to_addresses'], true);

        $data['to_addresses'] = implode(',', $data['to_addresses']);

        return $data;
    }

    protected function formatConfidential($rowId, $data)
    {
        if ($data['confidential'] == '1') {
            $data['confidential'] = '<span class="badge badge-danger text-uppercase">Yes</span>';
            $data['body'] = "Confidential emails are encrypted on the server and cannot be viewed.";
        } else if ($data['confidential'] == '0') {
            $data['confidential'] = '<span class="badge badge-secondary text-uppercase">No</span>';
        }

        return $data;
    }

    protected function addRequeueButton($rowId, $data)
    {
        if ($data['status'] === '<span class="badge badge-danger text-uppercase">Error</span>') {
            $data['status'] .=
                '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-markread-' . $rowId . '" href="' . $this->links->url('system/email/queue/requeue') . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="ml-1 mr-1 pl-2 pr-2 text-white btn btn-primary btn-xs rowRequeue text-uppercase">
                    <i class="mr-1 fas fa-fw fa-xs fa-sync-alt"></i>
                    <span class="text-xs"> Requeue</span>
                </a>';
        }

        return $data;
    }

    /**
     * @acl(name="update")
     */
    public function changePriorityAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->emailqueue->changePriority($this->postData());

            $this->addResponse(
                $this->emailqueue->packagesData->responseMessage,
                $this->emailqueue->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        if ($this->request->isPost()) {

            $this->emailqueue->removeEmailService($this->postData());

            $this->view->responseCode = $this->emailqueue->packagesData->responseCode;

            $this->view->responseMessage = $this->emailqueue->packagesData->responseMessage;

        } else {
            $this->view->responseCode = 1;

            $this->view->responseMessage = 'Method Not Allowed';
        }
    }

    public function processQueueAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->emailqueue->processQueue();

            $this->addResponse(
                $this->emailqueue->packagesData->responseMessage,
                $this->emailqueue->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }

    public function requeueAction()
    {
        if ($this->request->isPost()) {
            if (!$this->checkCSRF()) {
                return;
            }

            $this->emailqueue->reQueue($this->postData());

            $this->addResponse(
                $this->emailqueue->packagesData->responseMessage,
                $this->emailqueue->packagesData->responseCode
            );
        } else {
            $this->addResponse('Method Not Allowed', 1);
        }
    }
}