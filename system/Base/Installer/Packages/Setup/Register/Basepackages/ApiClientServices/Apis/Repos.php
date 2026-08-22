<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package System\Base\Installer\Packages\Setup\Register\Basepackages\ApiClientServices\Apis
 * @copyright Copyright (c) 2026
 * @link      https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\ApiClientServices\Apis;

/**
 * Seeds default API client service endpoints and repository configurations.
 *
 * @package System\Base\Installer\Packages\Setup\Register\Basepackages\ApiClientServices\Apis
 */
class Repos
{
    /**
     * Registers default Core and Modules GitHub repository client entries.
     *
     * @param mixed                $db       PDO database connection adapter.
     * @param mixed                $ff       FlatFile database manager.
     * @param array<string, mixed> $postData Setup post payload.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, array $postData): void
    {
        $coreRepo = [
            'api_url'       => 'https://api.github.com',
            'org_user'      => 'sp-framework',
            'repo_url'      => 'https://github.com/sp-framework/core',
            'branch'        => 'main',
            'auth_type'     => 'autho',
            'authorization' => ''
        ];

        $usedBy = ['modules'];
        if (isset($postData['dev']) && (string) $postData['dev'] === 'true') {
            $usedBy[] = 'devtoolsmodules';
        }

        $coreApi = [
            'name'        => 'SP Framework Core',
            'description' => 'SP Framework Core Repository',
            'category'    => 'repos',
            'provider'    => 'Github',
            'in_use'      => 1,
            'used_by'     => json_encode($usedBy),
            'setup'       => 4,
            'app_type'    => 'core',
            'location'    => 'basepackages'
        ];

        $modulesRepo = [
            'api_url'       => 'https://api.github.com',
            'org_user'      => 'sp-modules',
            'repo_url'      => 'https://github.com/sp-modules',
            'branch'        => 'main',
            'auth_type'     => 'autho',
            'authorization' => ''
        ];

        $modulesApi = [
            'name'        => 'SP Framework Modules',
            'description' => 'SP Framework Modules Repository',
            'category'    => 'repos',
            'provider'    => 'Github',
            'in_use'      => 1,
            'used_by'     => json_encode($usedBy),
            'setup'       => 4,
            'app_type'    => 'core',
            'location'    => 'basepackages'
        ];

        if ($db) {
            $db->insertAsDict('basepackages_api_client_services_apis_repos', $coreRepo);
            $coreApi['api_category_id'] = $db->lastInsertId();
            $db->insertAsDict('basepackages_api_client_services', $coreApi);

            $db->insertAsDict('basepackages_api_client_services_apis_repos', $modulesRepo);
            $modulesApi['api_category_id'] = $db->lastInsertId();
            $db->insertAsDict('basepackages_api_client_services', $modulesApi);
        }

        if ($ff) {
            $reposStore = $ff->store('basepackages_api_client_services_apis_repos');
            $apisStore = $ff->store('basepackages_api_client_services');

            $reposStore->updateOrInsert($coreRepo);
            $coreApi['api_category_id'] = $reposStore->getLastInsertedId();
            $apisStore->updateOrInsert($coreApi);

            $reposStore->updateOrInsert($modulesRepo);
            $modulesApi['api_category_id'] = $reposStore->getLastInsertedId();
            $apisStore->updateOrInsert($modulesApi);
        }
    }
}