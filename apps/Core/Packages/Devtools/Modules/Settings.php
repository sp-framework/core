<?php

namespace Apps\Core\Packages\Devtools\Modules;

use System\Base\BasePackage;

class Settings extends BasePackage
{
    public function onUpdate($data)
    {
        if (isset($data['api_clients'])) {
            if (is_string($data['api_clients']) && $data['api_clients'] !== '') {
                $data['api_clients'] = $this->helper->decode($data['api_clients'], true);
            }
        }

        if (count($data['api_clients']) > 0) {
            foreach ($data['api_clients'] as $apiId) {
                $api = $this->basepackages->apiClientServices->getApiById($apiId);

                if ($api) {
                    $api['in_use'] = 1;

                    if (isset($api['used_by'])) {
                        if (is_string($api['used_by']) && $api['used_by'] !== '') {
                            $api['used_by'] = $this->helper->decode($api['used_by'], true);
                        }
                    }

                    array_push($api['used_by'], 'devtoolsmodules');

                    $this->basepackages->apiClientServices->updateApi($api);
                }
            }
        }
    }
}