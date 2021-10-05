<?php

namespace Apps\Dash\Packages\System\Tools\ImportExport;

use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;

class ImportExport extends BasePackage
{
    protected $importexportPackage;

    protected $importexportSettings = null;

    public function init()
    {
        $this->importexportPackage = $this->modules->packages->getNamePackage('ImportExport');

        if ($this->importexportPackage) {
            $this->importexportSettings = Json::decode($this->importexportPackage['settings'], true);

            return $this;
        }
    }

    public function getImportexportSettings()
    {
        if ($this->importexportSettings) {
            return $this->importexportSettings;
        } else {
            $this->init();
        }

        return $this->importexportSettings;
    }


}