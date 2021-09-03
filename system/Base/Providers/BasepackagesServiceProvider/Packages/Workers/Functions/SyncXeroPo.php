<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\PurchaseOrders;
use Phalcon\Helper\Json;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

class SyncXeroPo extends Functions
{
    public $funcName = "Sync Xero Purchase Orders";

    public function run(array $args = [])
    {
        set_time_limit(300);

        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            if (isset($args['task']['parameters']) && $args['task']['parameters'] !== '') {
                try {
                    $args['task']['parameters'] = Json::decode($args['task']['parameters'], true);
                } catch (\Exception $e) {

                    if ($e->getMessage() === "json_decode error: Syntax error") {
                        $thisFunction->packagesData->responseMessage = 'Task parameters format is incorrect. Make sure the format is json.';
                    } else {
                        $thisFunction->packagesData->responseMessage = $e->getMessage();
                    }

                    $thisFunction->packagesData->responseCode = 1;

                    $this->addJobResult($thisFunction->packagesData, $args);

                    $thisFunction->updateJobTask(3, $args);

                    return;
                }
            }

            $poSync = new PurchaseOrders;

            try {
                $poSync->sync(null, $args['task']['parameters']);

                $this->addJobResult($poSync->packagesData, $args);
            } catch (\Exception $e) {

                $thisFunction->packagesData->responseCode = 1;

                $thisFunction->packagesData->responseMessage = $e->getMessage();

                $this->addJobResult($thisFunction->packagesData, $args);

                $thisFunction->updateJobTask(4, $args);

                return;
            }

            $thisFunction->updateJobTask(3, $args);
        };
    }
}