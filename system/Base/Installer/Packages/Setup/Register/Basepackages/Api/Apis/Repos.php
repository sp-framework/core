<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Api\Apis;

class Repos
{
    public function register($db)
    {
        $newApi = $db->insertAsDict(
            'basepackages_api_apis_repos',
            [
                'api_url'               => 'https://dev.bazaari.com.au/',
                'org_user'              => 'sp-core',
                'repo_url'              => 'https://dev.bazaari.com.au/sp-core',
                'branch'                => 'master',
                'auth_type'             => 'access_token',
                'access_token'          => '5b7987057a61adfe7be9994b5a5e8d569d385138'//bcust Token
            ]
        );


        if ($newApi) {
            $db->insertAsDict(
                'basepackages_api',
                [
                    'name'              => 'Bazaari Core (SP)',
                    'description'       => 'Bazaari Core Repository',
                    'api_category_id'   => $db->lastInsertId(),
                    'category'          => 'repos',
                    'provider'          => 'Gitea',
                    'in_use'            => 1,
                    'used_by'           => 'modules',
                    'setup'             => 4,
                    'location'          => 'system'
                ]
            );
        }

        $newApi = $db->insertAsDict(
            'basepackages_api_apis_repos',
            [
                'api_url'               => 'https://dev.bazaari.com.au/',
                'org_user'              => 'sp-modules',
                'repo_url'              => 'https://dev.bazaari.com.au/sp-modules',
                'branch'                => 'master',
                'auth_type'             => 'access_token',
                'access_token'          => '5b7987057a61adfe7be9994b5a5e8d569d385138'//bcust Token
            ]
        );

        if ($newApi) {
            $db->insertAsDict(
                'basepackages_api',
                [
                    'api_category_id'       => $db->lastInsertId(),
                    'name'                  => 'Bazaari Modules (SP)',
                    'description'           => 'Bazaari Modules Repository',
                    'category'              => 'repos',
                    'provider'              => 'Gitea',
                    'in_use'                => 1,
                    'used_by'               => 'modules',
                    'setup'                 => 4,
                    'location'              => 'system'
                ]
            );
        }
    }
}