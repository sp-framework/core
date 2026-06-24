<?php

namespace Apps\Core\Components\System\Mutex;

use Apps\Core\Packages\Adminltetags\Traits\DynamicTable;
use System\Base\BaseComponent;

class MutexComponent extends BaseComponent
{
    use DynamicTable;

    protected $mutex;

    public function initialize()
    {
        $this->mutex = $this->basepackages->mutex;
    }

    /**
     * @acl(name=view)
     */
    public function viewAction()
    {
        $controlActions =
            [
                // 'disableActionsForIds'  => [1],
                'actionsToEnable'       =>
                [
                    'remove'    => 'system/mutex/remove'
                ]
            ];

        $replaceColumns =
            function ($dataArr) {
                if ($dataArr && is_array($dataArr) && count($dataArr) > 0) {
                    return $this->replaceColumns($dataArr);
                }

                return $dataArr;
            };

        $this->generateDTContent(
            $this->mutex,
            'system/mutex/view',
            null,
            ['package_class', 'package_row_id', 'parent_lock_id', 'locked_at', 'account_id'],
            false,
            ['package_class', 'package_row_id', 'parent_lock_id', 'locked_at', 'account_id'],
            null,
            ['parent_lock_id' => 'Locked By (Mutex ID)', 'locked_at' => 'Locked At | Release At'],
            $replaceColumns,
            'package_row_id'
        );

        $this->view->pick('mutex/list');
    }

    protected function replaceColumns($dataArr)
    {
        foreach ($dataArr as $dataKey => &$data) {
            if (isset($data['locked_at']) && $data['locked_at'] > 0) {
                $releaseTime = $data['locked_at'] + $this->mutex->getTimeout();
                $data['locked_at'] = (\Carbon\Carbon::parse($data['locked_at']))->toAtomString();
                $data['locked_at'] = $data['locked_at'] . ' | ' . (\Carbon\Carbon::parse($releaseTime))->toAtomString();
            }

            $account = $this->basepackages->accounts->getAccountById($data['account_id']);

            if ($account && isset($account['contact']['full_name'])) {
                $data['account_id'] = $account['contact']['full_name'];
            }

            $data = $this->generateRemoveButton($dataKey, $data);
        }

        return $dataArr;
    }

    protected function generateRemoveButton($rowId, $data)
    {
        if ($data['parent_lock_id'] == '0') {
            $data['parent_lock_id'] =
                '<a id="' . strtolower($this->app['route']) . '-' . strtolower($this->componentName) . '-remove-__control-' . $rowId . '" href="' . $this->links->url('system/mutex/remove/q/id/' . $data['id']) . '" type="button" data-id="' . $data['id'] . '" data-rowid="' . $rowId . '" class="ml-1 mr-1 text-white btn btn-danger btn-xs rowRemove text-uppercase" data-notificationtextfromcolumn="package_row_id">
                    <i class="fas fa-fw fa-xs fa-trash"></i> Remove lock
                </a>';
        }

        return $data;
    }

    /**
     * @acl(name="remove")
     */
    public function removeAction()
    {
        $this->requestIsPost();

        $this->mutex->releaseMutex($this->postData());

        $this->addResponse(
            $this->mutex->packagesData->responseMessage,
            $this->mutex->packagesData->responseCode
        );
    }
}