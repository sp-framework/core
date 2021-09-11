<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\ContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Contacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Organisations;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\PurchaseOrders;
use GuzzleHttp\Exception\ConnectException;
use Phalcon\Helper\Json;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Tasks;

class SyncXero extends Functions
{
    public $funcName = "Sync Xero";

    public function run(array $args = [])
    {
        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            $parameters = null;

            if (isset($args['task']['parameters']) && $args['task']['parameters'] !== '') {
                try {
                    $parameters = Json::decode($args['task']['parameters'], true);
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), "json_decode")) {
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

            if (!$parameters['process'] && !$parameters['method']) {
                $thisFunction->packagesData->responseCode = 1;

                $thisFunction->packagesData->responseMessage = 'Parameters process/method missing';

                $this->addJobResult($thisFunction->packagesData, $args);

                $thisFunction->updateJobTask(3, $args);

                return;
            }

            $sync = null;
            $scheduleChildProcesses = false;

            if ($parameters['process'] === 'contacts') {
                $sync = new Contacts;
            } else if ($parameters['process'] === 'purchaseOrders') {
                $sync = new PurchaseOrders;
            } else if ($parameters['process'] === 'contactGroups') {
                $sync = new ContactGroups;
            } else if ($parameters['process'] === 'organisations') {
                $sync = new Organisations;
            }

            if (!isset($parameters['timeout'])) {
                set_time_limit(300);
            } else {
                set_time_limit($parameters['timeout']);
            }

            try {
                if (isset($parameters['method'])) {
                    if (method_exists($sync, $parameters['method'])) {
                        $scheduleChildProcesses = $sync->{$parameters['method']}(null, $parameters);

                        $this->addJobResult($sync->packagesData, $args);
                    } else {
                        throw new \Exception('Parameters method not correct.');
                    }
                } else {
                    throw new \Exception('Parameters method not set.');
                }
            } catch (\PDOException | ConnectException | \Exception $e) {
                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->debug($e);
                }

                if (get_class($e) === 'GuzzleHttp\Exception\ConnectException') {
                    $message = $e->getMessage();

                    if ($parameters && $parameters['method'] === 'syncFromData') {
                        $this->scheduleChildProcesses($parameters);

                        $message = $message . '. Rescheduling Task.';
                    }

                    $thisFunction->packagesData->responseMessage = $message;
                }

                $thisFunction->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';

                $thisFunction->packagesData->responseCode = 1;

                if (isset($sync->packagesData->responseData)) {
                    $thisFunction->packagesData->responseData = $sync->packagesData->responseData;
                } else if (isset($sync->processing)) {
                    $thisFunction->packagesData->responseData = ['lastProcessingID' => $sync->processing];
                }

                $this->addJobResult($thisFunction->packagesData, $args);

                $thisFunction->updateJobTask(3, $args);

                return;
            }

            if ($scheduleChildProcesses) {
                $this->scheduleChildProcesses($parameters);
            }

            $thisFunction->updateJobTask(3, $args);
        };
    }

    protected function scheduleChildProcesses($parameters)
    {
        $taskPackage = new Tasks;

        $task = $taskPackage->findByParametersMethod('syncFromData');

        if (isset($parameters['processCount'])) {
            $count = $parameters['processCount'];
        } else {
            $count = 50;
        }

        if ($task) {
            $taskPackage->forceNextRun(
                [
                    'id'                => $task['id'],
                    'parameters'        =>
                    [
                        'process'       => $parameters['process'],
                        'method'        => 'syncFromData',
                        'processCount'  => $count
                    ]
                ]
            );
        }
    }
}
// Contacts Parameters -
// {
//     "process": "contacts",
//     "method": "sync",
//     "1":
//     {
//         "Contacts":
//         {
//             "modifiedSince": "2021-09-08 9:00:00"
//         }
//     }
// }
// Contacts SyncData -
// {
//     "method": "syncFromData"
// }
// Contact Groups -
// {
//     "process": "contactGroups",
//     "method": "sync"
// }