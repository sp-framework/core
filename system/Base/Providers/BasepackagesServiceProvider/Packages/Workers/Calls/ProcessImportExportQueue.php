<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Exceptions\CallsParametersIncorrect;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessImportExportQueue extends Calls
{
    public $funcName = 'Process Import/Export Queue';

    protected $args;

    public function run(array $args = [])
    {
        $thisCall = $this;

        return function() use ($thisCall, $args) {
            $thisCall->updateJobTask(2, $args);

            $this->args = $this->extractCallArgs($thisCall, $args);

            if (!$this->args) {
                return;
            }

            if (!isset($this->args['process'])) {
                $thisCall->packagesData->responseCode = 1;

                if (!isset($this->args['process'])) {
                    $thisCall->packagesData->responseMessage = 'Parameters process missing';
                }

                $this->addJobResult($thisCall->packagesData, $args);

                $thisCall->updateJobTask(3, $args);

                return;
            }

            if (!isset($this->args['timeout'])) {
                set_time_limit(300);
            } else {
                set_time_limit($this->args['timeout']);
            }

            try {
                if ($this->args['process'] === 'export') {
                    $process = 'processExports';
                } else if ($this->args['process'] === 'import') {
                    $process = 'processImports';
                }

                if (!isset($process)) {
                    throw new CallsParametersIncorrect('Task arguments "process" is not correct.');
                }

                if (method_exists($this->basepackages->importexport, $process)) {
                    $this->basepackages->importexport->{$process}($args['job']['id']);

                    $this->addJobResult($this->basepackages->importexport->packagesData, $args);
                } else {
                    throw new CallsParametersIncorrect('Task arguments "method" is not correct.');
                }
            } catch (\Exception $e) {
                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->critical(json_trace($e));
                }

                $thisCall->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';

                $thisCall->packagesData->responseCode = 1;

                if (isset($this->basepackages->importexport->responseData)) {
                    $thisCall->packagesData->responseData = $this->basepackages->importexport->responseData;
                } else if (isset($this->basepackages->importexport->processing)) {
                    $thisCall->packagesData->responseData = ['lastProcessingID' => $this->basepackages->importexport->processing];
                }

                $this->addJobResult($thisCall->packagesData, $args);

                $thisCall->updateJobTask(3, $args);

                return;
            }

            $this->addJobResult($this->basepackages->importexport->packagesData, $args);

            $thisCall->updateJobTask(3, $args);
        };
    }
}