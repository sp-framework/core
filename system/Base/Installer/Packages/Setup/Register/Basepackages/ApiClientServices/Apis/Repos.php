<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\ApiClientServices\Apis;

class Repos
{
    public function register($db, $ff)
    {
        $coreRepo =
            [
                'api_url'               => 'https://api.github.com',
                'org_user'              => 'sp-framework',
                'repo_url'              => 'https://github.com/sp-framework/core',
                'branch'                => 'main',
                'auth_type'             => 'autho',
                'authorization'         => ''
            ];

        $coreApi =
            [
                'name'              => 'SP Framework Core',
                'description'       => 'SP Framework Core Repository',
                'category'          => 'repos',
                'provider'          => 'Github',
                'in_use'            => 0,
                'used_by'           => json_encode([]),
                'setup'             => 4,
                'location'          => 'basepackages'
            ];

        $modulesRepo =
            [
                'api_url'               => 'https://api.github.com',
                'org_user'              => 'sp-modules',
                'repo_url'              => 'https://github.com/sp-modules/',
                'branch'                => 'main',
                'auth_type'             => 'autho',
                'authorization'         => ''
            ];

        $modulesApi =
            [
                'name'                  => 'SP Framework Modules',
                'description'           => 'SP Framework Modules Repository',
                'category'              => 'repos',
                'provider'              => 'Github',
                'in_use'                => 0,
                'used_by'               => json_encode([]),
                'setup'                 => 4,
                'location'              => 'basepackages'
            ];

        if ($db) {
            $newRepo = $db->insertAsDict('basepackages_api_client_services_apis_repos', $coreRepo);

            if ($newRepo) {
                $coreApi['api_category_id'] = $db->lastInsertId();

                $db->insertAsDict('basepackages_api_client_services', $coreApi);
            }

            $newRepo = $db->insertAsDict('basepackages_api_client_services_apis_repos', $modulesRepo);

            if ($newRepo) {
                $modulesApi['api_category_id'] = $db->lastInsertId();

                $db->insertAsDict('basepackages_api_client_services', $modulesApi);
            }
        }

        if ($ff) {
            $apisReposStore = $ff->store('basepackages_api_client_services_apis_repos');
            $apiStore = $ff->store('basepackages_api_client_services');

            $newRepo = $apisReposStore->updateOrInsert($coreRepo);

            if ($newRepo) {
                $coreApi['api_category_id'] = $apisReposStore->getLastInsertedId();

                $apiStore->updateOrInsert($coreApi);
            }

            $newRepo = $apisReposStore->updateOrInsert($modulesRepo);

            if ($newRepo) {
                $modulesApi['api_category_id'] = $apisReposStore->getLastInsertedId();

                $apiStore->updateOrInsert($modulesApi);
            }
        }
    }
}