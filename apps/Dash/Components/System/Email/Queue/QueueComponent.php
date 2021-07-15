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
                $emailservice = $this->emailqueue->getById($this->getData()['id']);

                $this->view->emailservice = $emailservice;
            }
            $this->view->pick('email/queue/view');

            return;
        }

        $emailqueue = $this->emailqueue->init();

        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'view'        => 'system/email/queue',
                    'edit'        => 'system/email/queue',
                    'remove'      => 'system/email/queue/remove',
                ]
            ];

        $conditions =
            [
                'conditions'    =>
                    '-:status:equals:0&',
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
            $emailqueue,
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
        }

        return $dataArr;
    }

    protected function formatStatus($rowId, $data)
    {
        if ($data['status'] == '0') {
            $data['status'] = '<span class="badge badge-primary text-uppercase">In Queue</span>';
        } else if ($data['status'] == '1') {
            $data['status'] = '<span class="badge badge-success text-uppercase">Sent</span>';
        } else if ($data['status'] == '2') {
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
}