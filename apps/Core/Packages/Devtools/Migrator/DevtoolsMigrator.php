<?php

namespace Apps\Core\Packages\Devtools\Migrator;

use System\Base\BasePackage;

class DevtoolsMigrator extends BasePackage
{
    protected $settings = Settings::class;

    protected $packageName = 'devtoolsmigrator';

    protected $apiClient;

    protected $apiClientConfig;

    public $devtoolsmigrator;

    public function syncRepositories($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'OrganizationApi';
            $method = 'orgListRepos';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'ReposApi';
            $method = 'reposListForOrg';
        }

        $args =
            [
                $this->apiClientConfig['org_user']
            ];

        try {
            $repositories = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($repositories) {
            $this->addResponse('Repositories Synced', 0, ['repositories' => $repositories]);

            return ['repositories' => $repositories];
        }

        $this->addResponse('Error syncing repositories or no repositories configured.', 1);
    }

    public function syncMilestones($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueGetMilestonesList';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'IssuesApi';
            $method = 'issuesListMilestones';
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                $data['repository_id'],
                'all'
            ];

        try {
            $milestones = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($milestones) {
            $this->addResponse('Milestones Synced', 0, ['milestones' => $milestones]);

            return ['milestones' => $milestones];
        }

        $this->addResponse('Error syncing milestones or no milestones configured.', 1);
    }

    public function migrateMilestones($data)
    {
        $data['api_id'] = $data['source_api_id'];
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueGetMilestonesList';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'IssuesApi';
            $method = 'issuesListMilestones';
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                $data['source_repository_id']
            ];

        try {
            $milestones = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($milestones) {
            $this->apiClient = null;
            $data['api_id'] = $data['destination_api_id'];
            if (!$this->initApi($data)) {
                return false;
            }

            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                // $collection = 'IssueApi';
                // $method = 'issueGetMilestonesList';
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'IssuesApi';
                $method = 'issuesCreateMilestone';
            }

            foreach ($milestones as $milestone) {
                if (in_array($milestone['id'], $data['milestones'])) {
                    $args = [
                        $this->apiClientConfig['org_user'],
                        $data['destination_repository_id'],
                        [
                            'title'         => $milestone['title'],
                            'state'         => $milestone['state'],
                            'description'   => $milestone['description']
                        ]
                    ];

                    try {
                        $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
                    } catch (\throwable $e) {
                        if ($e->getCode() === 422) {//Ignore if already exists
                            continue;
                        }

                        $this->addResponse($e->getMessage(), 1);

                        return false;
                    }
                }
            }

            $this->addResponse('Migrated all milestones. Confirm with destination repository.');

            return true;
        }

        $this->addResponse('Error migrating milestones.', 1);
    }

    public function syncLabels($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueListLabels';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'IssuesApi';
            $method = 'issuesListLabelsForRepo';
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                $data['repository_id'],
            ];

        try {
            $labels = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getmessage(), 1);

            return false;
        }

        if ($labels) {
            $labelsArr = ['labels' => $labels];
            $this->addResponse('Labels Synced', 0, $labelsArr);

            return $labelsArr;
        }

        $this->addResponse('Error syncing labels or no labels configured.', 1);
    }

    public function migrateLabels($data)
    {
        $data['api_id'] = $data['source_api_id'];
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueListLabels';
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'IssuesApi';
            $method = 'issuesListLabelsForRepo';
        }

        $args =
            [
                $this->apiClientConfig['org_user'],
                $data['source_repository_id']
            ];

        try {
            $labels = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
        } catch (\throwable $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        if ($labels) {
            $this->apiClient = null;
            $data['api_id'] = $data['destination_api_id'];
            if (!$this->initApi($data)) {
                return false;
            }

            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                // $collection = 'IssueApi';
                // $method = 'issueListLabels';
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'IssuesApi';
                $method = 'issuesCreateLabel';
            }

            foreach ($labels as $label) {
                if (in_array($label['id'], $data['labels'])) {
                    $args = [
                        $this->apiClientConfig['org_user'],
                        $data['destination_repository_id'],
                        [
                            'name'          => $label['name'],
                            'color'         => $label['color'],
                            'description'   => $label['description']
                        ]
                    ];

                    try {
                        $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
                    } catch (\throwable $e) {
                        if ($e->getCode() === 422) {//Ignore if already exists
                            continue;
                        }

                        $this->addResponse($e->getStatuscode(), 1);

                        return false;
                    }
                }
            }

            $this->addResponse('Migrated all labels. Confirm with destination repository.');

            return true;
        }

        $this->addResponse('Error migrating labels.', 1);
    }

    public function syncIssues($data)
    {
        if (!$this->initApi($data)) {
            return false;
        }

        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
            $collection = 'IssueApi';
            $method = 'issueListIssues';
            $args =
                [
                    $this->apiClientConfig['org_user'],
                    $data['repository_id'],
                    null,
                    implode(',', $data['labels'])
                ];
        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
            $collection = 'IssuesApi';
            $method = 'issuesListForRepo';
            $args =
                [
                    $this->apiClientConfig['org_user'],
                    $data['repository_id'],
                    null,
                    null,
                    null,
                    null,
                    null,
                    implode(',', $data['labels'])
                ];
        }

        try {
            $issues = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

            if ($issues) {
                $this->addResponse('Issues Synced', 0, ['issues' => $issues]);

                return $issues;
            }

            $this->addResponse('No issues found with selected milestone/label', 1);
        } catch (\Exception $e) {
            $this->addResponse($e->getMessage(), 1);

            return false;
        }

        return true;
    }

    public function getAvailableApis($getAll = false, $returnApis = true)
    {
        $apisArr = [];

        if (!$getAll) {
            $package = $this->getPackage();
            if (isset($package['settings']) &&
                isset($package['settings']['api_clients']) &&
                is_array($package['settings']['api_clients']) &&
                count($package['settings']['api_clients']) > 0
            ) {
                foreach ($package['settings']['api_clients'] as $key => $clientId) {
                    $client = $this->basepackages->apiClientServices->getApiById($clientId);

                    if ($client) {
                        array_push($apisArr, $client);
                    }
                }
            }
        } else {
            $apisArr = $this->basepackages->apiClientServices->getAll()->apiClientServices;
        }

        if (count($apisArr) > 0) {
            foreach ($apisArr as $api) {
                if ($api['category'] === 'repos') {
                    $useApi = $this->basepackages->apiClientServices->useApi([
                            'config' =>
                                [
                                    'id'           => $api['id'],
                                    'category'     => $api['category'],
                                    'provider'     => $api['provider'],
                                    'checkOnly'    => true//Set this to check if the API exists and can be instantiated.
                                ]
                        ]);

                    if ($useApi) {
                        $apiConfig = $useApi->getApiConfig();

                        $apis[$api['id']]['id'] = $apiConfig['id'];
                        $apis[$api['id']]['name'] = $apiConfig['name'];
                        $apis[$api['id']]['data']['url'] = $apiConfig['repo_url'];
                    }
                }
            }
        }

        if ($returnApis) {
            return $apis;
        }

        return $apisArr;
    }

    protected function initApi($data)
    {
        if ($this->apiClient && $this->apiClientConfig) {
            return true;
        }

        if (!isset($data['api_id']) && !isset($data['name'])) {
            $this->addResponse('API information not provided', 1, []);

            return false;
        }

        if (isset($data['api_id']) && $data['api_id'] == '0' || $data['api_id'] === '') {
            $this->addResponse('Provide correct API ID, cannot sync.', 1, []);

            return false;
        }

        $this->apiClient = $this->basepackages->apiClientServices->useApi($data['api_id'], true);

        $this->apiClientConfig = $this->apiClient->getApiConfig();

        if ($this->apiClientConfig['auth_type'] === 'auth' &&
            ((!$this->apiClientConfig['username'] || $this->apiClientConfig['username'] === '') &&
            (!$this->apiClientConfig['password'] || $this->apiClientConfig['password'] === ''))
        ) {
            $this->addResponse('Username/Password missing, cannot sync', 1);

            return false;
        } else if ($this->apiClientConfig['auth_type'] === 'access_token' &&
                  (!$this->apiClientConfig['access_token'] || $this->apiClientConfig['access_token'] === '')
        ) {
            $this->addResponse('Access token missing, cannot sync', 1);

            return false;
        } else if ($this->apiClientConfig['auth_type'] === 'autho' &&
                  (!$this->apiClientConfig['authorization'] || $this->apiClientConfig['authorization'] === '')
        ) {
            $this->addResponse('Authorization token missing, cannot sync', 1);

            return false;
        }

        return true;
    }
}