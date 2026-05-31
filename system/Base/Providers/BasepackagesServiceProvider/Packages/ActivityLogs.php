<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\BasepackagesActivityLogs;

class ActivityLogs extends BasePackage
{
    protected $modelToUse = BasepackagesActivityLogs::class;

    protected $packageNameS = 'activitylogs';

    public $activityLogs;

    const ACTIVITY_TYPE_ADD = 1;

    const ACTIVITY_TYPE_UPDATE = 2;

    public function init(bool $resetCache = false)
    {
        return $this;
    }

    public function addLog($packageName, array $data, array $oldData = null)
    {
        $dataId = $data['id'];
        unset($data['id']);

        $data = $this->removeSessionToken($data);
        if (isset($data['package_name'])) {
            unset($data['package_name']);
        }

        if ($oldData) {
            if (isset($oldData['id'])) {
                unset($oldData['id']);
            }
            if (isset($oldData['package_name'])) {
                unset($oldData['package_name']);
            }

            $activityData = $this->getDifference($this->jsonData($data), $this->jsonData($oldData));

            if (count($activityData) === 0) {
                return true;//Nothing changed, so we do not add activity log.
            }

            //Check if there are any logs from before, if not, we change from type UPDATE to type ADD
            $this->getLogs(packageName: $packageName, packageRowId: $dataId, getCount: true);

            if (isset($this->packagesData->paginationCounters['total_items']) && $this->packagesData->paginationCounters['total_items'] > 0) {
                $log['activity_type'] = self::ACTIVITY_TYPE_UPDATE;
            } else {
                $log['activity_type'] = self::ACTIVITY_TYPE_ADD;
            }
        } else {
            $activityData = $data;

            $log['activity_type'] = self::ACTIVITY_TYPE_ADD;
        }

        if ($log['activity_type'] === self::ACTIVITY_TYPE_ADD) {
            foreach ($activityData as $activityDataKey => $activityDataValue) {
                if (is_null($activityDataValue) || $activityDataValue === '') {
                    unset($activityData[$activityDataKey]);
                }
            }
        }

        if (PHP_SAPI === 'cli') {
            $log['account_id'] = 0;//System
        } else {
            $account = $this->access->auth->account();

            if ($account) {
                $log['account_id'] = $account['id'];//User
            } else {
                $log['account_id'] = 0;//System
            }
        }

        $log['package_name'] = $packageName;

        $log['package_row_id'] = $dataId;

        if (isset($data['created_at'])) {
            $log['created_at'] = $data['created_at'];
        }

        $log['log'] = $activityData;

        if ($this->add($log, false)) {
            $this->addResponse('Activity Log Added');
        } else {
            $this->addResponse('Error Adding Activity Log', 1);
        }
    }

    public function getLogs($packageName, int $packageRowId, $postLink = null, bool $newFirst = true, $page = 1, $getCount = false)
    {
        $logsArr = [];

        if ($newFirst) {
            $order = 'id desc';
        } else {
            $order = 'id asc';
        }

        $pagedLogs = $this->getPaged(
            [
                'conditions'    => '-|package_name|equals|' . $packageName . '&and|package_row_id|equals|' . $packageRowId . '&',
                'order'         => $order,
                'limit'         => 5,
                'page'          => $page
            ]
        );

        $logsArr['data'] = [];

        if ($pagedLogs) {
            if ($getCount) {//$this->packagesData->paginationCounters
                return true;
            }

            $logsArr['data'] = $pagedLogs->getItems();
        }

        if (count($logsArr['data']) > 0) {
            foreach ($logsArr['data'] as $key => &$log) {
                unset($log['id']);
                unset($log['package_name']);
                unset($log['package_row_id']);

                if ($log['account_id'] != 0) {
                    $account = $this->basepackages->accounts->getAccountById($log['account_id']);

                    if ($account) {
                        $log['account_email'] = $account['email'];
                        $log['account_full_name'] = $account['contact']['full_name'];

                        if (isset($account['contact']['portrait'])) {
                            $log['account_portrait'] = '<img src="' . $this->links->url('system/storages/q/uuid/' . $account['contact']['portrait'] . '/w/30') . '" class="rounded-sm" style="position:relative;width:20px;" alt="User Image">';
                        } else if (isset($account['contact']['initials_avatar']['small'])) {
                            $log['account_portrait'] = '<img src="data:image/png;base64,' . $account['contact']['initials_avatar']['small'] . '" class="rounded-sm" style="position:relative;width:20px;" alt="User Avatar">';
                        } else {
                            $log['account_portrait'] = '<img src="' . $this->links->images('general/user.png') . '" class="rounded-sm" style="position:relative;width:20px;" alt="User Image">';;
                        }
                    } else {
                        $log['account_email'] = 'N/A';
                        $log['account_full_name'] = 'System';
                        $log['account_portrait'] = '';
                    }
                } else {
                    $log['account_email'] = 'N/A';
                    $log['account_full_name'] = 'System';
                }

                if (is_string($log['log']) && $log['log'] !== '') {
                    $log['log'] = $this->helper->decode($log['log'], true);
                }
            }

            if ($this->packagesData->paginationCounters) {
                $logsArr = array_replace($logsArr, ['paginationCounters' => $this->packagesData->paginationCounters]);
            }

            $logsArr['postLink'] = $postLink;
            $logsArr['id'] = $packageRowId;
            $logsArr['packageName'] = $packageName;
            $logsArr['postLink'] = $postLink;
        }

        return $logsArr;
    }

    protected function getDifference(array $data, array $oldData)
    {
        return array_diff_assoc($data, $oldData);
    }
}