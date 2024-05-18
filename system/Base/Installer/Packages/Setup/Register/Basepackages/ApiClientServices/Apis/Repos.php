<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\ApiClientServices\Apis;

class Repos
{
    public function register($db, $ff)
    {
        $coreRepo =
            [
                'api_url'               => 'https://dev.bazaari.com.au/api/v1',
                'org_user'              => 'sp-core',
                'repo_url'              => 'https://dev.bazaari.com.au/sp-core',
                'branch'                => 'main',
                'auth_type'             => 'autho',
                'authorization'         => ''//bcust Token
            ];

        $coreApi =
            [
                'name'              => 'Bazaari Core (SP)',
                'description'       => 'Bazaari Core Repository',
                'category'          => 'repos',
                'provider'          => 'Gitea',
                'in_use'            => 1,
                'used_by'           => json_encode(['modules']),
                'setup'             => 4,
                'location'          => 'basepackages'
            ];

        $modulesRepo =
            [
                'api_url'               => 'https://dev.bazaari.com.au/api/v1',
                'org_user'              => 'sp-modules',
                'repo_url'              => 'https://dev.bazaari.com.au/sp-modules',
                'branch'                => 'main',
                'auth_type'             => 'autho',
                'authorization'         => ''//bcust Token
            ];

        $modulesApi =
            [
                'name'                  => 'Bazaari Modules (SP)',
                'description'           => 'Bazaari Modules Repository',
                'category'              => 'repos',
                'provider'              => 'Gitea',
                'in_use'                => 1,
                'used_by'               => json_encode(['modules']),
                'setup'                 => 4,
                'location'              => 'basepackages'
            ];

        $githubModulesRepo =
            [
                'api_url'               => 'https://api.github.com',
                'org_user'              => 'sp-modules',
                'repo_url'              => 'https://www.github.com/sp-modules',
                'branch'                => 'main',
                'auth_type'             => 'autho',
                'authorization'         => ''//github Token
            ];

        $githubModulesApi =
            [
                'name'                  => 'Github Modules (SP)',
                'description'           => 'Github Modules Repository',
                'category'              => 'repos',
                'provider'              => 'Github',
                'in_use'                => 1,
                'used_by'               => json_encode(['modules']),
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

            $newRepo = $db->insertAsDict('basepackages_api_client_services_apis_repos', $githubModulesRepo);

            if ($newRepo) {
                $githubModulesApi['api_category_id'] = $db->lastInsertId();

                $db->insertAsDict('basepackages_api_client_services', $githubModulesApi);
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

            $newRepo = $apisReposStore->updateOrInsert($githubModulesRepo);

            if ($newRepo) {
                $githubModulesApi['api_category_id'] = $apisReposStore->getLastInsertedId();

                $apiStore->updateOrInsert($githubModulesApi);
            }
        }
    }
}