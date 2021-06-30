<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Phalcon\Helper\Json;
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
            $data = $this->getDifference($data, $oldData);

            $log['activity_type'] = self::ACTIVITY_TYPE_UPDATE;
        } else {
            $log['activity_type'] = self::ACTIVITY_TYPE_ADD;
        }

        $account = $this->auth->account();

        if ($account) {
            $log['account_id'] = $account['id'];//User
        } else {
            $log['account_id'] = 0;//System
        }

        $log['package_name'] = $packageName;

        $log['package_row_id'] = $dataId;

        $log['log'] = Json::encode($data);

        if ($this->add($log)) {
            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Activity Log Added';
        } else {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Error Adding Activity Log';
        }
    }

    public function getLogs($packageName, int $packageRowId, bool $newFirst)
    {
        $logsArr = $this->getByParams(
            [
                'conditions'    => 'package_name = :packageName: AND package_row_id = :packageRowId:',
                'bind'          =>
                    [
                        'packageName'   => $packageName,
                        'packageRowId'  => $packageRowId,
                    ]
            ]
        );

        if ($logsArr && count($logsArr) > 0) {
            foreach ($logsArr as $key => &$log) {
                unset($log['id']);
                unset($log['package_name']);
                unset($log['package_row_id']);

                if ($log['account_id'] != 0) {
                    $account = $this->basepackages->accounts->getById($log['account_id']);
                    $log['account_email'] = $account['email'];

                    $profile = $this->basepackages->profile->getProfile($log['account_id']);
                    $log['account_full_name'] = $profile['full_name'];

                    unset($log['account_id']);
                } else {
                    $log['account_email'] = 'N/A';
                    $log['account_full_name'] = 'System';
                }

                if ($log['log'] !== '') {
                    $log['log'] = Json::decode($log['log'], true);
                }
            }

            if ($newFirst && count($logsArr) > 1) {
                return array_reverse($logsArr);
            }

            return $logsArr;
        }

        return [];
    }

    protected function getDifference(array $data, array $oldData)
    {
        return array_diff_assoc($data, $oldData);
    }

    protected function removeSessionToken($data)
    {
        $token = array_keys($data, $this->security->getRequestToken());

        if ($token) {
            unset($data[$token[0]]);
        }

        return $data;
    }
}