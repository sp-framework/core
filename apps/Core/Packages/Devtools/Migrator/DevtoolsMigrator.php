<?php

namespace Apps\Core\Packages\Devtools\Migrator;

use Apps\Core\Packages\Devtools\Migrator\Install\Package;
use Apps\Core\Packages\Devtools\Migrator\Model\DevtoolsMigrator as DevtoolsMigratorModel;
use System\Base\BasePackage;

class DevtoolsMigrator extends BasePackage
{
    protected $modelToUse = DevtoolsMigratorModel::class;

    protected $settings = Settings::class;

    protected $packageName = 'devtoolsmigrator';

    protected $apiClient;

    protected $apiClientConfig;

    public $devtoolsmigrator;

    public function installPackage($redoDb = false)
    {
        $packageInstaller = new Package;

        return $packageInstaller->install($redoDb);
    }

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

    public function importIssues($data)
    {
        //Increase Exectimeout to 20 mins as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 3600) {
            set_time_limit(3600);
        }

        $this->setFFAddUsingUpdateOrInsert(true);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        if (!$this->initApi($data)) {
            return false;
        }

        $issue = true;

        // foreach ($this->getAll()->devtoolsmigrator as $issue) {
        //     $issue['migrated'] = false;
        //     $this->update($issue);
        // }
        // $this->addResponse('Updated');
        // return true;

        if (isset($data['import_from']) && (int) $data['import_from'] > 0) {
            $issueCounter = $data['import_from'];
        } else {
            $issueCounter = $this->ffStore->count(true);

            if ($issueCounter === 0) {
                $issueCounter = 1;
            } else if ($issueCounter > 1) {
                $issueCounter = $this->ffStore->getLastInsertedId() + 1;
            }
        }

        while ($issue !== false) {
            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'IssueApi';
                $method = 'issueGetIssue';
                $args =
                    [
                        $this->apiClientConfig['org_user'],
                        $data['repository_id'],
                        $issueCounter
                    ];
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $collection = 'IssuesApi';
                $method = 'issuesGet';
                $args =
                    [
                        $this->apiClientConfig['org_user'],
                        $data['repository_id'],
                        $issueCounter
                    ];
            }

            try {
                $issue = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

                if ($issue && isset($issue['pull_request'])) {
                    $issueCounter++;

                    continue;
                }

                if ($issue) {
                    $newIssue['id'] = $issue['number'];
                    $newIssue['api_id'] = $data['api_id'];
                    $newIssue['repository_id'] = $data['repository_id'];
                    $newIssue['source_issue_id'] = $issue['number'];
                    $newIssue['issue_details'] = $issue;
                    if (isset($issue['comments']) && $issue['comments'] > 0) {
                        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                            $collection = 'IssueApi';
                            $method = 'issueGetComments';
                            $args =
                                [
                                    $this->apiClientConfig['org_user'],
                                    $data['repository_id'],
                                    $issue['number']
                                ];
                        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                            $collection = 'IssuesApi';
                            $method = 'issuesListComments';
                            $args =
                                [
                                    $this->apiClientConfig['org_user'],
                                    $data['repository_id'],
                                    $issue['number']
                                ];
                        }

                        $newIssue['issue_comments'] = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
                    }

                    if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                        $collection = 'IssueApi';
                        $method = 'issueGetCommentsAndTimeline';
                        $args =
                            [
                                $this->apiClientConfig['org_user'],
                                $data['repository_id'],
                                $issue['number']
                            ];
                    } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                        $collection = 'IssuesApi';
                        $method = 'issuesListEventsForTimeline';
                        $args =
                            [
                                $this->apiClientConfig['org_user'],
                                $data['repository_id'],
                                $issue['number']
                            ];
                    }

                    $newIssue['issue_timeline'] = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

                    $this->add($newIssue);
                }
            } catch (\Exception $e) {
                if ($e->getCode() === 404) {
                    $issuesArr = $this->getPaged([
                        'conditions'    => '-|migrated|equals|0&',
                        'limit'         => 100
                    ])->getItems();

                    $issues = [];

                    if ($issuesArr && count($issuesArr) > 0) {
                        foreach ($issuesArr as $issue) {
                            $issues[$issue['issue_details']['number']]['number'] = $issue['issue_details']['number'];
                            $issues[$issue['issue_details']['number']]['html_url'] = $issue['issue_details']['html_url'];
                            $issues[$issue['issue_details']['number']]['title'] = $issue['issue_details']['title'];
                            $issues[$issue['issue_details']['number']]['migrated'] = $issue['migrated'];
                        }
                    }

                    $this->addResponse('Imported ' . ($issueCounter - 1). ' issues. If this is incorrect, import again!', 0, ['issues' => $issues]);

                    return true;
                }

                $this->addResponse('Issue at : ' . $issueCounter . '. ' . $e->getMessage(), 1);

                return false;
            }

            $issueCounter++;
        }

        return true;
    }

    public function migrateIssues($data)
    {
        //Increase Exectimeout to 20 mins as this process takes time to extract and merge data.
        if ((int) ini_get('max_execution_time') < 3600) {
            set_time_limit(3600);
        }

        $this->setFFAddUsingUpdateOrInsert(true);

        $this->ffStore = $this->ff->store($this->ffStoreToUse);

        if (!$this->initApi($data)) {
            return false;
        }

        if (empty($data['migrate_issues'])) {
            $this->addResponse('Provide issues to migrate.', 1);

            return false;
        }

        $data['migrate_issues'] = explode(',', trim($data['migrate_issues']));

        try {
            foreach ($data['migrate_issues'] as $key => $issue) {
                if (str_contains($issue, '-')) {
                    $issuesRange = explode('-', $issue);

                    if (count($issuesRange) !== 2) {
                        $this->addResponse('Incorrect Range', 1);

                        return false;
                    }

                    for ($issueRange = (int) $issuesRange[0]; $issueRange <= $issuesRange[1]; $issueRange++) {
                        $this->processMigrateIssue($data, (int) $issueRange);
                    }
                } else {
                    $this->processMigrateIssue($data, (int) $issue);
                }
            }

            $this->addResponse('Migrated all entered issues.');
        } catch (\throwable $e) {
            dump($e);die();
            $this->addResponse('Issue at : ' . $issueRange ?? $issue . '. ' . $e->getMessage(), 1);
        }
    }

    protected function processMigrateIssue($data, $issueNumber)
    {
        sleep(2);//github rate limit issue

        $issue = $this->getById($issueNumber);

        if ($issue && $issue['migrated'] !== true) {
            $issueDetails = '######## Issue imported from Gitea ########' . PHP_EOL;
            $issueDetails .= '# Details' . PHP_EOL;
            $issueDetails .= 'Gitea Issue ID : ' . $issue['issue_details']['number'] . PHP_EOL;
            $issueDetails .= 'State : ' . $issue['issue_details']['state'] . PHP_EOL;
            $issueDetails .= 'Created : ' . $issue['issue_details']['created_at'] . PHP_EOL;
            if ($issue['issue_details']['state'] === 'closed') {
                $issueDetails .= 'Closed : ' . $issue['issue_details']['closed_at'] . PHP_EOL . PHP_EOL;
            }
            $issueDetails .= '# Issue Description' . PHP_EOL;
            $issueDetails .= $issue['issue_details']['body'] . PHP_EOL . PHP_EOL;
            $issueDetails .= '# Timeline' . PHP_EOL;

            if ($issue['issue_timeline']) {
                foreach ($issue['issue_timeline'] as $timeline) {
                    if (isset($timeline['body']) && $timeline['body'] !== '') {
                        if ($timeline['type'] === 'label' && isset($timeline['label'])) {
                            $issueDetails .= 'Label : Added ' . $timeline['label']['name'] . ' on ' . $timeline['updated_at'] . '.' . PHP_EOL;
                        }
                        if ($timeline['type'] === 'commit_ref') {
                            if (str_contains($timeline['body'], '/sp-core/core')) {
                                $issueDetails .= 'Commit Reference: ' . str_replace('/sp-core/core', '/' . $this->apiClientConfig['org_user'] . '/' . $data['repository_id'], $timeline['body']) . PHP_EOL;
                            } else if (str_contains($timeline['body'], '/sp/core')) {
                                $issueDetails .= 'Commit Reference: ' . str_replace('/sp/core', '/' . $this->apiClientConfig['org_user'] . '/' . $data['repository_id'], $timeline['body']) . PHP_EOL;
                            }
                        }
                        if ($timeline['type'] === 'comment') {
                            //replace images to github repo images.
                            $timeline['body'] = str_replace('![image](/attachments/', '![image](https://github.com/sp-framework/gitea/raw/dev/images/', $timeline['body']);

                            $issueDetails .= 'Commented : ' . $timeline['body'] . PHP_EOL;
                        }
                    } else {
                        if (isset($timeline['type']) && $timeline['type'] === 'label' && isset($timeline['label'])) {
                            $issueDetails .= 'Label : Removed ' . $timeline['label']['name'] . ' on ' . $timeline['updated_at'] . '.' . PHP_EOL;
                        }
                        if (isset($timeline['type']) && $timeline['type'] === 'milestone' && isset($timeline['milestone'])) {
                            if (isset($timeline['old_milestone'])) {
                                $issueDetails .= 'Milestone : Changed from ' . $timeline['old_milestone']['title'] . ' to ' . $timeline['milestone']['title'] . ' on ' . $timeline['updated_at'] . '.' . PHP_EOL;
                            } else {
                                $issueDetails .= 'Milestone : Added ' . $timeline['milestone']['title'] . ' on ' . $timeline['updated_at'] . '.' . PHP_EOL;
                            }
                        }
                        if (isset($timeline['type']) && $timeline['type'] === 'close') {
                            $issueDetails .= 'Issue Closed on ' . $timeline['updated_at'] . '.' . PHP_EOL;
                        }
                    }
                }
            }

            if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                $collection = 'IssueApi';
                $method = 'issueCreateIssue';
                $args =
                    [
                        $this->apiClientConfig['org_user'],
                        $data['repository_id'],
                        // []
                    ];
            } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                $issueLabels = [];
                if (isset($issue['issue_details']['labels']) && count($issue['issue_details']['labels']) > 0) {
                    foreach ($issue['issue_details']['labels'] as $label) {
                        array_push($issueLabels, $label['name']);
                    }
                }

                $issueMilestones = [];
                $milestones = $this->syncMilestones($data);
                if ($milestones && isset($milestones['milestones'])) {
                    foreach ($milestones['milestones'] as $milestone) {
                        $issueMilestones[$milestone['title']] = $milestone['number'];
                    }
                }

                $collection = 'IssuesApi';
                $method = 'issuesCreate';
                $args =
                    [
                        $this->apiClientConfig['org_user'],
                        $data['repository_id'],
                        [
                            'title'     => $issue['issue_details']['title'],
                            'body'      => $issue['issue_details']['body'] . PHP_EOL . PHP_EOL . $issueDetails,
                            'assignee'  => 'oyeaussie',
                            'milestone' =>
                                isset($issue['issue_details']['milestone']) && isset($issueMilestones[$issue['issue_details']['milestone']['title']]) ?
                                $issueMilestones[$issue['issue_details']['milestone']['title']] :
                                null,
                            'labels'    => $issueLabels
                        ]
                    ];
            }

            try {
                $migratedIssue = $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);

                if ($migratedIssue) {
                    if ($issue['issue_details']['state'] === 'closed') {
                        if (strtolower($this->apiClientConfig['provider']) === 'gitea') {
                            $collection = 'IssueApi';
                            $method = 'issueEditIssue';
                            $args =
                                [
                                    $this->apiClientConfig['org_user'],
                                    $data['repository_id'],
                                    // []
                                ];
                        } else if (strtolower($this->apiClientConfig['provider']) === 'github') {
                            $collection = 'IssuesApi';
                            $method = 'issuesUpdate';
                            $args =
                                [
                                    $this->apiClientConfig['org_user'],
                                    $data['repository_id'],
                                    $migratedIssue['number'],
                                    [
                                        'state'     => 'closed'
                                    ]
                                ];
                        }

                        $this->apiClient->useMethod($collection, $method, $args)->getResponse(true);
                    }

                    $issue['migrated'] = true;

                    $this->update($issue);
                }

                return true;
            } catch (\Exception $e) {
                throw $e;
            }
        }

        if ($issueNumber > $this->ffStore->getLastInsertedId()) {
            throw new \Exception('Issue with ID ' . $issueNumber . ' not found in local database, please import issue to migrate');
        }
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