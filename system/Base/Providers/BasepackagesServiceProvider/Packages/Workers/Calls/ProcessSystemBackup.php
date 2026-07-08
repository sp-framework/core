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

            if ($this->basepackages->backuprestore->packagesData->responseCode == 0) {
                if (isset($this->args['rclone_to_gdrive']) && $this->args['rclone_to_gdrive'] == 'true') {
                    if ((!isset($this->args['rclone_remote_drive']) || (isset($this->args['rclone_remote_drive']) && $this->args['rclone_remote_drive'] === '')) ||
                        (!isset($this->args['rclone_remote_path']) || (isset($this->args['rclone_remote_path']) && $this->args['rclone_remote_path'] === ''))
                    ) {
                        throw new \Exception('Rclone remote drive and remote path information missing');
                    }

                    if (isset($this->basepackages->backuprestore->packagesData->responseData['backupFile']) &&
                        $this->basepackages->backuprestore->packagesData->responseData['backupFile'] !== ''
                    ) {
                        $backupFile = $this->basepackages->backuprestore->packagesData->responseData['backupFile'];

                        if (command_exists('rclone')) {
                            try {
                                $filename = base_path('var/workers/output') . '/' . $args['task']['id'] . '-rclone.log';

                                exec('rclone copy ' . $backupFile . ' ' . $this->args['rclone_remote_drive'] . ':' . $this->args['rclone_remote_path'] . ' -v  2>&1', $output, $result);

                                file_put_contents($filename, implode("\n", $output));

                                if ($result === 0 && count($output) > 0) {
                                    $this->addResponse('Backup complete. File uploaded to Google Drive.', 0, ['backupFile' => $backupFile, 'rcloneOutput' => file_get_contents($filename)]);
                                } else {
                                    $this->addResponse('Backup complete and stored locally. File not uploaded to Google Drive.', 0, ['backupFile' => $backupFile, 'rcloneOutput' => file_get_contents($filename)]);
                                }

                                $this->addJobResult($this->packagesData, $args);

                                $this->updateJobTask(3, $args);
                            } catch (\throwable $e) {
                                throw $e;
                            }
                        } else {
                            throw new \Exception('Rclone is not installed on local system. Either install Rclone and configure it or disable rclone_to_gdrive in task parameters.');
                        }
                    }
                } else {
                    $this->addJobResult($this->basepackages->backuprestore->packagesData, $args);

                    $this->updateJobTask(3, $args);
                }
            } else {
                $this->addJobResult($this->basepackages->backuprestore->packagesData, $args);

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