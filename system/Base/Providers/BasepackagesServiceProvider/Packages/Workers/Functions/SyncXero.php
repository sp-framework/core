<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\ContactGroups;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Contacts;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Organisations;
use Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\PurchaseOrders;
use GuzzleHttp\Exception\ConnectException;
use Phalcon\Helper\Json;
use System\Base\Exceptions\FunctionParametersIncorrect;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Tasks;

class SyncXero extends Functions
{
    public $funcName = "Sync Xero";

    protected $parameters = null;

    public function run(array $args = [])
    {
        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            $this->parameters = $this->extractParameters($thisFunction, $args);

            if (!$this->parameters) {
                return;
            }

            if (!isset($this->parameters['process']) ||
                !isset($this->parameters['method'])
            ) {
                $thisFunction->packagesData->responseCode = 1;

                if (!isset($this->parameters['process'])) {
                    $thisFunction->packagesData->responseMessage = 'Parameters process missing';
                } else if (!isset($this->parameters['method'])) {
                    $thisFunction->packagesData->responseMessage = 'Parameters method missing';
                } else {
                    $thisFunction->packagesData->responseMessage = 'Parameters process/method missing';
                }

                $this->addJobResult($thisFunction->packagesData, $args);

                $thisFunction->updateJobTask(3, $args);

                return;
            }

            if (!isset($this->parameters['timeout'])) {
                set_time_limit(300);
            } else {
                set_time_limit($this->parameters['timeout']);
            }

            try {
                $sync = null;
                $scheduleChildProcesses = false;

                if ($this->parameters['process'] === 'contacts') {
                    $sync = new Contacts;
                } else if ($this->parameters['process'] === 'purchaseOrders') {
                    $sync = new PurchaseOrders;
                } else if ($this->parameters['process'] === 'contactGroups') {
                    $sync = new ContactGroups;
                } else if ($this->parameters['process'] === 'organisations') {
                    $sync = new Organisations;
                }

                if (!$sync) {
                    throw new FunctionParametersIncorrect('Task parameters "process" is not correct.');
                }

                if (method_exists($sync, $this->parameters['method'])) {
                    $scheduleChildProcesses = $sync->{$this->parameters['method']}(null, $this->parameters);

                    $this->addJobResult($sync->packagesData, $args);
                } else {
                    throw new FunctionParametersIncorrect('Task parameters "method" is not correct.');
                }
            } catch (\PDOException | ConnectException | \Exception $e) {
                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->debug($e);
                }

                $thisFunction->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';

                if (get_class($e) === 'GuzzleHttp\Exception\ConnectException') {
                    $message = $e->getMessage();

                    if ($this->parameters && $this->parameters['method'] === 'syncFromData') {
                        $this->scheduleChildProcesses();

                        $message = $message . '. Rescheduling Task.';
                    }

                    $thisFunction->packagesData->responseMessage = $message;
                } else if (get_class($e) === 'System\Base\Exceptions\FunctionParametersIncorrect') {
                    $thisFunction->packagesData->responseMessage = $e->getMessage();
                }

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
                $this->scheduleChildProcesses();
            }

            $thisFunction->updateJobTask(3, $args);
        };
    }

    protected function scheduleChildProcesses()
    {
        $taskPackage = new Tasks;

        $task = $taskPackage->findByParameter('syncFromData', 'method', 'syncxero');//(value, key, function)

        //This will run regardless you manually cancel the force next run. To cancel you have to cancel this before it executes a new cycle.
        if ($task && $task['force_next_run'] === null) {
            if (isset($this->parameters['processCount'])) {
                $count = $this->parameters['processCount'];
            } else {
                $count = 50;
            }

            $taskPackage->forceNextRun(
                [
                    'id'                => $task['id'],
                    'parameters'        =>
                    [
                        'process'       => $this->parameters['process'],
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
// {
//     "process": "contacts",
//     "method": "sync",
//     "1":
//     {
//         "Contacts":
//         {
//             "ContactID": ["40aa7206-dbd0-446c-b7a6-c05496ba5862"]
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

// {"process":"contacts","method":"sync","1":{"Contacts":{"modifiedSince":"2021-09-19 00:00:00"}}}
// {"process":"contacts","method":"sync","1":{"Contacts":{"ContactID":["3c3e4b8f-6fff-4d05-b4eb-aac8d2ec6671"]}}}
// INSERT INTO `basepackages_workers_tasks` (`id`, `name`, `description`, `function`, `schedule_id`, `is_external`, `is_raw`, `is_on_demand`, `priority`, `enabled`, `status`, `type`, `previous_run`, `next_run`, `force_next_run`, `parameters`, `email`, `result`) VALUES
// (6, 'Xero Sync (Data)', '', 'syncxero', 0, NULL, NULL, 1, 10, 0, 1, 1, '2021-09-25 08:42:06', '-', NULL, '{\"process\":\"contacts\",\"method\":\"syncFromData\",\"processCount\":50}', NULL, NULL),
// (7, 'Xero Sync (Contact Groups)', '', 'syncxero', 0, NULL, NULL, 1, 10, 0, 1, 1, '2021-09-10 18:38:01', '-', NULL, '{\"process\":\"contactGroups\",\"method\":\"sync\"}', NULL, NULL),
// (8, 'Xero Sync (Organisations)', '', 'syncxero', 0, NULL, NULL, 1, 10, 0, 1, 1, '2021-09-10 18:38:02', '-', NULL, '{\"process\":\"organisations\",\"method\":\"sync\"}', NULL, NULL);