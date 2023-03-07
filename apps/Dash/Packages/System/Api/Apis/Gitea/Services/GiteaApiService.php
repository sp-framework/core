<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Services;

use Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Services\GiteaApiBaseService;

class GiteaApiService extends GiteaApiBaseService
{
    protected static $operations =
        [
        'AdminCronList' => [
          'method' => 'GET',
          'resource' => 'admin/cron',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCronListRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'AdminCronRun' => [
          'method' => 'POST',
          'resource' => 'admin/cron/{task}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCronRunRestResponse',
          'params' => [
            'task' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminGetAllOrgs' => [
          'method' => 'GET',
          'resource' => 'admin/orgs',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminGetAllOrgsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'AdminUnadoptedList' => [
          'method' => 'GET',
          'resource' => 'admin/unadopted',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminUnadoptedListRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
            'pattern' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'AdminAdoptRepository' => [
          'method' => 'POST',
          'resource' => 'admin/unadopted/{owner}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminAdoptRepositoryRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminDeleteUnadoptedRepository' => [
          'method' => 'DELETE',
          'resource' => 'admin/unadopted/{owner}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUnadoptedRepositoryRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminGetAllUsers' => [
          'method' => 'GET',
          'resource' => 'admin/users',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminGetAllUsersRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'AdminCreateUser' => [
          'method' => 'POST',
          'resource' => 'admin/users',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateUserRestResponse',
          'params' => [
          ],
        ],
        'AdminDeleteUser' => [
          'method' => 'DELETE',
          'resource' => 'admin/users/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUserRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminEditUser' => [
          'method' => 'PATCH',
          'resource' => 'admin/users/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminEditUserRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminCreatePublicKey' => [
          'method' => 'POST',
          'resource' => 'admin/users/{username}/keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreatePublicKeyRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminDeleteUserPublicKey' => [
          'method' => 'DELETE',
          'resource' => 'admin/users/{username}/keys/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUserPublicKeyRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminCreateOrg' => [
          'method' => 'POST',
          'resource' => 'admin/users/{username}/orgs',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateOrgRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminCreateRepo' => [
          'method' => 'POST',
          'resource' => 'admin/users/{username}/repos',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateRepoRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RenderMarkdown' => [
          'method' => 'POST',
          'resource' => 'markdown',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RenderMarkdownRestResponse',
          'params' => [
          ],
        ],
        'RenderMarkdownRaw' => [
          'method' => 'POST',
          'resource' => 'markdown/raw',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RenderMarkdownRawRestResponse',
          'params' => [
          ],
        ],
        'NotifyGetList' => [
          'method' => 'GET',
          'resource' => 'notifications',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetListRestResponse',
          'params' => [
            'all' => [
              'valid' => [
                'string',
              ],
            ],
            'status-types' => [
              'valid' => [
          'array',
              ],
            ],
            'since' => [
              'valid' => [
                'string',
              ],
            ],
            'before' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'NotifyReadList' => [
          'method' => 'PUT',
          'resource' => 'notifications',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadListRestResponse',
          'params' => [
            'last_read_at' => [
              'valid' => [
                'string',
              ],
            ],
            'all' => [
              'valid' => [
                'string',
              ],
            ],
            'status-types' => [
              'valid' => [
          'array',
              ],
            ],
            'to-status' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'NotifyNewAvailable' => [
          'method' => 'GET',
          'resource' => 'notifications/new',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyNewAvailableRestResponse',
          'params' => [
          ],
        ],
        'NotifyGetThread' => [
          'method' => 'GET',
          'resource' => 'notifications/threads/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetThreadRestResponse',
          'params' => [
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'NotifyReadThread' => [
          'method' => 'PATCH',
          'resource' => 'notifications/threads/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadThreadRestResponse',
          'params' => [
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'to-status' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'CreateOrgRepoDeprecated' => [
          'method' => 'POST',
          'resource' => 'org/{org}/repos',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateOrgRepoDeprecatedRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgGetAll' => [
          'method' => 'GET',
          'resource' => 'orgs',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetAllRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgCreate' => [
          'method' => 'POST',
          'resource' => 'orgs',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateRestResponse',
          'params' => [
          ],
        ],
        'OrgGet' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDelete' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgEdit' => [
          'method' => 'PATCH',
          'resource' => 'orgs/{org}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListHooks' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/hooks',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListHooksRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgCreateHook' => [
          'method' => 'POST',
          'resource' => 'orgs/{org}/hooks/',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateHookRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgGetHook' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/hooks/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetHookRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDeleteHook' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}/hooks/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteHookRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgEditHook' => [
          'method' => 'PATCH',
          'resource' => 'orgs/{org}/hooks/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditHookRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListLabels' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/labels',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListLabelsRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgCreateLabel' => [
          'method' => 'POST',
          'resource' => 'orgs/{org}/labels',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateLabelRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgGetLabel' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/labels/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetLabelRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDeleteLabel' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}/labels/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteLabelRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgEditLabel' => [
          'method' => 'PATCH',
          'resource' => 'orgs/{org}/labels/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditLabelRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListMembers' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/members',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListMembersRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgIsMember' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/members/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgIsMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDeleteMember' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}/members/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListPublicMembers' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/public_members',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListPublicMembersRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgIsPublicMember' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/public_members/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgIsPublicMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgPublicizeMember' => [
          'method' => 'PUT',
          'resource' => 'orgs/{org}/public_members/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgPublicizeMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgConcealMember' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}/public_members/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgConcealMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListRepos' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/repos',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListReposRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreateOrgRepo' => [
          'method' => 'POST',
          'resource' => 'orgs/{org}/repos',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateOrgRepoRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListTeams' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/teams',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamsRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgCreateTeam' => [
          'method' => 'POST',
          'resource' => 'orgs/{org}/teams',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateTeamRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'TeamSearch' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/teams/search',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\TeamSearchRestResponse',
          'params' => [
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'q' => [
              'valid' => [
                'string',
              ],
            ],
            'include_desc' => [
              'valid' => [
          'boolean',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'IssueSearchIssues' => [
          'method' => 'GET',
          'resource' => 'repos/issues/search',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueSearchIssuesRestResponse',
          'params' => [
            'state' => [
              'valid' => [
                'string',
              ],
            ],
            'labels' => [
              'valid' => [
                'string',
              ],
            ],
            'q' => [
              'valid' => [
                'string',
              ],
            ],
            'priority_repo_id' => [
              'valid' => [
          'integer',
              ],
            ],
            'type' => [
              'valid' => [
                'string',
              ],
            ],
            'since' => [
              'valid' => [
                'string',
              ],
            ],
            'before' => [
              'valid' => [
                'string',
              ],
            ],
            'assigned' => [
              'valid' => [
          'boolean',
              ],
            ],
            'created' => [
              'valid' => [
          'boolean',
              ],
            ],
            'mentioned' => [
              'valid' => [
          'boolean',
              ],
            ],
            'review_requested' => [
              'valid' => [
          'boolean',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoMigrate' => [
          'method' => 'POST',
          'resource' => 'repos/migrate',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMigrateRestResponse',
          'params' => [
          ],
        ],
        'RepoSearch' => [
          'method' => 'GET',
          'resource' => 'repos/search',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSearchRestResponse',
          'params' => [
            'q' => [
              'valid' => [
                'string',
              ],
            ],
            'topic' => [
              'valid' => [
          'boolean',
              ],
            ],
            'includeDesc' => [
              'valid' => [
          'boolean',
              ],
            ],
            'uid' => [
              'valid' => [
          'integer',
              ],
            ],
            'priority_owner_id' => [
              'valid' => [
          'integer',
              ],
            ],
            'team_id' => [
              'valid' => [
          'integer',
              ],
            ],
            'starredBy' => [
              'valid' => [
          'integer',
              ],
            ],
            'private' => [
              'valid' => [
          'boolean',
              ],
            ],
            'is_private' => [
              'valid' => [
          'boolean',
              ],
            ],
            'template' => [
              'valid' => [
          'boolean',
              ],
            ],
            'archived' => [
              'valid' => [
          'boolean',
              ],
            ],
            'mode' => [
              'valid' => [
                'string',
              ],
            ],
            'exclusive' => [
              'valid' => [
          'boolean',
              ],
            ],
            'sort' => [
              'valid' => [
                'string',
              ],
            ],
            'order' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoGet' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDelete' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEdit' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetArchive' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/archive/{archive}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetArchiveRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'archive' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListBranchProtection' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/branch_protections',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreateBranchProtection' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/branch_protections',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetBranchProtection' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/branch_protections/{name}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteBranchProtection' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/branch_protections/{name}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditBranchProtection' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/branch_protections/{name}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListBranches' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/branches',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListBranchesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoCreateBranch' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/branches',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateBranchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetBranch' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/branches/{branch}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetBranchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'branch' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteBranch' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/branches/{branch}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteBranchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'branch' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListCollaborators' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/collaborators',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListCollaboratorsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoCheckCollaborator' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/collaborators/{collaborator}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCheckCollaboratorRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'collaborator' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoAddCollaborator' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/collaborators/{collaborator}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddCollaboratorRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'collaborator' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteCollaborator' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/collaborators/{collaborator}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteCollaboratorRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'collaborator' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetAllCommits' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/commits',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetAllCommitsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoGetCombinedStatusByRef' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/commits/{ref}/status',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetCombinedStatusByRefRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoListStatusesByRef' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/commits/{ref}/statuses',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStatusesByRefRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sort' => [
              'valid' => [
                'string',
              ],
            ],
            'state' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoGetContentsList' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/contents',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetContentsListRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'RepoGetContents' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/contents/{filepath}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetContentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'RepoUpdateFile' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/contents/{filepath}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdateFileRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreateFile' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/contents/{filepath}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateFileRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteFile' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/contents/{filepath}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteFileRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetEditorConfig' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/editorconfig/{filepath}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetEditorConfigRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'ListForks' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/forks',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\ListForksRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreateFork' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/forks',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateForkRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetBlob' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/blobs/{sha}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetBlobRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetSingleCommit' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/commits/{sha}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetSingleCommitRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListAllGitRefs' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/refs',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListAllGitRefsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListGitRefs' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/refs/{ref}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListGitRefsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetTag' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/tags/{sha}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetTree' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/trees/{sha}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetTreeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'recursive' => [
              'valid' => [
          'boolean',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoListHooks' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/hooks',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListHooksRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoCreateHook' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/hooks',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListGitHooks' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/hooks/git',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListGitHooksRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetGitHook' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/hooks/git/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetGitHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteGitHook' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/hooks/git/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteGitHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditGitHook' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/hooks/git/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditGitHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetHook' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/hooks/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteHook' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/hooks/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditHook' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/hooks/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoTestHook' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/hooks/{id}/tests',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTestHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetIssueTemplates' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issue_templates',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetIssueTemplatesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueListIssues' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueListIssuesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'state' => [
              'valid' => [
                'string',
              ],
            ],
            'labels' => [
              'valid' => [
                'string',
              ],
            ],
            'q' => [
              'valid' => [
                'string',
              ],
            ],
            'type' => [
              'valid' => [
                'string',
              ],
            ],
            'milestones' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'IssueCreateIssue' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateIssueRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetRepoComments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/comments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetRepoCommentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'since' => [
              'valid' => [
                'string',
              ],
            ],
            'before' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'IssueGetComment' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteComment' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditComment' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditCommentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetCommentReactions' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/reactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentReactionsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssuePostCommentReaction' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/reactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssuePostCommentReactionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteCommentReaction' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/reactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentReactionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetIssue' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetIssueRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditIssue' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/issues/{index}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditIssueRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetComments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/comments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'since' => [
              'valid' => [
                'string',
              ],
            ],
            'before' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'IssueCreateComment' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/comments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateCommentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteCommentDeprecated' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/comments/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentDeprecatedRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditCommentDeprecated' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/comments/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditCommentDeprecatedRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditIssueDeadline' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/deadline',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditIssueDeadlineRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetLabels' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetLabelsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueReplaceLabels' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueReplaceLabelsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueAddLabel' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueClearLabels' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueClearLabelsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueRemoveLabel' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueRemoveLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetIssueReactions' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/reactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetIssueReactionsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'IssuePostIssueReaction' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/reactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssuePostIssueReactionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteIssueReaction' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/reactions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteIssueReactionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteStopWatch' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/stopwatch/delete',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteStopWatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueStartStopWatch' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/stopwatch/start',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueStartStopWatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueStopStopWatch' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/stopwatch/stop',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueStopStopWatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueSubscriptions' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/subscriptions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueSubscriptionsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'IssueCheckSubscription' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/subscriptions/check',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCheckSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueAddSubscription' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/subscriptions/{user}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteSubscription' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/subscriptions/{user}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueTrackedTimes' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/times',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueTrackedTimesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
                'string',
              ],
            ],
            'since' => [
              'valid' => [
                'string',
              ],
            ],
            'before' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'IssueAddTime' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/times',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddTimeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueResetTime' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/times',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueResetTimeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteTime' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/times/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteTimeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListKeys' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListKeysRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'key_id' => [
              'valid' => [
          'integer',
              ],
            ],
            'fingerprint' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoCreateKey' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateKeyRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetKey' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/keys/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetKeyRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteKey' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/keys/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteKeyRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueListLabels' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/labels',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueListLabelsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'IssueCreateLabel' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/labels',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetLabel' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/labels/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteLabel' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/labels/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditLabel' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/labels/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetLanguages' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/languages',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetLanguagesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetMilestonesList' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/milestones',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetMilestonesListRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'state' => [
              'valid' => [
                'string',
              ],
            ],
            'name' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'IssueCreateMilestone' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/milestones',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateMilestoneRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetMilestone' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/milestones/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetMilestoneRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteMilestone' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/milestones/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteMilestoneRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditMilestone' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/milestones/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditMilestoneRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoMirrorSync' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/mirror-sync',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMirrorSyncRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'NotifyGetRepoList' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/notifications',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetRepoListRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'all' => [
              'valid' => [
                'string',
              ],
            ],
            'status-types' => [
              'valid' => [
          'array',
              ],
            ],
            'since' => [
              'valid' => [
                'string',
              ],
            ],
            'before' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'NotifyReadRepoList' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/notifications',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadRepoListRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'all' => [
              'valid' => [
                'string',
              ],
            ],
            'status-types' => [
              'valid' => [
          'array',
              ],
            ],
            'to-status' => [
              'valid' => [
                'string',
              ],
            ],
            'last_read_at' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'RepoListPullRequests' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListPullRequestsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'state' => [
              'valid' => [
                'string',
              ],
            ],
            'sort' => [
              'valid' => [
                'string',
              ],
            ],
            'milestone' => [
              'valid' => [
          'integer',
              ],
            ],
            'labels' => [
              'valid' => [
          'array',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoCreatePullRequest' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetPullRequest' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditPullRequest' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditPullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDownloadPullDiff' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}.diff',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDownloadPullDiffRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDownloadPullPatch' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}.patch',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDownloadPullPatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoPullRequestIsMerged' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/merge',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoPullRequestIsMergedRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoMergePullRequest' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/merge',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMergePullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreatePullReviewRequests' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/requested_reviewers',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullReviewRequestsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeletePullReviewRequests' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/requested_reviewers',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeletePullReviewRequestsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListPullReviews' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListPullReviewsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoCreatePullReview' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetPullReview' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoSubmitPullReview' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSubmitPullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeletePullReview' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeletePullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetPullReviewComments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}/comments',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullReviewCommentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDismissPullReview' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}/dismissals',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDismissPullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoUnDismissPullReview' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}/undismissals',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUnDismissPullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoUpdatePullRequest' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/update',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdatePullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetRawFile' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/raw/{filepath}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetRawFileRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'RepoListReleases' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListReleasesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'per_page' => [
              'valid' => [
          'integer',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoCreateRelease' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/releases',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetReleaseByTag' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/tags/{tag}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseByTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'tag' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteReleaseByTag' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/releases/tags/{tag}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseByTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'tag' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetRelease' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteRelease' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/releases/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditRelease' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/releases/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListReleaseAttachments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListReleaseAttachmentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreateReleaseAttachment' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateReleaseAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'RepoGetReleaseAttachment' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets/{attachment_id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteReleaseAttachment' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets/{attachment_id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditReleaseAttachment' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets/{attachment_id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditReleaseAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoSigningKey' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/signing-key.gpg',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSigningKeyRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListStargazers' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/stargazers',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStargazersRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoListStatuses' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/statuses/{sha}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStatusesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sort' => [
              'valid' => [
                'string',
              ],
            ],
            'state' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoCreateStatus' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/statuses/{sha}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateStatusRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListSubscribers' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/subscribers',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListSubscribersRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentCheckSubscription' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/subscription',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentPutSubscription' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/subscription',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteSubscription' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/subscription',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListTags' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/tags',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTagsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoDeleteTag' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/tags/{tag}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'tag' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListTeams' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/teams',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTeamsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCheckTeam' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/teams/{team}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCheckTeamRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'team' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoAddTeam' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/teams/{team}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddTeamRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'team' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteTeam' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/teams/{team}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTeamRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'team' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoTrackedTimes' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/times',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTrackedTimesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
                'string',
              ],
            ],
            'since' => [
              'valid' => [
                'string',
              ],
            ],
            'before' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserTrackedTimes' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/times/{user}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserTrackedTimesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListTopics' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/topics',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTopicsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'RepoUpdateTopics' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/topics',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdateTopicsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoAddTopíc' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/topics/{topic}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddTopícRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'topic' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteTopic' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/topics/{topic}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTopicRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'topic' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoTransfer' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/transfer',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTransferRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetByID' => [
          'method' => 'GET',
          'resource' => 'repositories/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetByIDRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetGeneralAPISettings' => [
          'method' => 'GET',
          'resource' => 'settings/api',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralAPISettingsRestResponse',
          'params' => [
          ],
        ],
        'GetGeneralAttachmentSettings' => [
          'method' => 'GET',
          'resource' => 'settings/attachment',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralAttachmentSettingsRestResponse',
          'params' => [
          ],
        ],
        'GetGeneralRepositorySettings' => [
          'method' => 'GET',
          'resource' => 'settings/repository',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralRepositorySettingsRestResponse',
          'params' => [
          ],
        ],
        'GetGeneralUISettings' => [
          'method' => 'GET',
          'resource' => 'settings/ui',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralUISettingsRestResponse',
          'params' => [
          ],
        ],
        'GetSigningKey' => [
          'method' => 'GET',
          'resource' => 'signing-key.gpg',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetSigningKeyRestResponse',
          'params' => [
          ],
        ],
        'OrgGetTeam' => [
          'method' => 'GET',
          'resource' => 'teams/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetTeamRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDeleteTeam' => [
          'method' => 'DELETE',
          'resource' => 'teams/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteTeamRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgEditTeam' => [
          'method' => 'PATCH',
          'resource' => 'teams/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditTeamRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListTeamMembers' => [
          'method' => 'GET',
          'resource' => 'teams/{id}/members',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamMembersRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgListTeamMember' => [
          'method' => 'GET',
          'resource' => 'teams/{id}/members/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamMemberRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgAddTeamMember' => [
          'method' => 'PUT',
          'resource' => 'teams/{id}/members/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgAddTeamMemberRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgRemoveTeamMember' => [
          'method' => 'DELETE',
          'resource' => 'teams/{id}/members/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgRemoveTeamMemberRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListTeamRepos' => [
          'method' => 'GET',
          'resource' => 'teams/{id}/repos',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamReposRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgAddTeamRepository' => [
          'method' => 'PUT',
          'resource' => 'teams/{id}/repos/{org}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgAddTeamRepositoryRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgRemoveTeamRepository' => [
          'method' => 'DELETE',
          'resource' => 'teams/{id}/repos/{org}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgRemoveTeamRepositoryRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
            'org' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'TopicSearch' => [
          'method' => 'GET',
          'resource' => 'topics/search',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\TopicSearchRestResponse',
          'params' => [
            'q' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserGetCurrent' => [
          'method' => 'GET',
          'resource' => 'user',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetCurrentRestResponse',
          'params' => [
          ],
        ],
        'UserGetOauth2Application' => [
          'method' => 'GET',
          'resource' => 'user/applications/oauth2',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetOauth2ApplicationRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCreateOAuth2Application' => [
          'method' => 'POST',
          'resource' => 'user/applications/oauth2',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCreateOAuth2ApplicationRestResponse',
          'params' => [
          ],
        ],
        'UserGetOAuth2Application' => [
          'method' => 'GET',
          'resource' => 'user/applications/oauth2/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetOAuth2ApplicationRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserDeleteOAuth2Application' => [
          'method' => 'DELETE',
          'resource' => 'user/applications/oauth2/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteOAuth2ApplicationRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserUpdateOAuth2Application' => [
          'method' => 'PATCH',
          'resource' => 'user/applications/oauth2/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserUpdateOAuth2ApplicationRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserListEmails' => [
          'method' => 'GET',
          'resource' => 'user/emails',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListEmailsRestResponse',
          'params' => [
          ],
        ],
        'UserAddEmail' => [
          'method' => 'POST',
          'resource' => 'user/emails',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserAddEmailRestResponse',
          'params' => [
          ],
        ],
        'UserDeleteEmail' => [
          'method' => 'DELETE',
          'resource' => 'user/emails',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteEmailRestResponse',
          'params' => [
          ],
        ],
        'UserCurrentListFollowers' => [
          'method' => 'GET',
          'resource' => 'user/followers',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListFollowersRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentListFollowing' => [
          'method' => 'GET',
          'resource' => 'user/following',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListFollowingRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentCheckFollowing' => [
          'method' => 'GET',
          'resource' => 'user/following/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckFollowingRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentPutFollow' => [
          'method' => 'PUT',
          'resource' => 'user/following/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutFollowRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteFollow' => [
          'method' => 'DELETE',
          'resource' => 'user/following/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteFollowRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentListGPGKeys' => [
          'method' => 'GET',
          'resource' => 'user/gpg_keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListGPGKeysRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentPostGPGKey' => [
          'method' => 'POST',
          'resource' => 'user/gpg_keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPostGPGKeyRestResponse',
          'params' => [
          ],
        ],
        'UserCurrentGetGPGKey' => [
          'method' => 'GET',
          'resource' => 'user/gpg_keys/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentGetGPGKeyRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteGPGKey' => [
          'method' => 'DELETE',
          'resource' => 'user/gpg_keys/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteGPGKeyRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentListKeys' => [
          'method' => 'GET',
          'resource' => 'user/keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListKeysRestResponse',
          'params' => [
            'fingerprint' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentPostKey' => [
          'method' => 'POST',
          'resource' => 'user/keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPostKeyRestResponse',
          'params' => [
          ],
        ],
        'UserCurrentGetKey' => [
          'method' => 'GET',
          'resource' => 'user/keys/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentGetKeyRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteKey' => [
          'method' => 'DELETE',
          'resource' => 'user/keys/{id}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteKeyRestResponse',
          'params' => [
            'id' => [
              'valid' => [
          'integer',
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListCurrentUserOrgs' => [
          'method' => 'GET',
          'resource' => 'user/orgs',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListCurrentUserOrgsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentListRepos' => [
          'method' => 'GET',
          'resource' => 'user/repos',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListReposRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'CreateCurrentUserRepo' => [
          'method' => 'POST',
          'resource' => 'user/repos',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateCurrentUserRepoRestResponse',
          'params' => [
          ],
        ],
        'UserCurrentListStarred' => [
          'method' => 'GET',
          'resource' => 'user/starred',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListStarredRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentCheckStarring' => [
          'method' => 'GET',
          'resource' => 'user/starred/{owner}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckStarringRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentPutStar' => [
          'method' => 'PUT',
          'resource' => 'user/starred/{owner}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutStarRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteStar' => [
          'method' => 'DELETE',
          'resource' => 'user/starred/{owner}/{repo}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteStarRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserGetStopWatches' => [
          'method' => 'GET',
          'resource' => 'user/stopwatches',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetStopWatchesRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentListSubscriptions' => [
          'method' => 'GET',
          'resource' => 'user/subscriptions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListSubscriptionsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserListTeams' => [
          'method' => 'GET',
          'resource' => 'user/teams',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListTeamsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCurrentTrackedTimes' => [
          'method' => 'GET',
          'resource' => 'user/times',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentTrackedTimesRestResponse',
          'params' => [
            'since' => [
              'valid' => [
                'string',
              ],
            ],
            'before' => [
              'valid' => [
                'string',
              ],
            ],
          ],
        ],
        'UserSearch' => [
          'method' => 'GET',
          'resource' => 'users/search',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserSearchRestResponse',
          'params' => [
            'q' => [
              'valid' => [
                'string',
              ],
            ],
            'uid' => [
              'valid' => [
          'integer',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCheckFollowing' => [
          'method' => 'GET',
          'resource' => 'users/{follower}/following/{followee}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCheckFollowingRestResponse',
          'params' => [
            'follower' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'followee' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserGet' => [
          'method' => 'GET',
          'resource' => 'users/{username}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserListFollowers' => [
          'method' => 'GET',
          'resource' => 'users/{username}/followers',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListFollowersRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserListFollowing' => [
          'method' => 'GET',
          'resource' => 'users/{username}/following',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListFollowingRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserListGPGKeys' => [
          'method' => 'GET',
          'resource' => 'users/{username}/gpg_keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListGPGKeysRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserGetHeatmapData' => [
          'method' => 'GET',
          'resource' => 'users/{username}/heatmap',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetHeatmapDataRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserListKeys' => [
          'method' => 'GET',
          'resource' => 'users/{username}/keys',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListKeysRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'fingerprint' => [
              'valid' => [
                'string',
              ],
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'OrgListUserOrgs' => [
          'method' => 'GET',
          'resource' => 'users/{username}/orgs',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListUserOrgsRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserListRepos' => [
          'method' => 'GET',
          'resource' => 'users/{username}/repos',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListReposRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserListStarred' => [
          'method' => 'GET',
          'resource' => 'users/{username}/starred',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListStarredRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserListSubscriptions' => [
          'method' => 'GET',
          'resource' => 'users/{username}/subscriptions',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListSubscriptionsRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserGetTokens' => [
          'method' => 'GET',
          'resource' => 'users/{username}/tokens',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetTokensRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
          'integer',
              ],
            ],
            'limit' => [
              'valid' => [
          'integer',
              ],
            ],
          ],
        ],
        'UserCreateToken' => [
          'method' => 'POST',
          'resource' => 'users/{username}/tokens',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCreateTokenRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'UserDeleteAccessToken' => [
          'method' => 'DELETE',
          'resource' => 'users/{username}/tokens/{token}',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteAccessTokenRestResponse',
          'params' => [
            'username' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
            'token' => [
              'valid' => [
                'string',
              ],
              'required' => true,
            ],
          ],
        ],
        'GetVersion' => [
          'method' => 'GET',
          'resource' => 'version',
          'responseClass' => '\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetVersionRestResponse',
          'params' => [
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function adminCronList(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCronListRestRequest $request)
    {
        return $this->adminCronListAsync($request)->wait();
    }

    public function adminCronListAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCronListRestRequest $request)
    {
        return $this->callOperationAsync('AdminCronList', $request);
    }

    public function adminCronRun(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCronRunRestRequest $request)
    {
        return $this->adminCronRunAsync($request)->wait();
    }

    public function adminCronRunAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCronRunRestRequest $request)
    {
        return $this->callOperationAsync('AdminCronRun', $request);
    }

    public function adminGetAllOrgs(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminGetAllOrgsRestRequest $request)
    {
        return $this->adminGetAllOrgsAsync($request)->wait();
    }

    public function adminGetAllOrgsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminGetAllOrgsRestRequest $request)
    {
        return $this->callOperationAsync('AdminGetAllOrgs', $request);
    }

    public function adminUnadoptedList(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminUnadoptedListRestRequest $request)
    {
        return $this->adminUnadoptedListAsync($request)->wait();
    }

    public function adminUnadoptedListAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminUnadoptedListRestRequest $request)
    {
        return $this->callOperationAsync('AdminUnadoptedList', $request);
    }

    public function adminAdoptRepository(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminAdoptRepositoryRestRequest $request)
    {
        return $this->adminAdoptRepositoryAsync($request)->wait();
    }

    public function adminAdoptRepositoryAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminAdoptRepositoryRestRequest $request)
    {
        return $this->callOperationAsync('AdminAdoptRepository', $request);
    }

    public function adminDeleteUnadoptedRepository(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUnadoptedRepositoryRestRequest $request)
    {
        return $this->adminDeleteUnadoptedRepositoryAsync($request)->wait();
    }

    public function adminDeleteUnadoptedRepositoryAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUnadoptedRepositoryRestRequest $request)
    {
        return $this->callOperationAsync('AdminDeleteUnadoptedRepository', $request);
    }

    public function adminGetAllUsers(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminGetAllUsersRestRequest $request)
    {
        return $this->adminGetAllUsersAsync($request)->wait();
    }

    public function adminGetAllUsersAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminGetAllUsersRestRequest $request)
    {
        return $this->callOperationAsync('AdminGetAllUsers', $request);
    }

    public function adminCreateUser(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateUserRestRequest $request)
    {
        return $this->adminCreateUserAsync($request)->wait();
    }

    public function adminCreateUserAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateUserRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreateUser', $request);
    }

    public function adminDeleteUser(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUserRestRequest $request)
    {
        return $this->adminDeleteUserAsync($request)->wait();
    }

    public function adminDeleteUserAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUserRestRequest $request)
    {
        return $this->callOperationAsync('AdminDeleteUser', $request);
    }

    public function adminEditUser(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminEditUserRestRequest $request)
    {
        return $this->adminEditUserAsync($request)->wait();
    }

    public function adminEditUserAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminEditUserRestRequest $request)
    {
        return $this->callOperationAsync('AdminEditUser', $request);
    }

    public function adminCreatePublicKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreatePublicKeyRestRequest $request)
    {
        return $this->adminCreatePublicKeyAsync($request)->wait();
    }

    public function adminCreatePublicKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreatePublicKeyRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreatePublicKey', $request);
    }

    public function adminDeleteUserPublicKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUserPublicKeyRestRequest $request)
    {
        return $this->adminDeleteUserPublicKeyAsync($request)->wait();
    }

    public function adminDeleteUserPublicKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminDeleteUserPublicKeyRestRequest $request)
    {
        return $this->callOperationAsync('AdminDeleteUserPublicKey', $request);
    }

    public function adminCreateOrg(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateOrgRestRequest $request)
    {
        return $this->adminCreateOrgAsync($request)->wait();
    }

    public function adminCreateOrgAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateOrgRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreateOrg', $request);
    }

    public function adminCreateRepo(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateRepoRestRequest $request)
    {
        return $this->adminCreateRepoAsync($request)->wait();
    }

    public function adminCreateRepoAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\AdminCreateRepoRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreateRepo', $request);
    }

    public function renderMarkdown(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RenderMarkdownRestRequest $request)
    {
        return $this->renderMarkdownAsync($request)->wait();
    }

    public function renderMarkdownAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RenderMarkdownRestRequest $request)
    {
        return $this->callOperationAsync('RenderMarkdown', $request);
    }

    public function renderMarkdownRaw(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RenderMarkdownRawRestRequest $request)
    {
        return $this->renderMarkdownRawAsync($request)->wait();
    }

    public function renderMarkdownRawAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RenderMarkdownRawRestRequest $request)
    {
        return $this->callOperationAsync('RenderMarkdownRaw', $request);
    }

    public function notifyGetList(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetListRestRequest $request)
    {
        return $this->notifyGetListAsync($request)->wait();
    }

    public function notifyGetListAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetListRestRequest $request)
    {
        return $this->callOperationAsync('NotifyGetList', $request);
    }

    public function notifyReadList(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadListRestRequest $request)
    {
        return $this->notifyReadListAsync($request)->wait();
    }

    public function notifyReadListAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadListRestRequest $request)
    {
        return $this->callOperationAsync('NotifyReadList', $request);
    }

    public function notifyNewAvailable(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyNewAvailableRestRequest $request)
    {
        return $this->notifyNewAvailableAsync($request)->wait();
    }

    public function notifyNewAvailableAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyNewAvailableRestRequest $request)
    {
        return $this->callOperationAsync('NotifyNewAvailable', $request);
    }

    public function notifyGetThread(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetThreadRestRequest $request)
    {
        return $this->notifyGetThreadAsync($request)->wait();
    }

    public function notifyGetThreadAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetThreadRestRequest $request)
    {
        return $this->callOperationAsync('NotifyGetThread', $request);
    }

    public function notifyReadThread(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadThreadRestRequest $request)
    {
        return $this->notifyReadThreadAsync($request)->wait();
    }

    public function notifyReadThreadAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadThreadRestRequest $request)
    {
        return $this->callOperationAsync('NotifyReadThread', $request);
    }

    public function createOrgRepoDeprecated(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateOrgRepoDeprecatedRestRequest $request)
    {
        return $this->createOrgRepoDeprecatedAsync($request)->wait();
    }

    public function createOrgRepoDeprecatedAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateOrgRepoDeprecatedRestRequest $request)
    {
        return $this->callOperationAsync('CreateOrgRepoDeprecated', $request);
    }

    public function orgGetAll(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetAllRestRequest $request)
    {
        return $this->orgGetAllAsync($request)->wait();
    }

    public function orgGetAllAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetAllRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetAll', $request);
    }

    public function orgCreate(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateRestRequest $request)
    {
        return $this->orgCreateAsync($request)->wait();
    }

    public function orgCreateAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateRestRequest $request)
    {
        return $this->callOperationAsync('OrgCreate', $request);
    }

    public function orgGet(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetRestRequest $request)
    {
        return $this->orgGetAsync($request)->wait();
    }

    public function orgGetAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetRestRequest $request)
    {
        return $this->callOperationAsync('OrgGet', $request);
    }

    public function orgDelete(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteRestRequest $request)
    {
        return $this->orgDeleteAsync($request)->wait();
    }

    public function orgDeleteAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteRestRequest $request)
    {
        return $this->callOperationAsync('OrgDelete', $request);
    }

    public function orgEdit(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditRestRequest $request)
    {
        return $this->orgEditAsync($request)->wait();
    }

    public function orgEditAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditRestRequest $request)
    {
        return $this->callOperationAsync('OrgEdit', $request);
    }

    public function orgListHooks(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListHooksRestRequest $request)
    {
        return $this->orgListHooksAsync($request)->wait();
    }

    public function orgListHooksAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListHooksRestRequest $request)
    {
        return $this->callOperationAsync('OrgListHooks', $request);
    }

    public function orgCreateHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateHookRestRequest $request)
    {
        return $this->orgCreateHookAsync($request)->wait();
    }

    public function orgCreateHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateHookRestRequest $request)
    {
        return $this->callOperationAsync('OrgCreateHook', $request);
    }

    public function orgGetHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetHookRestRequest $request)
    {
        return $this->orgGetHookAsync($request)->wait();
    }

    public function orgGetHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetHookRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetHook', $request);
    }

    public function orgDeleteHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteHookRestRequest $request)
    {
        return $this->orgDeleteHookAsync($request)->wait();
    }

    public function orgDeleteHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteHookRestRequest $request)
    {
        return $this->callOperationAsync('OrgDeleteHook', $request);
    }

    public function orgEditHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditHookRestRequest $request)
    {
        return $this->orgEditHookAsync($request)->wait();
    }

    public function orgEditHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditHookRestRequest $request)
    {
        return $this->callOperationAsync('OrgEditHook', $request);
    }

    public function orgListLabels(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListLabelsRestRequest $request)
    {
        return $this->orgListLabelsAsync($request)->wait();
    }

    public function orgListLabelsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListLabelsRestRequest $request)
    {
        return $this->callOperationAsync('OrgListLabels', $request);
    }

    public function orgCreateLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateLabelRestRequest $request)
    {
        return $this->orgCreateLabelAsync($request)->wait();
    }

    public function orgCreateLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateLabelRestRequest $request)
    {
        return $this->callOperationAsync('OrgCreateLabel', $request);
    }

    public function orgGetLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetLabelRestRequest $request)
    {
        return $this->orgGetLabelAsync($request)->wait();
    }

    public function orgGetLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetLabelRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetLabel', $request);
    }

    public function orgDeleteLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteLabelRestRequest $request)
    {
        return $this->orgDeleteLabelAsync($request)->wait();
    }

    public function orgDeleteLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteLabelRestRequest $request)
    {
        return $this->callOperationAsync('OrgDeleteLabel', $request);
    }

    public function orgEditLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditLabelRestRequest $request)
    {
        return $this->orgEditLabelAsync($request)->wait();
    }

    public function orgEditLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditLabelRestRequest $request)
    {
        return $this->callOperationAsync('OrgEditLabel', $request);
    }

    public function orgListMembers(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListMembersRestRequest $request)
    {
        return $this->orgListMembersAsync($request)->wait();
    }

    public function orgListMembersAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListMembersRestRequest $request)
    {
        return $this->callOperationAsync('OrgListMembers', $request);
    }

    public function orgIsMember(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgIsMemberRestRequest $request)
    {
        return $this->orgIsMemberAsync($request)->wait();
    }

    public function orgIsMemberAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgIsMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgIsMember', $request);
    }

    public function orgDeleteMember(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteMemberRestRequest $request)
    {
        return $this->orgDeleteMemberAsync($request)->wait();
    }

    public function orgDeleteMemberAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgDeleteMember', $request);
    }

    public function orgListPublicMembers(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListPublicMembersRestRequest $request)
    {
        return $this->orgListPublicMembersAsync($request)->wait();
    }

    public function orgListPublicMembersAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListPublicMembersRestRequest $request)
    {
        return $this->callOperationAsync('OrgListPublicMembers', $request);
    }

    public function orgIsPublicMember(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgIsPublicMemberRestRequest $request)
    {
        return $this->orgIsPublicMemberAsync($request)->wait();
    }

    public function orgIsPublicMemberAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgIsPublicMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgIsPublicMember', $request);
    }

    public function orgPublicizeMember(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgPublicizeMemberRestRequest $request)
    {
        return $this->orgPublicizeMemberAsync($request)->wait();
    }

    public function orgPublicizeMemberAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgPublicizeMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgPublicizeMember', $request);
    }

    public function orgConcealMember(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgConcealMemberRestRequest $request)
    {
        return $this->orgConcealMemberAsync($request)->wait();
    }

    public function orgConcealMemberAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgConcealMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgConcealMember', $request);
    }

    public function orgListRepos(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListReposRestRequest $request)
    {
        return $this->orgListReposAsync($request)->wait();
    }

    public function orgListReposAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListReposRestRequest $request)
    {
        return $this->callOperationAsync('OrgListRepos', $request);
    }

    public function createOrgRepo(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateOrgRepoRestRequest $request)
    {
        return $this->createOrgRepoAsync($request)->wait();
    }

    public function createOrgRepoAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateOrgRepoRestRequest $request)
    {
        return $this->callOperationAsync('CreateOrgRepo', $request);
    }

    public function orgListTeams(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamsRestRequest $request)
    {
        return $this->orgListTeamsAsync($request)->wait();
    }

    public function orgListTeamsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamsRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeams', $request);
    }

    public function orgCreateTeam(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateTeamRestRequest $request)
    {
        return $this->orgCreateTeamAsync($request)->wait();
    }

    public function orgCreateTeamAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgCreateTeamRestRequest $request)
    {
        return $this->callOperationAsync('OrgCreateTeam', $request);
    }

    public function teamSearch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\TeamSearchRestRequest $request)
    {
        return $this->teamSearchAsync($request)->wait();
    }

    public function teamSearchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\TeamSearchRestRequest $request)
    {
        return $this->callOperationAsync('TeamSearch', $request);
    }

    public function issueSearchIssues(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueSearchIssuesRestRequest $request)
    {
        return $this->issueSearchIssuesAsync($request)->wait();
    }

    public function issueSearchIssuesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueSearchIssuesRestRequest $request)
    {
        return $this->callOperationAsync('IssueSearchIssues', $request);
    }

    public function repoMigrate(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMigrateRestRequest $request)
    {
        return $this->repoMigrateAsync($request)->wait();
    }

    public function repoMigrateAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMigrateRestRequest $request)
    {
        return $this->callOperationAsync('RepoMigrate', $request);
    }

    public function repoSearch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSearchRestRequest $request)
    {
        return $this->repoSearchAsync($request)->wait();
    }

    public function repoSearchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSearchRestRequest $request)
    {
        return $this->callOperationAsync('RepoSearch', $request);
    }

    public function repoGet(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetRestRequest $request)
    {
        return $this->repoGetAsync($request)->wait();
    }

    public function repoGetAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetRestRequest $request)
    {
        return $this->callOperationAsync('RepoGet', $request);
    }

    public function repoDelete(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteRestRequest $request)
    {
        return $this->repoDeleteAsync($request)->wait();
    }

    public function repoDeleteAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteRestRequest $request)
    {
        return $this->callOperationAsync('RepoDelete', $request);
    }

    public function repoEdit(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditRestRequest $request)
    {
        return $this->repoEditAsync($request)->wait();
    }

    public function repoEditAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditRestRequest $request)
    {
        return $this->callOperationAsync('RepoEdit', $request);
    }

    public function repoGetArchive(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetArchiveRestRequest $request)
    {
        return $this->repoGetArchiveAsync($request)->wait();
    }

    public function repoGetArchiveAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetArchiveRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetArchive', $request);
    }

    public function repoListBranchProtection(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListBranchProtectionRestRequest $request)
    {
        return $this->repoListBranchProtectionAsync($request)->wait();
    }

    public function repoListBranchProtectionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoListBranchProtection', $request);
    }

    public function repoCreateBranchProtection(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateBranchProtectionRestRequest $request)
    {
        return $this->repoCreateBranchProtectionAsync($request)->wait();
    }

    public function repoCreateBranchProtectionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateBranchProtection', $request);
    }

    public function repoGetBranchProtection(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetBranchProtectionRestRequest $request)
    {
        return $this->repoGetBranchProtectionAsync($request)->wait();
    }

    public function repoGetBranchProtectionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetBranchProtection', $request);
    }

    public function repoDeleteBranchProtection(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteBranchProtectionRestRequest $request)
    {
        return $this->repoDeleteBranchProtectionAsync($request)->wait();
    }

    public function repoDeleteBranchProtectionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteBranchProtection', $request);
    }

    public function repoEditBranchProtection(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditBranchProtectionRestRequest $request)
    {
        return $this->repoEditBranchProtectionAsync($request)->wait();
    }

    public function repoEditBranchProtectionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditBranchProtection', $request);
    }

    public function repoListBranches(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListBranchesRestRequest $request)
    {
        return $this->repoListBranchesAsync($request)->wait();
    }

    public function repoListBranchesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListBranchesRestRequest $request)
    {
        return $this->callOperationAsync('RepoListBranches', $request);
    }

    public function repoCreateBranch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateBranchRestRequest $request)
    {
        return $this->repoCreateBranchAsync($request)->wait();
    }

    public function repoCreateBranchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateBranchRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateBranch', $request);
    }

    public function repoGetBranch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetBranchRestRequest $request)
    {
        return $this->repoGetBranchAsync($request)->wait();
    }

    public function repoGetBranchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetBranchRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetBranch', $request);
    }

    public function repoDeleteBranch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteBranchRestRequest $request)
    {
        return $this->repoDeleteBranchAsync($request)->wait();
    }

    public function repoDeleteBranchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteBranchRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteBranch', $request);
    }

    public function repoListCollaborators(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListCollaboratorsRestRequest $request)
    {
        return $this->repoListCollaboratorsAsync($request)->wait();
    }

    public function repoListCollaboratorsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListCollaboratorsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListCollaborators', $request);
    }

    public function repoCheckCollaborator(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCheckCollaboratorRestRequest $request)
    {
        return $this->repoCheckCollaboratorAsync($request)->wait();
    }

    public function repoCheckCollaboratorAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCheckCollaboratorRestRequest $request)
    {
        return $this->callOperationAsync('RepoCheckCollaborator', $request);
    }

    public function repoAddCollaborator(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddCollaboratorRestRequest $request)
    {
        return $this->repoAddCollaboratorAsync($request)->wait();
    }

    public function repoAddCollaboratorAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddCollaboratorRestRequest $request)
    {
        return $this->callOperationAsync('RepoAddCollaborator', $request);
    }

    public function repoDeleteCollaborator(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteCollaboratorRestRequest $request)
    {
        return $this->repoDeleteCollaboratorAsync($request)->wait();
    }

    public function repoDeleteCollaboratorAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteCollaboratorRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteCollaborator', $request);
    }

    public function repoGetAllCommits(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetAllCommitsRestRequest $request)
    {
        return $this->repoGetAllCommitsAsync($request)->wait();
    }

    public function repoGetAllCommitsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetAllCommitsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetAllCommits', $request);
    }

    public function repoGetCombinedStatusByRef(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetCombinedStatusByRefRestRequest $request)
    {
        return $this->repoGetCombinedStatusByRefAsync($request)->wait();
    }

    public function repoGetCombinedStatusByRefAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetCombinedStatusByRefRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetCombinedStatusByRef', $request);
    }

    public function repoListStatusesByRef(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStatusesByRefRestRequest $request)
    {
        return $this->repoListStatusesByRefAsync($request)->wait();
    }

    public function repoListStatusesByRefAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStatusesByRefRestRequest $request)
    {
        return $this->callOperationAsync('RepoListStatusesByRef', $request);
    }

    public function repoGetContentsList(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetContentsListRestRequest $request)
    {
        return $this->repoGetContentsListAsync($request)->wait();
    }

    public function repoGetContentsListAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetContentsListRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetContentsList', $request);
    }

    public function repoGetContents(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetContentsRestRequest $request)
    {
        return $this->repoGetContentsAsync($request)->wait();
    }

    public function repoGetContentsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetContentsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetContents', $request);
    }

    public function repoUpdateFile(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdateFileRestRequest $request)
    {
        return $this->repoUpdateFileAsync($request)->wait();
    }

    public function repoUpdateFileAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdateFileRestRequest $request)
    {
        return $this->callOperationAsync('RepoUpdateFile', $request);
    }

    public function repoCreateFile(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateFileRestRequest $request)
    {
        return $this->repoCreateFileAsync($request)->wait();
    }

    public function repoCreateFileAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateFileRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateFile', $request);
    }

    public function repoDeleteFile(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteFileRestRequest $request)
    {
        return $this->repoDeleteFileAsync($request)->wait();
    }

    public function repoDeleteFileAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteFileRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteFile', $request);
    }

    public function repoGetEditorConfig(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetEditorConfigRestRequest $request)
    {
        return $this->repoGetEditorConfigAsync($request)->wait();
    }

    public function repoGetEditorConfigAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetEditorConfigRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetEditorConfig', $request);
    }

    public function listForks(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\ListForksRestRequest $request)
    {
        return $this->listForksAsync($request)->wait();
    }

    public function listForksAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\ListForksRestRequest $request)
    {
        return $this->callOperationAsync('ListForks', $request);
    }

    public function createFork(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateForkRestRequest $request)
    {
        return $this->createForkAsync($request)->wait();
    }

    public function createForkAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateForkRestRequest $request)
    {
        return $this->callOperationAsync('CreateFork', $request);
    }

    public function GetBlob(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetBlobRestRequest $request)
    {
        return $this->GetBlobAsync($request)->wait();
    }

    public function GetBlobAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetBlobRestRequest $request)
    {
        return $this->callOperationAsync('GetBlob', $request);
    }

    public function repoGetSingleCommit(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetSingleCommitRestRequest $request)
    {
        return $this->repoGetSingleCommitAsync($request)->wait();
    }

    public function repoGetSingleCommitAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetSingleCommitRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetSingleCommit', $request);
    }

    public function repoListAllGitRefs(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListAllGitRefsRestRequest $request)
    {
        return $this->repoListAllGitRefsAsync($request)->wait();
    }

    public function repoListAllGitRefsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListAllGitRefsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListAllGitRefs', $request);
    }

    public function repoListGitRefs(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListGitRefsRestRequest $request)
    {
        return $this->repoListGitRefsAsync($request)->wait();
    }

    public function repoListGitRefsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListGitRefsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListGitRefs', $request);
    }

    public function GetTag(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetTagRestRequest $request)
    {
        return $this->GetTagAsync($request)->wait();
    }

    public function GetTagAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetTagRestRequest $request)
    {
        return $this->callOperationAsync('GetTag', $request);
    }

    public function GetTree(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetTreeRestRequest $request)
    {
        return $this->GetTreeAsync($request)->wait();
    }

    public function GetTreeAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetTreeRestRequest $request)
    {
        return $this->callOperationAsync('GetTree', $request);
    }

    public function repoListHooks(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListHooksRestRequest $request)
    {
        return $this->repoListHooksAsync($request)->wait();
    }

    public function repoListHooksAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListHooksRestRequest $request)
    {
        return $this->callOperationAsync('RepoListHooks', $request);
    }

    public function repoCreateHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateHookRestRequest $request)
    {
        return $this->repoCreateHookAsync($request)->wait();
    }

    public function repoCreateHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateHook', $request);
    }

    public function repoListGitHooks(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListGitHooksRestRequest $request)
    {
        return $this->repoListGitHooksAsync($request)->wait();
    }

    public function repoListGitHooksAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListGitHooksRestRequest $request)
    {
        return $this->callOperationAsync('RepoListGitHooks', $request);
    }

    public function repoGetGitHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetGitHookRestRequest $request)
    {
        return $this->repoGetGitHookAsync($request)->wait();
    }

    public function repoGetGitHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetGitHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetGitHook', $request);
    }

    public function repoDeleteGitHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteGitHookRestRequest $request)
    {
        return $this->repoDeleteGitHookAsync($request)->wait();
    }

    public function repoDeleteGitHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteGitHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteGitHook', $request);
    }

    public function repoEditGitHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditGitHookRestRequest $request)
    {
        return $this->repoEditGitHookAsync($request)->wait();
    }

    public function repoEditGitHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditGitHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditGitHook', $request);
    }

    public function repoGetHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetHookRestRequest $request)
    {
        return $this->repoGetHookAsync($request)->wait();
    }

    public function repoGetHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetHook', $request);
    }

    public function repoDeleteHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteHookRestRequest $request)
    {
        return $this->repoDeleteHookAsync($request)->wait();
    }

    public function repoDeleteHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteHook', $request);
    }

    public function repoEditHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditHookRestRequest $request)
    {
        return $this->repoEditHookAsync($request)->wait();
    }

    public function repoEditHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditHook', $request);
    }

    public function repoTestHook(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTestHookRestRequest $request)
    {
        return $this->repoTestHookAsync($request)->wait();
    }

    public function repoTestHookAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTestHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoTestHook', $request);
    }

    public function repoGetIssueTemplates(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetIssueTemplatesRestRequest $request)
    {
        return $this->repoGetIssueTemplatesAsync($request)->wait();
    }

    public function repoGetIssueTemplatesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetIssueTemplatesRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetIssueTemplates', $request);
    }

    public function issueListIssues(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueListIssuesRestRequest $request)
    {
        return $this->issueListIssuesAsync($request)->wait();
    }

    public function issueListIssuesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueListIssuesRestRequest $request)
    {
        return $this->callOperationAsync('IssueListIssues', $request);
    }

    public function issueCreateIssue(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateIssueRestRequest $request)
    {
        return $this->issueCreateIssueAsync($request)->wait();
    }

    public function issueCreateIssueAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateIssueRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateIssue', $request);
    }

    public function issueGetRepoComments(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetRepoCommentsRestRequest $request)
    {
        return $this->issueGetRepoCommentsAsync($request)->wait();
    }

    public function issueGetRepoCommentsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetRepoCommentsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetRepoComments', $request);
    }

    public function issueGetComment(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentRestRequest $request)
    {
        return $this->issueGetCommentAsync($request)->wait();
    }

    public function issueGetCommentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetComment', $request);
    }

    public function issueDeleteComment(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentRestRequest $request)
    {
        return $this->issueDeleteCommentAsync($request)->wait();
    }

    public function issueDeleteCommentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteComment', $request);
    }

    public function issueEditComment(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditCommentRestRequest $request)
    {
        return $this->issueEditCommentAsync($request)->wait();
    }

    public function issueEditCommentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditCommentRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditComment', $request);
    }

    public function issueGetCommentReactions(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentReactionsRestRequest $request)
    {
        return $this->issueGetCommentReactionsAsync($request)->wait();
    }

    public function issueGetCommentReactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentReactionsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetCommentReactions', $request);
    }

    public function issuePostCommentReaction(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssuePostCommentReactionRestRequest $request)
    {
        return $this->issuePostCommentReactionAsync($request)->wait();
    }

    public function issuePostCommentReactionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssuePostCommentReactionRestRequest $request)
    {
        return $this->callOperationAsync('IssuePostCommentReaction', $request);
    }

    public function issueDeleteCommentReaction(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentReactionRestRequest $request)
    {
        return $this->issueDeleteCommentReactionAsync($request)->wait();
    }

    public function issueDeleteCommentReactionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentReactionRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteCommentReaction', $request);
    }

    public function issueGetIssue(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetIssueRestRequest $request)
    {
        return $this->issueGetIssueAsync($request)->wait();
    }

    public function issueGetIssueAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetIssueRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetIssue', $request);
    }

    public function issueEditIssue(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditIssueRestRequest $request)
    {
        return $this->issueEditIssueAsync($request)->wait();
    }

    public function issueEditIssueAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditIssueRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditIssue', $request);
    }

    public function issueGetComments(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentsRestRequest $request)
    {
        return $this->issueGetCommentsAsync($request)->wait();
    }

    public function issueGetCommentsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetCommentsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetComments', $request);
    }

    public function issueCreateComment(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateCommentRestRequest $request)
    {
        return $this->issueCreateCommentAsync($request)->wait();
    }

    public function issueCreateCommentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateCommentRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateComment', $request);
    }

    public function issueDeleteCommentDeprecated(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentDeprecatedRestRequest $request)
    {
        return $this->issueDeleteCommentDeprecatedAsync($request)->wait();
    }

    public function issueDeleteCommentDeprecatedAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteCommentDeprecatedRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteCommentDeprecated', $request);
    }

    public function issueEditCommentDeprecated(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditCommentDeprecatedRestRequest $request)
    {
        return $this->issueEditCommentDeprecatedAsync($request)->wait();
    }

    public function issueEditCommentDeprecatedAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditCommentDeprecatedRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditCommentDeprecated', $request);
    }

    public function issueEditIssueDeadline(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditIssueDeadlineRestRequest $request)
    {
        return $this->issueEditIssueDeadlineAsync($request)->wait();
    }

    public function issueEditIssueDeadlineAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditIssueDeadlineRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditIssueDeadline', $request);
    }

    public function issueGetLabels(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetLabelsRestRequest $request)
    {
        return $this->issueGetLabelsAsync($request)->wait();
    }

    public function issueGetLabelsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetLabelsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetLabels', $request);
    }

    public function issueReplaceLabels(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueReplaceLabelsRestRequest $request)
    {
        return $this->issueReplaceLabelsAsync($request)->wait();
    }

    public function issueReplaceLabelsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueReplaceLabelsRestRequest $request)
    {
        return $this->callOperationAsync('IssueReplaceLabels', $request);
    }

    public function issueAddLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddLabelRestRequest $request)
    {
        return $this->issueAddLabelAsync($request)->wait();
    }

    public function issueAddLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueAddLabel', $request);
    }

    public function issueClearLabels(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueClearLabelsRestRequest $request)
    {
        return $this->issueClearLabelsAsync($request)->wait();
    }

    public function issueClearLabelsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueClearLabelsRestRequest $request)
    {
        return $this->callOperationAsync('IssueClearLabels', $request);
    }

    public function issueRemoveLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueRemoveLabelRestRequest $request)
    {
        return $this->issueRemoveLabelAsync($request)->wait();
    }

    public function issueRemoveLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueRemoveLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueRemoveLabel', $request);
    }

    public function issueGetIssueReactions(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetIssueReactionsRestRequest $request)
    {
        return $this->issueGetIssueReactionsAsync($request)->wait();
    }

    public function issueGetIssueReactionsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetIssueReactionsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetIssueReactions', $request);
    }

    public function issuePostIssueReaction(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssuePostIssueReactionRestRequest $request)
    {
        return $this->issuePostIssueReactionAsync($request)->wait();
    }

    public function issuePostIssueReactionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssuePostIssueReactionRestRequest $request)
    {
        return $this->callOperationAsync('IssuePostIssueReaction', $request);
    }

    public function issueDeleteIssueReaction(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteIssueReactionRestRequest $request)
    {
        return $this->issueDeleteIssueReactionAsync($request)->wait();
    }

    public function issueDeleteIssueReactionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteIssueReactionRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteIssueReaction', $request);
    }

    public function issueDeleteStopWatch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteStopWatchRestRequest $request)
    {
        return $this->issueDeleteStopWatchAsync($request)->wait();
    }

    public function issueDeleteStopWatchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteStopWatchRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteStopWatch', $request);
    }

    public function issueStartStopWatch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueStartStopWatchRestRequest $request)
    {
        return $this->issueStartStopWatchAsync($request)->wait();
    }

    public function issueStartStopWatchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueStartStopWatchRestRequest $request)
    {
        return $this->callOperationAsync('IssueStartStopWatch', $request);
    }

    public function issueStopStopWatch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueStopStopWatchRestRequest $request)
    {
        return $this->issueStopStopWatchAsync($request)->wait();
    }

    public function issueStopStopWatchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueStopStopWatchRestRequest $request)
    {
        return $this->callOperationAsync('IssueStopStopWatch', $request);
    }

    public function issueSubscriptions(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueSubscriptionsRestRequest $request)
    {
        return $this->issueSubscriptionsAsync($request)->wait();
    }

    public function issueSubscriptionsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueSubscriptionsRestRequest $request)
    {
        return $this->callOperationAsync('IssueSubscriptions', $request);
    }

    public function issueCheckSubscription(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCheckSubscriptionRestRequest $request)
    {
        return $this->issueCheckSubscriptionAsync($request)->wait();
    }

    public function issueCheckSubscriptionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCheckSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('IssueCheckSubscription', $request);
    }

    public function issueAddSubscription(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddSubscriptionRestRequest $request)
    {
        return $this->issueAddSubscriptionAsync($request)->wait();
    }

    public function issueAddSubscriptionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('IssueAddSubscription', $request);
    }

    public function issueDeleteSubscription(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteSubscriptionRestRequest $request)
    {
        return $this->issueDeleteSubscriptionAsync($request)->wait();
    }

    public function issueDeleteSubscriptionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteSubscription', $request);
    }

    public function issueTrackedTimes(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueTrackedTimesRestRequest $request)
    {
        return $this->issueTrackedTimesAsync($request)->wait();
    }

    public function issueTrackedTimesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueTrackedTimesRestRequest $request)
    {
        return $this->callOperationAsync('IssueTrackedTimes', $request);
    }

    public function issueAddTime(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddTimeRestRequest $request)
    {
        return $this->issueAddTimeAsync($request)->wait();
    }

    public function issueAddTimeAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueAddTimeRestRequest $request)
    {
        return $this->callOperationAsync('IssueAddTime', $request);
    }

    public function issueResetTime(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueResetTimeRestRequest $request)
    {
        return $this->issueResetTimeAsync($request)->wait();
    }

    public function issueResetTimeAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueResetTimeRestRequest $request)
    {
        return $this->callOperationAsync('IssueResetTime', $request);
    }

    public function issueDeleteTime(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteTimeRestRequest $request)
    {
        return $this->issueDeleteTimeAsync($request)->wait();
    }

    public function issueDeleteTimeAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteTimeRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteTime', $request);
    }

    public function repoListKeys(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListKeysRestRequest $request)
    {
        return $this->repoListKeysAsync($request)->wait();
    }

    public function repoListKeysAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListKeysRestRequest $request)
    {
        return $this->callOperationAsync('RepoListKeys', $request);
    }

    public function repoCreateKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateKeyRestRequest $request)
    {
        return $this->repoCreateKeyAsync($request)->wait();
    }

    public function repoCreateKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateKeyRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateKey', $request);
    }

    public function repoGetKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetKeyRestRequest $request)
    {
        return $this->repoGetKeyAsync($request)->wait();
    }

    public function repoGetKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetKeyRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetKey', $request);
    }

    public function repoDeleteKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteKeyRestRequest $request)
    {
        return $this->repoDeleteKeyAsync($request)->wait();
    }

    public function repoDeleteKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteKeyRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteKey', $request);
    }

    public function issueListLabels(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueListLabelsRestRequest $request)
    {
        return $this->issueListLabelsAsync($request)->wait();
    }

    public function issueListLabelsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueListLabelsRestRequest $request)
    {
        return $this->callOperationAsync('IssueListLabels', $request);
    }

    public function issueCreateLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateLabelRestRequest $request)
    {
        return $this->issueCreateLabelAsync($request)->wait();
    }

    public function issueCreateLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateLabel', $request);
    }

    public function issueGetLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetLabelRestRequest $request)
    {
        return $this->issueGetLabelAsync($request)->wait();
    }

    public function issueGetLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetLabel', $request);
    }

    public function issueDeleteLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteLabelRestRequest $request)
    {
        return $this->issueDeleteLabelAsync($request)->wait();
    }

    public function issueDeleteLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteLabel', $request);
    }

    public function issueEditLabel(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditLabelRestRequest $request)
    {
        return $this->issueEditLabelAsync($request)->wait();
    }

    public function issueEditLabelAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditLabel', $request);
    }

    public function repoGetLanguages(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetLanguagesRestRequest $request)
    {
        return $this->repoGetLanguagesAsync($request)->wait();
    }

    public function repoGetLanguagesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetLanguagesRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetLanguages', $request);
    }

    public function issueGetMilestonesList(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetMilestonesListRestRequest $request)
    {
        return $this->issueGetMilestonesListAsync($request)->wait();
    }

    public function issueGetMilestonesListAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetMilestonesListRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetMilestonesList', $request);
    }

    public function issueCreateMilestone(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateMilestoneRestRequest $request)
    {
        return $this->issueCreateMilestoneAsync($request)->wait();
    }

    public function issueCreateMilestoneAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueCreateMilestoneRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateMilestone', $request);
    }

    public function issueGetMilestone(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetMilestoneRestRequest $request)
    {
        return $this->issueGetMilestoneAsync($request)->wait();
    }

    public function issueGetMilestoneAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueGetMilestoneRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetMilestone', $request);
    }

    public function issueDeleteMilestone(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteMilestoneRestRequest $request)
    {
        return $this->issueDeleteMilestoneAsync($request)->wait();
    }

    public function issueDeleteMilestoneAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueDeleteMilestoneRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteMilestone', $request);
    }

    public function issueEditMilestone(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditMilestoneRestRequest $request)
    {
        return $this->issueEditMilestoneAsync($request)->wait();
    }

    public function issueEditMilestoneAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\IssueEditMilestoneRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditMilestone', $request);
    }

    public function repoMirrorSync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMirrorSyncRestRequest $request)
    {
        return $this->repoMirrorSyncAsync($request)->wait();
    }

    public function repoMirrorSyncAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMirrorSyncRestRequest $request)
    {
        return $this->callOperationAsync('RepoMirrorSync', $request);
    }

    public function notifyGetRepoList(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetRepoListRestRequest $request)
    {
        return $this->notifyGetRepoListAsync($request)->wait();
    }

    public function notifyGetRepoListAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyGetRepoListRestRequest $request)
    {
        return $this->callOperationAsync('NotifyGetRepoList', $request);
    }

    public function notifyReadRepoList(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadRepoListRestRequest $request)
    {
        return $this->notifyReadRepoListAsync($request)->wait();
    }

    public function notifyReadRepoListAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\NotifyReadRepoListRestRequest $request)
    {
        return $this->callOperationAsync('NotifyReadRepoList', $request);
    }

    public function repoListPullRequests(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListPullRequestsRestRequest $request)
    {
        return $this->repoListPullRequestsAsync($request)->wait();
    }

    public function repoListPullRequestsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListPullRequestsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListPullRequests', $request);
    }

    public function repoCreatePullRequest(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullRequestRestRequest $request)
    {
        return $this->repoCreatePullRequestAsync($request)->wait();
    }

    public function repoCreatePullRequestAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreatePullRequest', $request);
    }

    public function repoGetPullRequest(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullRequestRestRequest $request)
    {
        return $this->repoGetPullRequestAsync($request)->wait();
    }

    public function repoGetPullRequestAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPullRequest', $request);
    }

    public function repoEditPullRequest(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditPullRequestRestRequest $request)
    {
        return $this->repoEditPullRequestAsync($request)->wait();
    }

    public function repoEditPullRequestAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditPullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditPullRequest', $request);
    }

    public function repoDownloadPullDiff(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDownloadPullDiffRestRequest $request)
    {
        return $this->repoDownloadPullDiffAsync($request)->wait();
    }

    public function repoDownloadPullDiffAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDownloadPullDiffRestRequest $request)
    {
        return $this->callOperationAsync('RepoDownloadPullDiff', $request);
    }

    public function repoDownloadPullPatch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDownloadPullPatchRestRequest $request)
    {
        return $this->repoDownloadPullPatchAsync($request)->wait();
    }

    public function repoDownloadPullPatchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDownloadPullPatchRestRequest $request)
    {
        return $this->callOperationAsync('RepoDownloadPullPatch', $request);
    }

    public function repoPullRequestIsMerged(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoPullRequestIsMergedRestRequest $request)
    {
        return $this->repoPullRequestIsMergedAsync($request)->wait();
    }

    public function repoPullRequestIsMergedAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoPullRequestIsMergedRestRequest $request)
    {
        return $this->callOperationAsync('RepoPullRequestIsMerged', $request);
    }

    public function repoMergePullRequest(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMergePullRequestRestRequest $request)
    {
        return $this->repoMergePullRequestAsync($request)->wait();
    }

    public function repoMergePullRequestAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoMergePullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoMergePullRequest', $request);
    }

    public function repoCreatePullReviewRequests(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullReviewRequestsRestRequest $request)
    {
        return $this->repoCreatePullReviewRequestsAsync($request)->wait();
    }

    public function repoCreatePullReviewRequestsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullReviewRequestsRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreatePullReviewRequests', $request);
    }

    public function repoDeletePullReviewRequests(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeletePullReviewRequestsRestRequest $request)
    {
        return $this->repoDeletePullReviewRequestsAsync($request)->wait();
    }

    public function repoDeletePullReviewRequestsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeletePullReviewRequestsRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeletePullReviewRequests', $request);
    }

    public function repoListPullReviews(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListPullReviewsRestRequest $request)
    {
        return $this->repoListPullReviewsAsync($request)->wait();
    }

    public function repoListPullReviewsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListPullReviewsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListPullReviews', $request);
    }

    public function repoCreatePullReview(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullReviewRestRequest $request)
    {
        return $this->repoCreatePullReviewAsync($request)->wait();
    }

    public function repoCreatePullReviewAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreatePullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreatePullReview', $request);
    }

    public function repoGetPullReview(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullReviewRestRequest $request)
    {
        return $this->repoGetPullReviewAsync($request)->wait();
    }

    public function repoGetPullReviewAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPullReview', $request);
    }

    public function repoSubmitPullReview(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSubmitPullReviewRestRequest $request)
    {
        return $this->repoSubmitPullReviewAsync($request)->wait();
    }

    public function repoSubmitPullReviewAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSubmitPullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoSubmitPullReview', $request);
    }

    public function repoDeletePullReview(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeletePullReviewRestRequest $request)
    {
        return $this->repoDeletePullReviewAsync($request)->wait();
    }

    public function repoDeletePullReviewAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeletePullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeletePullReview', $request);
    }

    public function repoGetPullReviewComments(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullReviewCommentsRestRequest $request)
    {
        return $this->repoGetPullReviewCommentsAsync($request)->wait();
    }

    public function repoGetPullReviewCommentsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetPullReviewCommentsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPullReviewComments', $request);
    }

    public function repoDismissPullReview(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDismissPullReviewRestRequest $request)
    {
        return $this->repoDismissPullReviewAsync($request)->wait();
    }

    public function repoDismissPullReviewAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDismissPullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoDismissPullReview', $request);
    }

    public function repoUnDismissPullReview(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUnDismissPullReviewRestRequest $request)
    {
        return $this->repoUnDismissPullReviewAsync($request)->wait();
    }

    public function repoUnDismissPullReviewAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUnDismissPullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoUnDismissPullReview', $request);
    }

    public function repoUpdatePullRequest(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdatePullRequestRestRequest $request)
    {
        return $this->repoUpdatePullRequestAsync($request)->wait();
    }

    public function repoUpdatePullRequestAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdatePullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoUpdatePullRequest', $request);
    }

    public function repoGetRawFile(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetRawFileRestRequest $request)
    {
        return $this->repoGetRawFileAsync($request)->wait();
    }

    public function repoGetRawFileAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetRawFileRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetRawFile', $request);
    }

    public function repoListReleases(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListReleasesRestRequest $request)
    {
        return $this->repoListReleasesAsync($request)->wait();
    }

    public function repoListReleasesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListReleasesRestRequest $request)
    {
        return $this->callOperationAsync('RepoListReleases', $request);
    }

    public function repoCreateRelease(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateReleaseRestRequest $request)
    {
        return $this->repoCreateReleaseAsync($request)->wait();
    }

    public function repoCreateReleaseAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateRelease', $request);
    }

    public function repoGetReleaseByTag(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseByTagRestRequest $request)
    {
        return $this->repoGetReleaseByTagAsync($request)->wait();
    }

    public function repoGetReleaseByTagAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseByTagRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetReleaseByTag', $request);
    }

    public function repoDeleteReleaseByTag(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseByTagRestRequest $request)
    {
        return $this->repoDeleteReleaseByTagAsync($request)->wait();
    }

    public function repoDeleteReleaseByTagAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseByTagRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteReleaseByTag', $request);
    }

    public function repoGetRelease(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseRestRequest $request)
    {
        return $this->repoGetReleaseAsync($request)->wait();
    }

    public function repoGetReleaseAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetRelease', $request);
    }

    public function repoDeleteRelease(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseRestRequest $request)
    {
        return $this->repoDeleteReleaseAsync($request)->wait();
    }

    public function repoDeleteReleaseAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteRelease', $request);
    }

    public function repoEditRelease(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditReleaseRestRequest $request)
    {
        return $this->repoEditReleaseAsync($request)->wait();
    }

    public function repoEditReleaseAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditRelease', $request);
    }

    public function repoListReleaseAttachments(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListReleaseAttachmentsRestRequest $request)
    {
        return $this->repoListReleaseAttachmentsAsync($request)->wait();
    }

    public function repoListReleaseAttachmentsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListReleaseAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListReleaseAttachments', $request);
    }

    public function repoCreateReleaseAttachment(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateReleaseAttachmentRestRequest $request)
    {
        return $this->repoCreateReleaseAttachmentAsync($request)->wait();
    }

    public function repoCreateReleaseAttachmentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateReleaseAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateReleaseAttachment', $request);
    }

    public function repoGetReleaseAttachment(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseAttachmentRestRequest $request)
    {
        return $this->repoGetReleaseAttachmentAsync($request)->wait();
    }

    public function repoGetReleaseAttachmentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetReleaseAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetReleaseAttachment', $request);
    }

    public function repoDeleteReleaseAttachment(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseAttachmentRestRequest $request)
    {
        return $this->repoDeleteReleaseAttachmentAsync($request)->wait();
    }

    public function repoDeleteReleaseAttachmentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteReleaseAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteReleaseAttachment', $request);
    }

    public function repoEditReleaseAttachment(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditReleaseAttachmentRestRequest $request)
    {
        return $this->repoEditReleaseAttachmentAsync($request)->wait();
    }

    public function repoEditReleaseAttachmentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoEditReleaseAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditReleaseAttachment', $request);
    }

    public function repoSigningKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSigningKeyRestRequest $request)
    {
        return $this->repoSigningKeyAsync($request)->wait();
    }

    public function repoSigningKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoSigningKeyRestRequest $request)
    {
        return $this->callOperationAsync('RepoSigningKey', $request);
    }

    public function repoListStargazers(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStargazersRestRequest $request)
    {
        return $this->repoListStargazersAsync($request)->wait();
    }

    public function repoListStargazersAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStargazersRestRequest $request)
    {
        return $this->callOperationAsync('RepoListStargazers', $request);
    }

    public function repoListStatuses(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStatusesRestRequest $request)
    {
        return $this->repoListStatusesAsync($request)->wait();
    }

    public function repoListStatusesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListStatusesRestRequest $request)
    {
        return $this->callOperationAsync('RepoListStatuses', $request);
    }

    public function repoCreateStatus(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateStatusRestRequest $request)
    {
        return $this->repoCreateStatusAsync($request)->wait();
    }

    public function repoCreateStatusAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCreateStatusRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateStatus', $request);
    }

    public function repoListSubscribers(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListSubscribersRestRequest $request)
    {
        return $this->repoListSubscribersAsync($request)->wait();
    }

    public function repoListSubscribersAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListSubscribersRestRequest $request)
    {
        return $this->callOperationAsync('RepoListSubscribers', $request);
    }

    public function userCurrentCheckSubscription(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckSubscriptionRestRequest $request)
    {
        return $this->userCurrentCheckSubscriptionAsync($request)->wait();
    }

    public function userCurrentCheckSubscriptionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentCheckSubscription', $request);
    }

    public function userCurrentPutSubscription(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutSubscriptionRestRequest $request)
    {
        return $this->userCurrentPutSubscriptionAsync($request)->wait();
    }

    public function userCurrentPutSubscriptionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPutSubscription', $request);
    }

    public function userCurrentDeleteSubscription(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteSubscriptionRestRequest $request)
    {
        return $this->userCurrentDeleteSubscriptionAsync($request)->wait();
    }

    public function userCurrentDeleteSubscriptionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteSubscription', $request);
    }

    public function repoListTags(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTagsRestRequest $request)
    {
        return $this->repoListTagsAsync($request)->wait();
    }

    public function repoListTagsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTagsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListTags', $request);
    }

    public function repoDeleteTag(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTagRestRequest $request)
    {
        return $this->repoDeleteTagAsync($request)->wait();
    }

    public function repoDeleteTagAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTagRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteTag', $request);
    }

    public function repoListTeams(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTeamsRestRequest $request)
    {
        return $this->repoListTeamsAsync($request)->wait();
    }

    public function repoListTeamsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTeamsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListTeams', $request);
    }

    public function repoCheckTeam(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCheckTeamRestRequest $request)
    {
        return $this->repoCheckTeamAsync($request)->wait();
    }

    public function repoCheckTeamAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoCheckTeamRestRequest $request)
    {
        return $this->callOperationAsync('RepoCheckTeam', $request);
    }

    public function repoAddTeam(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddTeamRestRequest $request)
    {
        return $this->repoAddTeamAsync($request)->wait();
    }

    public function repoAddTeamAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddTeamRestRequest $request)
    {
        return $this->callOperationAsync('RepoAddTeam', $request);
    }

    public function repoDeleteTeam(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTeamRestRequest $request)
    {
        return $this->repoDeleteTeamAsync($request)->wait();
    }

    public function repoDeleteTeamAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTeamRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteTeam', $request);
    }

    public function repoTrackedTimes(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTrackedTimesRestRequest $request)
    {
        return $this->repoTrackedTimesAsync($request)->wait();
    }

    public function repoTrackedTimesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTrackedTimesRestRequest $request)
    {
        return $this->callOperationAsync('RepoTrackedTimes', $request);
    }

    public function userTrackedTimes(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserTrackedTimesRestRequest $request)
    {
        return $this->userTrackedTimesAsync($request)->wait();
    }

    public function userTrackedTimesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserTrackedTimesRestRequest $request)
    {
        return $this->callOperationAsync('UserTrackedTimes', $request);
    }

    public function repoListTopics(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTopicsRestRequest $request)
    {
        return $this->repoListTopicsAsync($request)->wait();
    }

    public function repoListTopicsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoListTopicsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListTopics', $request);
    }

    public function repoUpdateTopics(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdateTopicsRestRequest $request)
    {
        return $this->repoUpdateTopicsAsync($request)->wait();
    }

    public function repoUpdateTopicsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoUpdateTopicsRestRequest $request)
    {
        return $this->callOperationAsync('RepoUpdateTopics', $request);
    }

    public function repoAddTopíc(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddTopícRestRequest $request)
    {
        return $this->repoAddTopícAsync($request)->wait();
    }

    public function repoAddTopícAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoAddTopícRestRequest $request)
    {
        return $this->callOperationAsync('RepoAddTopíc', $request);
    }

    public function repoDeleteTopic(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTopicRestRequest $request)
    {
        return $this->repoDeleteTopicAsync($request)->wait();
    }

    public function repoDeleteTopicAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoDeleteTopicRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteTopic', $request);
    }

    public function repoTransfer(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTransferRestRequest $request)
    {
        return $this->repoTransferAsync($request)->wait();
    }

    public function repoTransferAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoTransferRestRequest $request)
    {
        return $this->callOperationAsync('RepoTransfer', $request);
    }

    public function repoGetByID(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetByIDRestRequest $request)
    {
        return $this->repoGetByIDAsync($request)->wait();
    }

    public function repoGetByIDAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\RepoGetByIDRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetByID', $request);
    }

    public function getGeneralAPISettings(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralAPISettingsRestRequest $request)
    {
        return $this->getGeneralAPISettingsAsync($request)->wait();
    }

    public function getGeneralAPISettingsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralAPISettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetGeneralAPISettings', $request);
    }

    public function getGeneralAttachmentSettings(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralAttachmentSettingsRestRequest $request)
    {
        return $this->getGeneralAttachmentSettingsAsync($request)->wait();
    }

    public function getGeneralAttachmentSettingsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralAttachmentSettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetGeneralAttachmentSettings', $request);
    }

    public function getGeneralRepositorySettings(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralRepositorySettingsRestRequest $request)
    {
        return $this->getGeneralRepositorySettingsAsync($request)->wait();
    }

    public function getGeneralRepositorySettingsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralRepositorySettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetGeneralRepositorySettings', $request);
    }

    public function getGeneralUISettings(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralUISettingsRestRequest $request)
    {
        return $this->getGeneralUISettingsAsync($request)->wait();
    }

    public function getGeneralUISettingsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetGeneralUISettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetGeneralUISettings', $request);
    }

    public function getSigningKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetSigningKeyRestRequest $request)
    {
        return $this->getSigningKeyAsync($request)->wait();
    }

    public function getSigningKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetSigningKeyRestRequest $request)
    {
        return $this->callOperationAsync('GetSigningKey', $request);
    }

    public function orgGetTeam(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetTeamRestRequest $request)
    {
        return $this->orgGetTeamAsync($request)->wait();
    }

    public function orgGetTeamAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgGetTeamRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetTeam', $request);
    }

    public function orgDeleteTeam(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteTeamRestRequest $request)
    {
        return $this->orgDeleteTeamAsync($request)->wait();
    }

    public function orgDeleteTeamAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgDeleteTeamRestRequest $request)
    {
        return $this->callOperationAsync('OrgDeleteTeam', $request);
    }

    public function orgEditTeam(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditTeamRestRequest $request)
    {
        return $this->orgEditTeamAsync($request)->wait();
    }

    public function orgEditTeamAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgEditTeamRestRequest $request)
    {
        return $this->callOperationAsync('OrgEditTeam', $request);
    }

    public function orgListTeamMembers(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamMembersRestRequest $request)
    {
        return $this->orgListTeamMembersAsync($request)->wait();
    }

    public function orgListTeamMembersAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamMembersRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeamMembers', $request);
    }

    public function orgListTeamMember(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamMemberRestRequest $request)
    {
        return $this->orgListTeamMemberAsync($request)->wait();
    }

    public function orgListTeamMemberAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeamMember', $request);
    }

    public function orgAddTeamMember(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgAddTeamMemberRestRequest $request)
    {
        return $this->orgAddTeamMemberAsync($request)->wait();
    }

    public function orgAddTeamMemberAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgAddTeamMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgAddTeamMember', $request);
    }

    public function orgRemoveTeamMember(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgRemoveTeamMemberRestRequest $request)
    {
        return $this->orgRemoveTeamMemberAsync($request)->wait();
    }

    public function orgRemoveTeamMemberAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgRemoveTeamMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgRemoveTeamMember', $request);
    }

    public function orgListTeamRepos(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamReposRestRequest $request)
    {
        return $this->orgListTeamReposAsync($request)->wait();
    }

    public function orgListTeamReposAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListTeamReposRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeamRepos', $request);
    }

    public function orgAddTeamRepository(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgAddTeamRepositoryRestRequest $request)
    {
        return $this->orgAddTeamRepositoryAsync($request)->wait();
    }

    public function orgAddTeamRepositoryAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgAddTeamRepositoryRestRequest $request)
    {
        return $this->callOperationAsync('OrgAddTeamRepository', $request);
    }

    public function orgRemoveTeamRepository(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgRemoveTeamRepositoryRestRequest $request)
    {
        return $this->orgRemoveTeamRepositoryAsync($request)->wait();
    }

    public function orgRemoveTeamRepositoryAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgRemoveTeamRepositoryRestRequest $request)
    {
        return $this->callOperationAsync('OrgRemoveTeamRepository', $request);
    }

    public function topicSearch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\TopicSearchRestRequest $request)
    {
        return $this->topicSearchAsync($request)->wait();
    }

    public function topicSearchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\TopicSearchRestRequest $request)
    {
        return $this->callOperationAsync('TopicSearch', $request);
    }

    public function userGetCurrent(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetCurrentRestRequest $request)
    {
        return $this->userGetCurrentAsync($request)->wait();
    }

    public function userGetCurrentAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetCurrentRestRequest $request)
    {
        return $this->callOperationAsync('UserGetCurrent', $request);
    }

    public function userGetOauth2Application(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetOauth2ApplicationRestRequest $request)
    {
        return $this->userGetOauth2ApplicationAsync($request)->wait();
    }

    public function userGetOauth2ApplicationAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetOauth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserGetOauth2Application', $request);
    }

    public function userCreateOAuth2Application(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCreateOAuth2ApplicationRestRequest $request)
    {
        return $this->userCreateOAuth2ApplicationAsync($request)->wait();
    }

    public function userCreateOAuth2ApplicationAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCreateOAuth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserCreateOAuth2Application', $request);
    }

    public function userGetOAuth2Application(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetOAuth2ApplicationRestRequest $request)
    {
        return $this->userGetOAuth2ApplicationAsync($request)->wait();
    }

    public function userGetOAuth2ApplicationAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetOAuth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserGetOAuth2Application', $request);
    }

    public function userDeleteOAuth2Application(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteOAuth2ApplicationRestRequest $request)
    {
        return $this->userDeleteOAuth2ApplicationAsync($request)->wait();
    }

    public function userDeleteOAuth2ApplicationAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteOAuth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserDeleteOAuth2Application', $request);
    }

    public function userUpdateOAuth2Application(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserUpdateOAuth2ApplicationRestRequest $request)
    {
        return $this->userUpdateOAuth2ApplicationAsync($request)->wait();
    }

    public function userUpdateOAuth2ApplicationAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserUpdateOAuth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserUpdateOAuth2Application', $request);
    }

    public function userListEmails(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListEmailsRestRequest $request)
    {
        return $this->userListEmailsAsync($request)->wait();
    }

    public function userListEmailsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListEmailsRestRequest $request)
    {
        return $this->callOperationAsync('UserListEmails', $request);
    }

    public function userAddEmail(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserAddEmailRestRequest $request)
    {
        return $this->userAddEmailAsync($request)->wait();
    }

    public function userAddEmailAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserAddEmailRestRequest $request)
    {
        return $this->callOperationAsync('UserAddEmail', $request);
    }

    public function userDeleteEmail(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteEmailRestRequest $request)
    {
        return $this->userDeleteEmailAsync($request)->wait();
    }

    public function userDeleteEmailAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteEmailRestRequest $request)
    {
        return $this->callOperationAsync('UserDeleteEmail', $request);
    }

    public function userCurrentListFollowers(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListFollowersRestRequest $request)
    {
        return $this->userCurrentListFollowersAsync($request)->wait();
    }

    public function userCurrentListFollowersAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListFollowersRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListFollowers', $request);
    }

    public function userCurrentListFollowing(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListFollowingRestRequest $request)
    {
        return $this->userCurrentListFollowingAsync($request)->wait();
    }

    public function userCurrentListFollowingAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListFollowingRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListFollowing', $request);
    }

    public function userCurrentCheckFollowing(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckFollowingRestRequest $request)
    {
        return $this->userCurrentCheckFollowingAsync($request)->wait();
    }

    public function userCurrentCheckFollowingAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckFollowingRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentCheckFollowing', $request);
    }

    public function userCurrentPutFollow(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutFollowRestRequest $request)
    {
        return $this->userCurrentPutFollowAsync($request)->wait();
    }

    public function userCurrentPutFollowAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutFollowRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPutFollow', $request);
    }

    public function userCurrentDeleteFollow(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteFollowRestRequest $request)
    {
        return $this->userCurrentDeleteFollowAsync($request)->wait();
    }

    public function userCurrentDeleteFollowAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteFollowRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteFollow', $request);
    }

    public function userCurrentListGPGKeys(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListGPGKeysRestRequest $request)
    {
        return $this->userCurrentListGPGKeysAsync($request)->wait();
    }

    public function userCurrentListGPGKeysAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListGPGKeysRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListGPGKeys', $request);
    }

    public function userCurrentPostGPGKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPostGPGKeyRestRequest $request)
    {
        return $this->userCurrentPostGPGKeyAsync($request)->wait();
    }

    public function userCurrentPostGPGKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPostGPGKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPostGPGKey', $request);
    }

    public function userCurrentGetGPGKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentGetGPGKeyRestRequest $request)
    {
        return $this->userCurrentGetGPGKeyAsync($request)->wait();
    }

    public function userCurrentGetGPGKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentGetGPGKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentGetGPGKey', $request);
    }

    public function userCurrentDeleteGPGKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteGPGKeyRestRequest $request)
    {
        return $this->userCurrentDeleteGPGKeyAsync($request)->wait();
    }

    public function userCurrentDeleteGPGKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteGPGKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteGPGKey', $request);
    }

    public function userCurrentListKeys(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListKeysRestRequest $request)
    {
        return $this->userCurrentListKeysAsync($request)->wait();
    }

    public function userCurrentListKeysAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListKeysRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListKeys', $request);
    }

    public function userCurrentPostKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPostKeyRestRequest $request)
    {
        return $this->userCurrentPostKeyAsync($request)->wait();
    }

    public function userCurrentPostKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPostKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPostKey', $request);
    }

    public function userCurrentGetKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentGetKeyRestRequest $request)
    {
        return $this->userCurrentGetKeyAsync($request)->wait();
    }

    public function userCurrentGetKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentGetKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentGetKey', $request);
    }

    public function userCurrentDeleteKey(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteKeyRestRequest $request)
    {
        return $this->userCurrentDeleteKeyAsync($request)->wait();
    }

    public function userCurrentDeleteKeyAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteKey', $request);
    }

    public function orgListCurrentUserOrgs(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListCurrentUserOrgsRestRequest $request)
    {
        return $this->orgListCurrentUserOrgsAsync($request)->wait();
    }

    public function orgListCurrentUserOrgsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListCurrentUserOrgsRestRequest $request)
    {
        return $this->callOperationAsync('OrgListCurrentUserOrgs', $request);
    }

    public function userCurrentListRepos(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListReposRestRequest $request)
    {
        return $this->userCurrentListReposAsync($request)->wait();
    }

    public function userCurrentListReposAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListReposRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListRepos', $request);
    }

    public function createCurrentUserRepo(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateCurrentUserRepoRestRequest $request)
    {
        return $this->createCurrentUserRepoAsync($request)->wait();
    }

    public function createCurrentUserRepoAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\CreateCurrentUserRepoRestRequest $request)
    {
        return $this->callOperationAsync('CreateCurrentUserRepo', $request);
    }

    public function userCurrentListStarred(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListStarredRestRequest $request)
    {
        return $this->userCurrentListStarredAsync($request)->wait();
    }

    public function userCurrentListStarredAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListStarredRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListStarred', $request);
    }

    public function userCurrentCheckStarring(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckStarringRestRequest $request)
    {
        return $this->userCurrentCheckStarringAsync($request)->wait();
    }

    public function userCurrentCheckStarringAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentCheckStarringRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentCheckStarring', $request);
    }

    public function userCurrentPutStar(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutStarRestRequest $request)
    {
        return $this->userCurrentPutStarAsync($request)->wait();
    }

    public function userCurrentPutStarAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentPutStarRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPutStar', $request);
    }

    public function userCurrentDeleteStar(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteStarRestRequest $request)
    {
        return $this->userCurrentDeleteStarAsync($request)->wait();
    }

    public function userCurrentDeleteStarAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentDeleteStarRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteStar', $request);
    }

    public function userGetStopWatches(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetStopWatchesRestRequest $request)
    {
        return $this->userGetStopWatchesAsync($request)->wait();
    }

    public function userGetStopWatchesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetStopWatchesRestRequest $request)
    {
        return $this->callOperationAsync('UserGetStopWatches', $request);
    }

    public function userCurrentListSubscriptions(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListSubscriptionsRestRequest $request)
    {
        return $this->userCurrentListSubscriptionsAsync($request)->wait();
    }

    public function userCurrentListSubscriptionsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentListSubscriptionsRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListSubscriptions', $request);
    }

    public function userListTeams(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListTeamsRestRequest $request)
    {
        return $this->userListTeamsAsync($request)->wait();
    }

    public function userListTeamsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListTeamsRestRequest $request)
    {
        return $this->callOperationAsync('UserListTeams', $request);
    }

    public function userCurrentTrackedTimes(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentTrackedTimesRestRequest $request)
    {
        return $this->userCurrentTrackedTimesAsync($request)->wait();
    }

    public function userCurrentTrackedTimesAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCurrentTrackedTimesRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentTrackedTimes', $request);
    }

    public function userSearch(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserSearchRestRequest $request)
    {
        return $this->userSearchAsync($request)->wait();
    }

    public function userSearchAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserSearchRestRequest $request)
    {
        return $this->callOperationAsync('UserSearch', $request);
    }

    public function userCheckFollowing(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCheckFollowingRestRequest $request)
    {
        return $this->userCheckFollowingAsync($request)->wait();
    }

    public function userCheckFollowingAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCheckFollowingRestRequest $request)
    {
        return $this->callOperationAsync('UserCheckFollowing', $request);
    }

    public function userGet(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetRestRequest $request)
    {
        return $this->userGetAsync($request)->wait();
    }

    public function userGetAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetRestRequest $request)
    {
        return $this->callOperationAsync('UserGet', $request);
    }

    public function userListFollowers(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListFollowersRestRequest $request)
    {
        return $this->userListFollowersAsync($request)->wait();
    }

    public function userListFollowersAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListFollowersRestRequest $request)
    {
        return $this->callOperationAsync('UserListFollowers', $request);
    }

    public function userListFollowing(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListFollowingRestRequest $request)
    {
        return $this->userListFollowingAsync($request)->wait();
    }

    public function userListFollowingAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListFollowingRestRequest $request)
    {
        return $this->callOperationAsync('UserListFollowing', $request);
    }

    public function userListGPGKeys(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListGPGKeysRestRequest $request)
    {
        return $this->userListGPGKeysAsync($request)->wait();
    }

    public function userListGPGKeysAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListGPGKeysRestRequest $request)
    {
        return $this->callOperationAsync('UserListGPGKeys', $request);
    }

    public function userGetHeatmapData(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetHeatmapDataRestRequest $request)
    {
        return $this->userGetHeatmapDataAsync($request)->wait();
    }

    public function userGetHeatmapDataAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetHeatmapDataRestRequest $request)
    {
        return $this->callOperationAsync('UserGetHeatmapData', $request);
    }

    public function userListKeys(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListKeysRestRequest $request)
    {
        return $this->userListKeysAsync($request)->wait();
    }

    public function userListKeysAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListKeysRestRequest $request)
    {
        return $this->callOperationAsync('UserListKeys', $request);
    }

    public function orgListUserOrgs(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListUserOrgsRestRequest $request)
    {
        return $this->orgListUserOrgsAsync($request)->wait();
    }

    public function orgListUserOrgsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\OrgListUserOrgsRestRequest $request)
    {
        return $this->callOperationAsync('OrgListUserOrgs', $request);
    }

    public function userListRepos(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListReposRestRequest $request)
    {
        return $this->userListReposAsync($request)->wait();
    }

    public function userListReposAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListReposRestRequest $request)
    {
        return $this->callOperationAsync('UserListRepos', $request);
    }

    public function userListStarred(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListStarredRestRequest $request)
    {
        return $this->userListStarredAsync($request)->wait();
    }

    public function userListStarredAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListStarredRestRequest $request)
    {
        return $this->callOperationAsync('UserListStarred', $request);
    }

    public function userListSubscriptions(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListSubscriptionsRestRequest $request)
    {
        return $this->userListSubscriptionsAsync($request)->wait();
    }

    public function userListSubscriptionsAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserListSubscriptionsRestRequest $request)
    {
        return $this->callOperationAsync('UserListSubscriptions', $request);
    }

    public function userGetTokens(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetTokensRestRequest $request)
    {
        return $this->userGetTokensAsync($request)->wait();
    }

    public function userGetTokensAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserGetTokensRestRequest $request)
    {
        return $this->callOperationAsync('UserGetTokens', $request);
    }

    public function userCreateToken(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCreateTokenRestRequest $request)
    {
        return $this->userCreateTokenAsync($request)->wait();
    }

    public function userCreateTokenAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserCreateTokenRestRequest $request)
    {
        return $this->callOperationAsync('UserCreateToken', $request);
    }

    public function userDeleteAccessToken(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteAccessTokenRestRequest $request)
    {
        return $this->userDeleteAccessTokenAsync($request)->wait();
    }

    public function userDeleteAccessTokenAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\UserDeleteAccessTokenRestRequest $request)
    {
        return $this->callOperationAsync('UserDeleteAccessToken', $request);
    }

    public function getVersion(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetVersionRestRequest $request)
    {
        return $this->getVersionAsync($request)->wait();
    }

    public function getVersionAsync(\Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Operations\GetVersionRestRequest $request)
    {
        return $this->callOperationAsync('GetVersion', $request);
    }
}