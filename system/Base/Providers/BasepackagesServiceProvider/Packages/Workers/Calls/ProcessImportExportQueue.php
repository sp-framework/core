<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Exceptions\CallsParametersIncorrect;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessImportExportQueue extends Calls
{
    public $funcDisplayName = 'Process Import/Export Queue';

    public $funcDescription = 'Process import/export processes with this call.';

    protected $args;

    public function run(array $args = [])
    {
        $this->updateJobTask(2, $args);

        $this->args = $this->extractCallArgs($this, $args);

        if (!$this->args) {
            return;
        }

        if (!isset($this->args['process'])) {
            $this->packagesData->responseCode = 1;

            if (!isset($this->args['process'])) {
                $this->packagesData->responseMessage = 'Parameters process missing';
            }

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(3, $args);

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

            $this->packagesData->responseMessage = 'Exception: Please check exceptions log for more details.';

            $this->packagesData->responseCode = 1;

            if (isset($this->basepackages->importexport->responseData)) {
                $this->packagesData->responseData = $this->basepackages->importexport->responseData;
            } else if (isset($this->basepackages->importexport->processing)) {
                $this->packagesData->responseData = ['lastProcessingID' => $this->basepackages->importexport->processing];
            }

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(3, $args);

            return;
        }

        $this->addJobResult($this->basepackages->importexport->packagesData, $args);

        $this->updateJobTask(3, $args);
    }
}