<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

use System\Base\Exceptions\FunctionParametersIncorrect;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Functions;

class ProcessImportExportQueue extends Functions
{
    public $funcName = 'Process Import/Export Queue';

    public function run(array $args = [])
    {
        $thisFunction = $this;

        return function() use ($thisFunction, $args) {
            $thisFunction->updateJobTask(2, $args);

            $this->parameters = $this->extractParameters($thisFunction, $args);

            if (!$this->parameters) {
                return;
            }

            if (!isset($this->parameters['process'])) {
                $thisFunction->packagesData->responseCode = 1;

                if (!isset($this->parameters['process'])) {
                    $thisFunction->packagesData->responseMessage = 'Parameters process missing';
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
                if ($this->parameters['process'] === 'export') {
                    $process = 'processExports';
                } else if ($this->parameters['process'] === 'import') {
                    $process = 'processImports';
                }

                if (!isset($process)) {
                    throw new FunctionParametersIncorrect('Task parameters "process" is not correct.');
                }

                if (method_exists($this->basepackages->importexport, $process)) {
                    $this->basepackages->importexport->{$process}($args['job']['id']);

                    $this->addJobResult($this->basepackages->importexport->packagesData, $args);
                } else {
                    throw new FunctionParametersIncorrect('Task parameters "method" is not correct.');
                }
            } catch (\Exception $e) {
                if ($this->config->logs->exceptions) {
                    $this->logger->logExceptions->debug($e);
                }

                $thisFunction->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';

                $thisFunction->packagesData->responseCode = 1;

                if (isset($this->basepackages->importexport->responseData)) {
                    $thisFunction->packagesData->responseData = $this->basepackages->importexport->responseData;
                } else if (isset($this->basepackages->importexport->processing)) {
                    $thisFunction->packagesData->responseData = ['lastProcessingID' => $this->basepackages->importexport->processing];
                }

                $this->addJobResult($thisFunction->packagesData, $args);

                $thisFunction->updateJobTask(3, $args);

                return;
            }

            $this->addJobResult($this->basepackages->importexport->packagesData, $args);

            $thisFunction->updateJobTask(3, $args);
        };
    }
}