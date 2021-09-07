<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\PurchaseOrders;
use Phalcon\Helper\Json;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

class SyncXeroPurchaseOrders extends Functions
{
    public $funcName = "Sync Xero Purchase Orders";

    public function run(array $args = [])
    {
        set_time_limit(300);

        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            $parameters = null;

            if (isset($args['task']['parameters']) && $args['task']['parameters'] !== '') {
                try {
                    $parameters = Json::decode($args['task']['parameters'], true);
                } catch (\Exception $e) {

                    if ($e->getMessage() === "json_decode error: Syntax error") {
                        $thisFunction->packagesData->responseMessage = 'Task parameters format is incorrect. Make sure the format is json.';
                    } else {
                        $thisFunction->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';
                    }

                    if ($this->config->logs->exceptions) {
                        $this->logger->logExceptions->debug($e);
                    }

                    $thisFunction->packagesData->responseCode = 1;

                    $this->addJobResult($thisFunction->packagesData, $args);

                    $thisFunction->updateJobTask(3, $args);

                    return;
                }
            }

            $poSync = new PurchaseOrders;

            try {
                $poSync->sync(null, $parameters);

                $this->addJobResult($poSync->packagesData, $args);
            } catch (\Exception $e) {

                $thisFunction->packagesData->responseCode = 1;

                $thisFunction->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';

                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->debug($e);
                }

                $this->addJobResult($thisFunction->packagesData, $args);

                $thisFunction->updateJobTask(4, $args);

                return;
            }

            $thisFunction->updateJobTask(3, $args);
        };
    }
}