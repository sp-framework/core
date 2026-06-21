<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;

class ProcessSystemBackup extends Calls
{
    public $funcDisplayName = 'Process System Backup';

    public $funcDescription = 'Process full system backup via this call.';

    protected $args;

    public function run(array $args = [])
    {
        $this->updateJobTask(2, $args);

        $this->args = $this->extractCallArgs($this, $args);

        if (!$this->args) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = 'Call function arguments missing';

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }

        //Perform backup here
        try {
            if ($this->args['notes'] === '') {
                $this->args['notes'] = 'Backup taken while processing task : ' . $args['task']['name'];
            }

            $backupInit = $this->basepackages->backuprestore->init('backup');

            if (!$backupInit) {
                throw new \Exception('Error initializing backup! Contact developer');
            }

            $backupInit->backup($this->args, true);

            $this->addJobResult($this->basepackages->backuprestore->packagesData, $args);

            if ($this->basepackages->backuprestore->packagesData->responseCode == 0) {
                $this->updateJobTask(3, $args);
            } else {
                $this->updateJobTask(4, $args);
            }
        } catch (\throwable $e) {
            $this->packagesData->responseCode = 1;

            $this->packagesData->responseMessage = $e->getMessage();

            $this->addJobResult($this->packagesData, $args);

            $this->updateJobTask(4, $args);

            return;
        }
    }
}