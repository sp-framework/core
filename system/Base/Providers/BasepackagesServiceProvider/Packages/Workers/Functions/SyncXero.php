<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\ContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Contacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\PurchaseOrders;
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
            } catch (\Exception $e) {
                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->debug($e);
                }

                $thisFunction->packagesData->responseCode = 1;

                $thisFunction->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';

                $this->addJobResult($thisFunction->packagesData, $args);

                $thisFunction->updateJobTask(4, $args);

                return;
            }

            if ($scheduleChildProcesses) {
                $taskPackage = new Tasks;

                $task = $taskPackage->findByParametersMethod('syncFromData');

                if ($task) {
                    $taskPackage->forceNextRun(
                        [
                            'id'            => $task['id'],
                            'parameters'    =>
                            [
                                'process'   => $parameters['process'],
                                'method'    => 'syncFromData',
                            ]
                        ]
                    );
                }
            }

            $thisFunction->updateJobTask(3, $args);
        };
    }
}