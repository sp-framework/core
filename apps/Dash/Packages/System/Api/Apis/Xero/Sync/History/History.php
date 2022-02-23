<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History;

use Apps\Dash\Packages\System\Api\Api;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\History\Model\SystemApiXeroHistory;
use System\Base\BasePackage;

class History extends BasePackage
{
    protected $apiPackage;

    protected $api;

    protected $xeroApi;

    protected $request;

    public function sync($apiId, $xeroPackage, $xeroPackageRowId, array $historyRecords)
    {
        $this->addUpdateXeroHistory($apiId, $xeroPackage, $xeroPackageRowId, $historyRecords);
    }

    public function addUpdateXeroHistory($apiId, $xeroPackage, $xeroPackageRowId, array $historyRecords)
    {
        if (count($historyRecords) > 0) {
            foreach ($historyRecords as $historyRecordKey => $historyRecord) {
                $model = SystemApiXeroHistory::class;

                $xeroHistory = $model::findFirst(
                    [
                        'conditions'    => 'xero_package_row_id = :xpri: AND DateUTCString = :dutcs:',
                        'bind'          =>
                            [
                                'xpri'  => $xeroPackageRowId,
                                'dutcs' => $historyRecord['DateUTCString']
                            ]
                    ]
                );

                $historyRecord['api_id'] = $apiId;
                $historyRecord['xero_package'] = $xeroPackage;
                $historyRecord['xero_package_row_id'] = $xeroPackageRowId;

                if (!$xeroHistory) {
                    $modelToUse = new $model();

                    $modelToUse->assign($historyRecord);

                    $modelToUse->create();
                } else {
                    $xeroHistory->assign($historyRecord);

                    $xeroHistory->update();
                }
            }
        }
    }

    public function reSync()
    {
        //
    }

    public function syncWithLocal()
    {
        //
    }
}