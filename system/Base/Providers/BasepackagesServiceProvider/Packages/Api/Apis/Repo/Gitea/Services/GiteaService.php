<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Services;

use System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Services\GiteaBaseService;

class GiteaService extends GiteaBaseService
{
    protected static $operations =
        [
        'ActivitypubPerson' => [
          'method' => 'GET',
          'resource' => 'activitypub/user/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ActivitypubPersonRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'ActivitypubPersonInbox' => [
          'method' => 'POST',
          'resource' => 'activitypub/user/{username}/inbox',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ActivitypubPersonInboxRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminCronList' => [
          'method' => 'GET',
          'resource' => 'admin/cron',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCronListRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminCronRun' => [
          'method' => 'POST',
          'resource' => 'admin/cron/{task}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCronRunRestResponse',
          'params' => [
            'task' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminListHooks' => [
          'method' => 'GET',
          'resource' => 'admin/hooks',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminListHooksRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminCreateHook' => [
          'method' => 'POST',
          'resource' => 'admin/hooks',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateHookRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminGetHook' => [
          'method' => 'GET',
          'resource' => 'admin/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetHookRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminEditHook' => [
          'method' => 'PATCH',
          'resource' => 'admin/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminEditHookRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminGetAllOrgs' => [
          'method' => 'GET',
          'resource' => 'admin/orgs',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetAllOrgsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminUnadoptedList' => [
          'method' => 'GET',
          'resource' => 'admin/unadopted',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminUnadoptedListRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
            'pattern' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminAdoptRepository' => [
          'method' => 'POST',
          'resource' => 'admin/unadopted/{owner}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminAdoptRepositoryRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminDeleteUnadoptedRepository' => [
          'method' => 'DELETE',
          'resource' => 'admin/unadopted/{owner}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUnadoptedRepositoryRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminGetAllUsers' => [
          'method' => 'GET',
          'resource' => 'admin/users',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetAllUsersRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminCreateUser' => [
          'method' => 'POST',
          'resource' => 'admin/users',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateUserRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminDeleteUser' => [
          'method' => 'DELETE',
          'resource' => 'admin/users/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUserRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'purge' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminEditUser' => [
          'method' => 'PATCH',
          'resource' => 'admin/users/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminEditUserRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminCreatePublicKey' => [
          'method' => 'POST',
          'resource' => 'admin/users/{username}/keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreatePublicKeyRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'key' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'AdminDeleteUserPublicKey' => [
          'method' => 'DELETE',
          'resource' => 'admin/users/{username}/keys/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUserPublicKeyRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminCreateOrg' => [
          'method' => 'POST',
          'resource' => 'admin/users/{username}/orgs',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateOrgRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'organization' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminCreateRepo' => [
          'method' => 'POST',
          'resource' => 'admin/users/{username}/repos',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateRepoRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repository' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AdminDeleteHook' => [
          'method' => 'DELETE',
          'resource' => 'amdin/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteHookRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RenderMarkdown' => [
          'method' => 'POST',
          'resource' => 'markdown',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RenderMarkdownRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RenderMarkdownRaw' => [
          'method' => 'POST',
          'resource' => 'markdown/raw',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RenderMarkdownRawRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetNodeInfo' => [
          'method' => 'GET',
          'resource' => 'nodeinfo',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetNodeInfoRestResponse',
          'params' => [
          ],
        ],
        'NotifyGetList' => [
          'method' => 'GET',
          'resource' => 'notifications',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetListRestResponse',
          'params' => [
            'all' => [
              'valid' => [
              ],
            ],
            'status-types' => [
              'valid' => [
              ],
            ],
            'subject-type' => [
              'valid' => [
              ],
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'NotifyReadList' => [
          'method' => 'PUT',
          'resource' => 'notifications',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadListRestResponse',
          'params' => [
            'last_read_at' => [
              'valid' => [
              ],
            ],
            'all' => [
              'valid' => [
              ],
            ],
            'status-types' => [
              'valid' => [
              ],
            ],
            'to-status' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'NotifyNewAvailable' => [
          'method' => 'GET',
          'resource' => 'notifications/new',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyNewAvailableRestResponse',
          'params' => [
          ],
        ],
        'NotifyGetThread' => [
          'method' => 'GET',
          'resource' => 'notifications/threads/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetThreadRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'NotifyReadThread' => [
          'method' => 'PATCH',
          'resource' => 'notifications/threads/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadThreadRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'to-status' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'CreateOrgRepoDeprecated' => [
          'method' => 'POST',
          'resource' => 'org/{org}/repos',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateOrgRepoDeprecatedRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgGetAll' => [
          'method' => 'GET',
          'resource' => 'orgs',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetAllRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgCreate' => [
          'method' => 'POST',
          'resource' => 'orgs',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateRestResponse',
          'params' => [
            'organization' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgGet' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDelete' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgEdit' => [
          'method' => 'PATCH',
          'resource' => 'orgs/{org}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListHooks' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/hooks',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListHooksRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgCreateHook' => [
          'method' => 'POST',
          'resource' => 'orgs/{org}/hooks',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateHookRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgGetHook' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetHookRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDeleteHook' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteHookRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgEditHook' => [
          'method' => 'PATCH',
          'resource' => 'orgs/{org}/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditHookRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgListLabels' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/labels',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListLabelsRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgCreateLabel' => [
          'method' => 'POST',
          'resource' => 'orgs/{org}/labels',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateLabelRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgGetLabel' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/labels/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetLabelRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDeleteLabel' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}/labels/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteLabelRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgEditLabel' => [
          'method' => 'PATCH',
          'resource' => 'orgs/{org}/labels/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditLabelRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgListMembers' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/members',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListMembersRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgIsMember' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/members/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgIsMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDeleteMember' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}/members/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListPublicMembers' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/public_members',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListPublicMembersRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgIsPublicMember' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/public_members/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgIsPublicMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgPublicizeMember' => [
          'method' => 'PUT',
          'resource' => 'orgs/{org}/public_members/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgPublicizeMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgConcealMember' => [
          'method' => 'DELETE',
          'resource' => 'orgs/{org}/public_members/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgConcealMemberRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListRepos' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/repos',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListReposRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'CreateOrgRepo' => [
          'method' => 'POST',
          'resource' => 'orgs/{org}/repos',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateOrgRepoRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgListTeams' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/teams',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamsRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgCreateTeam' => [
          'method' => 'POST',
          'resource' => 'orgs/{org}/teams',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateTeamRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'TeamSearch' => [
          'method' => 'GET',
          'resource' => 'orgs/{org}/teams/search',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\TeamSearchRestResponse',
          'params' => [
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'q' => [
              'valid' => [
              ],
            ],
            'include_desc' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'ListPackages' => [
          'method' => 'GET',
          'resource' => 'packages/{owner}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListPackagesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
            'type' => [
              'valid' => [
              ],
            ],
            'q' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'GetPackage' => [
          'method' => 'GET',
          'resource' => 'packages/{owner}/{type}/{name}/{version}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetPackageRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'type' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'version' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'DeletePackage' => [
          'method' => 'DELETE',
          'resource' => 'packages/{owner}/{type}/{name}/{version}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\DeletePackageRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'type' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'version' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'ListPackageFiles' => [
          'method' => 'GET',
          'resource' => 'packages/{owner}/{type}/{name}/{version}/files',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListPackageFilesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'type' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'version' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueSearchIssues' => [
          'method' => 'GET',
          'resource' => 'repos/issues/search',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueSearchIssuesRestResponse',
          'params' => [
            'state' => [
              'valid' => [
              ],
            ],
            'labels' => [
              'valid' => [
              ],
            ],
            'milestones' => [
              'valid' => [
              ],
            ],
            'q' => [
              'valid' => [
              ],
            ],
            'priority_repo_id' => [
              'valid' => [
              ],
            ],
            'type' => [
              'valid' => [
              ],
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
            'assigned' => [
              'valid' => [
              ],
            ],
            'created' => [
              'valid' => [
              ],
            ],
            'mentioned' => [
              'valid' => [
              ],
            ],
            'review_requested' => [
              'valid' => [
              ],
            ],
            'owner' => [
              'valid' => [
              ],
            ],
            'team' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoMigrate' => [
          'method' => 'POST',
          'resource' => 'repos/migrate',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMigrateRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoSearch' => [
          'method' => 'GET',
          'resource' => 'repos/search',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSearchRestResponse',
          'params' => [
            'q' => [
              'valid' => [
              ],
            ],
            'topic' => [
              'valid' => [
              ],
            ],
            'includeDesc' => [
              'valid' => [
              ],
            ],
            'uid' => [
              'valid' => [
              ],
            ],
            'priority_owner_id' => [
              'valid' => [
              ],
            ],
            'team_id' => [
              'valid' => [
              ],
            ],
            'starredBy' => [
              'valid' => [
              ],
            ],
            'private' => [
              'valid' => [
              ],
            ],
            'is_private' => [
              'valid' => [
              ],
            ],
            'template' => [
              'valid' => [
              ],
            ],
            'archived' => [
              'valid' => [
              ],
            ],
            'mode' => [
              'valid' => [
              ],
            ],
            'exclusive' => [
              'valid' => [
              ],
            ],
            'sort' => [
              'valid' => [
              ],
            ],
            'order' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGet' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDelete' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEdit' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetArchive' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/archive/{archive}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetArchiveRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'archive' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetAssignees' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/assignees',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetAssigneesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListBranchProtection' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/branch_protections',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreateBranchProtection' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/branch_protections',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetBranchProtection' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/branch_protections/{name}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteBranchProtection' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/branch_protections/{name}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditBranchProtection' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/branch_protections/{name}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditBranchProtectionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListBranches' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/branches',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListBranchesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCreateBranch' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/branches',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateBranchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetBranch' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/branches/{branch}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetBranchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'branch' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteBranch' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/branches/{branch}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteBranchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'branch' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListCollaborators' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/collaborators',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListCollaboratorsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCheckCollaborator' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/collaborators/{collaborator}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCheckCollaboratorRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'collaborator' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoAddCollaborator' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/collaborators/{collaborator}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddCollaboratorRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'collaborator' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoDeleteCollaborator' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/collaborators/{collaborator}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteCollaboratorRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'collaborator' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetRepoPermissions' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/collaborators/{collaborator}/permission',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRepoPermissionsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'collaborator' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetAllCommits' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/commits',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetAllCommitsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
            ],
            'path' => [
              'valid' => [
              ],
            ],
            'stat' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetCombinedStatusByRef' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/commits/{ref}/status',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetCombinedStatusByRefRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListStatusesByRef' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/commits/{ref}/statuses',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStatusesByRefRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sort' => [
              'valid' => [
              ],
            ],
            'state' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetContentsList' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/contents',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetContentsListRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetContents' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/contents/{filepath}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetContentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoUpdateFile' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/contents/{filepath}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdateFileRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreateFile' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/contents/{filepath}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateFileRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteFile' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/contents/{filepath}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteFileRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoApplyDiffPatch' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/diffpatch',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoApplyDiffPatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetEditorConfig' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/editorconfig/{filepath}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetEditorConfigRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'ListForks' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/forks',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListForksRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'CreateFork' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/forks',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateForkRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'GetBlob' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/blobs/{sha}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetBlobRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetSingleCommit' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/commits/{sha}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetSingleCommitRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDownloadCommitDiffOrPatch' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/commits/{sha}.{diffType}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDownloadCommitDiffOrPatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'diffType' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetNote' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/notes/{sha}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetNoteRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListAllGitRefs' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/refs',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListAllGitRefsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListGitRefs' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/refs/{ref}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListGitRefsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetAnnotatedTag' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/tags/{sha}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetAnnotatedTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetTree' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/git/trees/{sha}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetTreeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'recursive' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'per_page' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListHooks' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/hooks',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListHooksRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCreateHook' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/hooks',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListGitHooks' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/hooks/git',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListGitHooksRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetGitHook' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/hooks/git/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetGitHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteGitHook' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/hooks/git/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteGitHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditGitHook' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/hooks/git/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditGitHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetHook' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteHook' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditHook' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/hooks/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoTestHook' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/hooks/{id}/tests',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTestHookRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetIssueTemplates' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issue_templates',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetIssueTemplatesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueListIssues' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssuesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'state' => [
              'valid' => [
              ],
            ],
            'labels' => [
              'valid' => [
              ],
            ],
            'q' => [
              'valid' => [
              ],
            ],
            'type' => [
              'valid' => [
              ],
            ],
            'milestones' => [
              'valid' => [
              ],
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
            'created_by' => [
              'valid' => [
              ],
            ],
            'assigned_by' => [
              'valid' => [
              ],
            ],
            'mentioned_by' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueCreateIssue' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetRepoComments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/comments',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetRepoCommentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetComment' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteComment' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditComment' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditCommentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueListIssueCommentAttachments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/assets',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssueCommentAttachmentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueCreateIssueCommentAttachment' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/assets',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueCommentAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
            ],
            'attachment' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetIssueCommentAttachment' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueCommentAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteIssueCommentAttachment' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueCommentAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditIssueCommentAttachment' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueCommentAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetCommentReactions' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/reactions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentReactionsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssuePostCommentReaction' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/reactions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssuePostCommentReactionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'content' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueDeleteCommentReaction' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/comments/{id}/reactions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentReactionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'content' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetIssue' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDelete' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditIssue' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/issues/{index}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueListIssueAttachments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/assets',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssueAttachmentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueCreateIssueAttachment' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/assets',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
            ],
            'attachment' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetIssueAttachment' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteIssueAttachment' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditIssueAttachment' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetComments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/comments',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueCreateComment' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/comments',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateCommentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueDeleteCommentDeprecated' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/comments/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentDeprecatedRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditCommentDeprecated' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/comments/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditCommentDeprecatedRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueEditIssueDeadline' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/deadline',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueDeadlineRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetLabels' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetLabelsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueReplaceLabels' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueReplaceLabelsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueAddLabel' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueClearLabels' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueClearLabelsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueRemoveLabel' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/labels/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueRemoveLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetIssueReactions' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/reactions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueReactionsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssuePostIssueReaction' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/reactions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssuePostIssueReactionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'content' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueDeleteIssueReaction' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/reactions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueReactionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'content' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueDeleteStopWatch' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/stopwatch/delete',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteStopWatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueStartStopWatch' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/stopwatch/start',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueStartStopWatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueStopStopWatch' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/stopwatch/stop',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueStopStopWatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueSubscriptions' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/subscriptions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueSubscriptionsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueCheckSubscription' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/subscriptions/check',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCheckSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueAddSubscription' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/subscriptions/{user}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteSubscription' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/subscriptions/{user}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueGetCommentsAndTimeline' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/timeline',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentsAndTimelineRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueTrackedTimes' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/times',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueTrackedTimesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
              ],
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueAddTime' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/times',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddTimeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueResetTime' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/times',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueResetTimeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteTime' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/issues/{index}/times/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteTimeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListKeys' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListKeysRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'key_id' => [
              'valid' => [
              ],
            ],
            'fingerprint' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCreateKey' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateKeyRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetKey' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/keys/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetKeyRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteKey' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/keys/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteKeyRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueListLabels' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/labels',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListLabelsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueCreateLabel' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/labels',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetLabel' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/labels/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteLabel' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/labels/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditLabel' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/labels/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditLabelRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetLanguages' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/languages',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetLanguagesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetRawFileOrLFS' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/media/{filepath}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRawFileOrLFSRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetMilestonesList' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/milestones',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetMilestonesListRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'state' => [
              'valid' => [
              ],
            ],
            'name' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueCreateMilestone' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/milestones',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateMilestoneRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'IssueGetMilestone' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/milestones/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetMilestoneRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueDeleteMilestone' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/milestones/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteMilestoneRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'IssueEditMilestone' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/milestones/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditMilestoneRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoMirrorSync' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/mirror-sync',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMirrorSyncRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'NotifyGetRepoList' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/notifications',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetRepoListRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'all' => [
              'valid' => [
              ],
            ],
            'status-types' => [
              'valid' => [
              ],
            ],
            'subject-type' => [
              'valid' => [
              ],
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'NotifyReadRepoList' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/notifications',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadRepoListRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'all' => [
              'valid' => [
              ],
            ],
            'status-types' => [
              'valid' => [
              ],
            ],
            'to-status' => [
              'valid' => [
              ],
            ],
            'last_read_at' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListPullRequests' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPullRequestsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'state' => [
              'valid' => [
              ],
            ],
            'sort' => [
              'valid' => [
              ],
            ],
            'milestone' => [
              'valid' => [
              ],
            ],
            'labels' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCreatePullRequest' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetPullRequest' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditPullRequest' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditPullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoDownloadPullDiffOrPatch' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}.{diffType}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDownloadPullDiffOrPatchRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'diffType' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'binary' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetPullRequestCommits' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/commits',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestCommitsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetPullRequestFiles' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/files',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestFilesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'skip-to' => [
              'valid' => [
              ],
            ],
            'whitespace' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoPullRequestIsMerged' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/merge',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoPullRequestIsMergedRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoMergePullRequest' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/merge',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMergePullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCancelScheduledAutoMerge' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/merge',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCancelScheduledAutoMergeRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreatePullReviewRequests' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/requested_reviewers',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullReviewRequestsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeletePullReviewRequests' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/requested_reviewers',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePullReviewRequestsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListPullReviews' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPullReviewsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCreatePullReview' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetPullReview' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoSubmitPullReview' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSubmitPullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeletePullReview' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetPullReviewComments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}/comments',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullReviewCommentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDismissPullReview' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}/dismissals',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDismissPullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoUnDismissPullReview' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/reviews/{id}/undismissals',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUnDismissPullReviewRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoUpdatePullRequest' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/pulls/{index}/update',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdatePullRequestRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'index' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'style' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListPushMirrors' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/push_mirrors',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPushMirrorsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoAddPushMirror' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/push_mirrors',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddPushMirrorRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoPushMirrorSync' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/push_mirrors-sync',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoPushMirrorSyncRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetPushMirrorByRemoteName' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/push_mirrors/{name}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPushMirrorByRemoteNameRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeletePushMirror' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/push_mirrors/{name}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePushMirrorRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetRawFile' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/raw/{filepath}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRawFileRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'filepath' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'ref' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListReleases' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListReleasesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'draft' => [
              'valid' => [
              ],
            ],
            'pre-release' => [
              'valid' => [
              ],
            ],
            'per_page' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCreateRelease' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/releases',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetLatestRelease' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/latest',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetLatestReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetReleaseByTag' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/tags/{tag}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseByTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'tag' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteReleaseByTag' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/releases/tags/{tag}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseByTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'tag' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetRelease' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteRelease' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/releases/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditRelease' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/releases/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditReleaseRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListReleaseAttachments' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListReleaseAttachmentsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreateReleaseAttachment' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateReleaseAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'name' => [
              'valid' => [
              ],
            ],
            'attachment' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoGetReleaseAttachment' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteReleaseAttachment' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditReleaseAttachment' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/releases/{id}/assets/{attachment_id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditReleaseAttachmentRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'attachment_id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetReviewers' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/reviewers',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReviewersRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoSigningKey' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/signing-key.gpg',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSigningKeyRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListStargazers' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/stargazers',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStargazersRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListStatuses' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/statuses/{sha}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStatusesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sort' => [
              'valid' => [
              ],
            ],
            'state' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCreateStatus' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/statuses/{sha}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateStatusRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'sha' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoListSubscribers' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/subscribers',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListSubscribersRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentCheckSubscription' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/subscription',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentPutSubscription' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/subscription',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteSubscription' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/subscription',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteSubscriptionRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListTags' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/tags',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTagsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoCreateTag' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/tags',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetTag' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/tags/{tag}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'tag' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteTag' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/tags/{tag}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTagRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'tag' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListTeams' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/teams',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTeamsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCheckTeam' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/teams/{team}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCheckTeamRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'team' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoAddTeam' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/teams/{team}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddTeamRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'team' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteTeam' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/teams/{team}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTeamRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'team' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoTrackedTimes' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/times',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTrackedTimesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
              ],
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserTrackedTimes' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/times/{user}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserTrackedTimesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'user' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoListTopics' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/topics',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTopicsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoUpdateTopics' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/topics',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdateTopicsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoAddTopic' => [
          'method' => 'PUT',
          'resource' => 'repos/{owner}/{repo}/topics/{topic}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddTopicRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'topic' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteTopic' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/topics/{topic}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTopicRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'topic' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoTransfer' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/transfer',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTransferRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'AcceptRepoTransfer' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/transfer/accept',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AcceptRepoTransferRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RejectRepoTransfer' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/transfer/reject',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RejectRepoTransferRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoCreateWikiPage' => [
          'method' => 'POST',
          'resource' => 'repos/{owner}/{repo}/wiki/new',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateWikiPageRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetWikiPage' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/wiki/page/{pageName}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPageRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'pageName' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoDeleteWikiPage' => [
          'method' => 'DELETE',
          'resource' => 'repos/{owner}/{repo}/wiki/page/{pageName}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteWikiPageRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'pageName' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'RepoEditWikiPage' => [
          'method' => 'PATCH',
          'resource' => 'repos/{owner}/{repo}/wiki/page/{pageName}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditWikiPageRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'pageName' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetWikiPages' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/wiki/pages',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPagesRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetWikiPageRevisions' => [
          'method' => 'GET',
          'resource' => 'repos/{owner}/{repo}/wiki/revisions/{pageName}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPageRevisionsRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'pageName' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'GenerateRepo' => [
          'method' => 'POST',
          'resource' => 'repos/{template_owner}/{template_repo}/generate',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GenerateRepoRestResponse',
          'params' => [
            'template_owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'template_repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'RepoGetByID' => [
          'method' => 'GET',
          'resource' => 'repositories/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetByIDRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetGeneralAPISettings' => [
          'method' => 'GET',
          'resource' => 'settings/api',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralAPISettingsRestResponse',
          'params' => [
          ],
        ],
        'GetGeneralAttachmentSettings' => [
          'method' => 'GET',
          'resource' => 'settings/attachment',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralAttachmentSettingsRestResponse',
          'params' => [
          ],
        ],
        'GetGeneralRepositorySettings' => [
          'method' => 'GET',
          'resource' => 'settings/repository',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralRepositorySettingsRestResponse',
          'params' => [
          ],
        ],
        'GetGeneralUISettings' => [
          'method' => 'GET',
          'resource' => 'settings/ui',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralUISettingsRestResponse',
          'params' => [
          ],
        ],
        'GetSigningKey' => [
          'method' => 'GET',
          'resource' => 'signing-key.gpg',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetSigningKeyRestResponse',
          'params' => [
          ],
        ],
        'OrgGetTeam' => [
          'method' => 'GET',
          'resource' => 'teams/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetTeamRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgDeleteTeam' => [
          'method' => 'DELETE',
          'resource' => 'teams/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteTeamRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgEditTeam' => [
          'method' => 'PATCH',
          'resource' => 'teams/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditTeamRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgListTeamMembers' => [
          'method' => 'GET',
          'resource' => 'teams/{id}/members',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamMembersRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgListTeamMember' => [
          'method' => 'GET',
          'resource' => 'teams/{id}/members/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamMemberRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgAddTeamMember' => [
          'method' => 'PUT',
          'resource' => 'teams/{id}/members/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgAddTeamMemberRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgRemoveTeamMember' => [
          'method' => 'DELETE',
          'resource' => 'teams/{id}/members/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgRemoveTeamMemberRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListTeamRepos' => [
          'method' => 'GET',
          'resource' => 'teams/{id}/repos',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamReposRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgListTeamRepo' => [
          'method' => 'GET',
          'resource' => 'teams/{id}/repos/{org}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamRepoRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgAddTeamRepository' => [
          'method' => 'PUT',
          'resource' => 'teams/{id}/repos/{org}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgAddTeamRepositoryRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgRemoveTeamRepository' => [
          'method' => 'DELETE',
          'resource' => 'teams/{id}/repos/{org}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgRemoveTeamRepositoryRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'TopicSearch' => [
          'method' => 'GET',
          'resource' => 'topics/search',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\TopicSearchRestResponse',
          'params' => [
            'q' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserGetCurrent' => [
          'method' => 'GET',
          'resource' => 'user',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetCurrentRestResponse',
          'params' => [
          ],
        ],
        'UserGetOauth2Application' => [
          'method' => 'GET',
          'resource' => 'user/applications/oauth2',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetOauth2ApplicationRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCreateOAuth2Application' => [
          'method' => 'POST',
          'resource' => 'user/applications/oauth2',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCreateOAuth2ApplicationRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserGetOAuth2Application' => [
          'method' => 'GET',
          'resource' => 'user/applications/oauth2/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetOAuth2ApplicationRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserDeleteOAuth2Application' => [
          'method' => 'DELETE',
          'resource' => 'user/applications/oauth2/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteOAuth2ApplicationRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserUpdateOAuth2Application' => [
          'method' => 'PATCH',
          'resource' => 'user/applications/oauth2/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserUpdateOAuth2ApplicationRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserListEmails' => [
          'method' => 'GET',
          'resource' => 'user/emails',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListEmailsRestResponse',
          'params' => [
          ],
        ],
        'UserAddEmail' => [
          'method' => 'POST',
          'resource' => 'user/emails',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserAddEmailRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserDeleteEmail' => [
          'method' => 'DELETE',
          'resource' => 'user/emails',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteEmailRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentListFollowers' => [
          'method' => 'GET',
          'resource' => 'user/followers',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListFollowersRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentListFollowing' => [
          'method' => 'GET',
          'resource' => 'user/following',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListFollowingRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentCheckFollowing' => [
          'method' => 'GET',
          'resource' => 'user/following/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckFollowingRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentPutFollow' => [
          'method' => 'PUT',
          'resource' => 'user/following/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutFollowRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteFollow' => [
          'method' => 'DELETE',
          'resource' => 'user/following/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteFollowRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetVerificationToken' => [
          'method' => 'GET',
          'resource' => 'user/gpg_key_token',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetVerificationTokenRestResponse',
          'params' => [
          ],
        ],
        'UserVerifyGPGKey' => [
          'method' => 'POST',
          'resource' => 'user/gpg_key_verify',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserVerifyGPGKeyRestResponse',
          'params' => [
          ],
        ],
        'UserCurrentListGPGKeys' => [
          'method' => 'GET',
          'resource' => 'user/gpg_keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListGPGKeysRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentPostGPGKey' => [
          'method' => 'POST',
          'resource' => 'user/gpg_keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPostGPGKeyRestResponse',
          'params' => [
            'Form' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentGetGPGKey' => [
          'method' => 'GET',
          'resource' => 'user/gpg_keys/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentGetGPGKeyRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteGPGKey' => [
          'method' => 'DELETE',
          'resource' => 'user/gpg_keys/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteGPGKeyRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentListKeys' => [
          'method' => 'GET',
          'resource' => 'user/keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListKeysRestResponse',
          'params' => [
            'fingerprint' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentPostKey' => [
          'method' => 'POST',
          'resource' => 'user/keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPostKeyRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentGetKey' => [
          'method' => 'GET',
          'resource' => 'user/keys/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentGetKeyRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteKey' => [
          'method' => 'DELETE',
          'resource' => 'user/keys/{id}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteKeyRestResponse',
          'params' => [
            'id' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'OrgListCurrentUserOrgs' => [
          'method' => 'GET',
          'resource' => 'user/orgs',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListCurrentUserOrgsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentListRepos' => [
          'method' => 'GET',
          'resource' => 'user/repos',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListReposRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'CreateCurrentUserRepo' => [
          'method' => 'POST',
          'resource' => 'user/repos',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateCurrentUserRepoRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'GetUserSettings' => [
          'method' => 'GET',
          'resource' => 'user/settings',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetUserSettingsRestResponse',
          'params' => [
          ],
        ],
        'UpdateUserSettings' => [
          'method' => 'PATCH',
          'resource' => 'user/settings',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UpdateUserSettingsRestResponse',
          'params' => [
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentListStarred' => [
          'method' => 'GET',
          'resource' => 'user/starred',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListStarredRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentCheckStarring' => [
          'method' => 'GET',
          'resource' => 'user/starred/{owner}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckStarringRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentPutStar' => [
          'method' => 'PUT',
          'resource' => 'user/starred/{owner}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutStarRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserCurrentDeleteStar' => [
          'method' => 'DELETE',
          'resource' => 'user/starred/{owner}/{repo}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteStarRestResponse',
          'params' => [
            'owner' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'repo' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserGetStopWatches' => [
          'method' => 'GET',
          'resource' => 'user/stopwatches',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetStopWatchesRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentListSubscriptions' => [
          'method' => 'GET',
          'resource' => 'user/subscriptions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListSubscriptionsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserListTeams' => [
          'method' => 'GET',
          'resource' => 'user/teams',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListTeamsRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCurrentTrackedTimes' => [
          'method' => 'GET',
          'resource' => 'user/times',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentTrackedTimesRestResponse',
          'params' => [
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
            'since' => [
              'valid' => [
              ],
            ],
            'before' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserSearch' => [
          'method' => 'GET',
          'resource' => 'users/search',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserSearchRestResponse',
          'params' => [
            'q' => [
              'valid' => [
              ],
            ],
            'uid' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserGet' => [
          'method' => 'GET',
          'resource' => 'users/{username}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserListFollowers' => [
          'method' => 'GET',
          'resource' => 'users/{username}/followers',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListFollowersRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserListFollowing' => [
          'method' => 'GET',
          'resource' => 'users/{username}/following',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListFollowingRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCheckFollowing' => [
          'method' => 'GET',
          'resource' => 'users/{username}/following/{target}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCheckFollowingRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'target' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserListGPGKeys' => [
          'method' => 'GET',
          'resource' => 'users/{username}/gpg_keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListGPGKeysRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserGetHeatmapData' => [
          'method' => 'GET',
          'resource' => 'users/{username}/heatmap',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetHeatmapDataRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserListKeys' => [
          'method' => 'GET',
          'resource' => 'users/{username}/keys',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListKeysRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'fingerprint' => [
              'valid' => [
              ],
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgListUserOrgs' => [
          'method' => 'GET',
          'resource' => 'users/{username}/orgs',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListUserOrgsRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'OrgGetUserPermissions' => [
          'method' => 'GET',
          'resource' => 'users/{username}/orgs/{org}/permissions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetUserPermissionsRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'org' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'UserListRepos' => [
          'method' => 'GET',
          'resource' => 'users/{username}/repos',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListReposRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserListStarred' => [
          'method' => 'GET',
          'resource' => 'users/{username}/starred',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListStarredRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserListSubscriptions' => [
          'method' => 'GET',
          'resource' => 'users/{username}/subscriptions',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListSubscriptionsRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserGetTokens' => [
          'method' => 'GET',
          'resource' => 'users/{username}/tokens',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetTokensRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'page' => [
              'valid' => [
              ],
            ],
            'limit' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserCreateToken' => [
          'method' => 'POST',
          'resource' => 'users/{username}/tokens',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCreateTokenRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'body' => [
              'valid' => [
              ],
            ],
          ],
        ],
        'UserDeleteAccessToken' => [
          'method' => 'DELETE',
          'resource' => 'users/{username}/tokens/{token}',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteAccessTokenRestResponse',
          'params' => [
            'username' => [
              'valid' => [
              ],
              'required' => true,
            ],
            'token' => [
              'valid' => [
              ],
              'required' => true,
            ],
          ],
        ],
        'GetVersion' => [
          'method' => 'GET',
          'resource' => 'version',
          'responseClass' => '\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetVersionRestResponse',
          'params' => [
          ],
        ],
      ];

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public function activitypubPerson(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ActivitypubPersonRestRequest $request)
    {
        return $this->activitypubPersonAsync($request)->wait();
    }

    public function activitypubPersonAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ActivitypubPersonRestRequest $request)
    {
        return $this->callOperationAsync('ActivitypubPerson', $request);
    }

    public function activitypubPersonInbox(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ActivitypubPersonInboxRestRequest $request)
    {
        return $this->activitypubPersonInboxAsync($request)->wait();
    }

    public function activitypubPersonInboxAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ActivitypubPersonInboxRestRequest $request)
    {
        return $this->callOperationAsync('ActivitypubPersonInbox', $request);
    }

    public function adminCronList(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCronListRestRequest $request)
    {
        return $this->adminCronListAsync($request)->wait();
    }

    public function adminCronListAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCronListRestRequest $request)
    {
        return $this->callOperationAsync('AdminCronList', $request);
    }

    public function adminCronRun(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCronRunRestRequest $request)
    {
        return $this->adminCronRunAsync($request)->wait();
    }

    public function adminCronRunAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCronRunRestRequest $request)
    {
        return $this->callOperationAsync('AdminCronRun', $request);
    }

    public function adminListHooks(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminListHooksRestRequest $request)
    {
        return $this->adminListHooksAsync($request)->wait();
    }

    public function adminListHooksAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminListHooksRestRequest $request)
    {
        return $this->callOperationAsync('AdminListHooks', $request);
    }

    public function adminCreateHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateHookRestRequest $request)
    {
        return $this->adminCreateHookAsync($request)->wait();
    }

    public function adminCreateHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateHookRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreateHook', $request);
    }

    public function adminGetHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetHookRestRequest $request)
    {
        return $this->adminGetHookAsync($request)->wait();
    }

    public function adminGetHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetHookRestRequest $request)
    {
        return $this->callOperationAsync('AdminGetHook', $request);
    }

    public function adminEditHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminEditHookRestRequest $request)
    {
        return $this->adminEditHookAsync($request)->wait();
    }

    public function adminEditHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminEditHookRestRequest $request)
    {
        return $this->callOperationAsync('AdminEditHook', $request);
    }

    public function adminGetAllOrgs(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetAllOrgsRestRequest $request)
    {
        return $this->adminGetAllOrgsAsync($request)->wait();
    }

    public function adminGetAllOrgsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetAllOrgsRestRequest $request)
    {
        return $this->callOperationAsync('AdminGetAllOrgs', $request);
    }

    public function adminUnadoptedList(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminUnadoptedListRestRequest $request)
    {
        return $this->adminUnadoptedListAsync($request)->wait();
    }

    public function adminUnadoptedListAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminUnadoptedListRestRequest $request)
    {
        return $this->callOperationAsync('AdminUnadoptedList', $request);
    }

    public function adminAdoptRepository(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminAdoptRepositoryRestRequest $request)
    {
        return $this->adminAdoptRepositoryAsync($request)->wait();
    }

    public function adminAdoptRepositoryAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminAdoptRepositoryRestRequest $request)
    {
        return $this->callOperationAsync('AdminAdoptRepository', $request);
    }

    public function adminDeleteUnadoptedRepository(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUnadoptedRepositoryRestRequest $request)
    {
        return $this->adminDeleteUnadoptedRepositoryAsync($request)->wait();
    }

    public function adminDeleteUnadoptedRepositoryAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUnadoptedRepositoryRestRequest $request)
    {
        return $this->callOperationAsync('AdminDeleteUnadoptedRepository', $request);
    }

    public function adminGetAllUsers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetAllUsersRestRequest $request)
    {
        return $this->adminGetAllUsersAsync($request)->wait();
    }

    public function adminGetAllUsersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminGetAllUsersRestRequest $request)
    {
        return $this->callOperationAsync('AdminGetAllUsers', $request);
    }

    public function adminCreateUser(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateUserRestRequest $request)
    {
        return $this->adminCreateUserAsync($request)->wait();
    }

    public function adminCreateUserAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateUserRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreateUser', $request);
    }

    public function adminDeleteUser(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUserRestRequest $request)
    {
        return $this->adminDeleteUserAsync($request)->wait();
    }

    public function adminDeleteUserAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUserRestRequest $request)
    {
        return $this->callOperationAsync('AdminDeleteUser', $request);
    }

    public function adminEditUser(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminEditUserRestRequest $request)
    {
        return $this->adminEditUserAsync($request)->wait();
    }

    public function adminEditUserAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminEditUserRestRequest $request)
    {
        return $this->callOperationAsync('AdminEditUser', $request);
    }

    public function adminCreatePublicKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreatePublicKeyRestRequest $request)
    {
        return $this->adminCreatePublicKeyAsync($request)->wait();
    }

    public function adminCreatePublicKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreatePublicKeyRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreatePublicKey', $request);
    }

    public function adminDeleteUserPublicKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUserPublicKeyRestRequest $request)
    {
        return $this->adminDeleteUserPublicKeyAsync($request)->wait();
    }

    public function adminDeleteUserPublicKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteUserPublicKeyRestRequest $request)
    {
        return $this->callOperationAsync('AdminDeleteUserPublicKey', $request);
    }

    public function adminCreateOrg(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateOrgRestRequest $request)
    {
        return $this->adminCreateOrgAsync($request)->wait();
    }

    public function adminCreateOrgAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateOrgRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreateOrg', $request);
    }

    public function adminCreateRepo(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateRepoRestRequest $request)
    {
        return $this->adminCreateRepoAsync($request)->wait();
    }

    public function adminCreateRepoAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminCreateRepoRestRequest $request)
    {
        return $this->callOperationAsync('AdminCreateRepo', $request);
    }

    public function adminDeleteHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteHookRestRequest $request)
    {
        return $this->adminDeleteHookAsync($request)->wait();
    }

    public function adminDeleteHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AdminDeleteHookRestRequest $request)
    {
        return $this->callOperationAsync('AdminDeleteHook', $request);
    }

    public function renderMarkdown(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RenderMarkdownRestRequest $request)
    {
        return $this->renderMarkdownAsync($request)->wait();
    }

    public function renderMarkdownAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RenderMarkdownRestRequest $request)
    {
        return $this->callOperationAsync('RenderMarkdown', $request);
    }

    public function renderMarkdownRaw(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RenderMarkdownRawRestRequest $request)
    {
        return $this->renderMarkdownRawAsync($request)->wait();
    }

    public function renderMarkdownRawAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RenderMarkdownRawRestRequest $request)
    {
        return $this->callOperationAsync('RenderMarkdownRaw', $request);
    }

    public function getNodeInfo(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetNodeInfoRestRequest $request)
    {
        return $this->getNodeInfoAsync($request)->wait();
    }

    public function getNodeInfoAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetNodeInfoRestRequest $request)
    {
        return $this->callOperationAsync('GetNodeInfo', $request);
    }

    public function notifyGetList(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetListRestRequest $request)
    {
        return $this->notifyGetListAsync($request)->wait();
    }

    public function notifyGetListAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetListRestRequest $request)
    {
        return $this->callOperationAsync('NotifyGetList', $request);
    }

    public function notifyReadList(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadListRestRequest $request)
    {
        return $this->notifyReadListAsync($request)->wait();
    }

    public function notifyReadListAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadListRestRequest $request)
    {
        return $this->callOperationAsync('NotifyReadList', $request);
    }

    public function notifyNewAvailable(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyNewAvailableRestRequest $request)
    {
        return $this->notifyNewAvailableAsync($request)->wait();
    }

    public function notifyNewAvailableAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyNewAvailableRestRequest $request)
    {
        return $this->callOperationAsync('NotifyNewAvailable', $request);
    }

    public function notifyGetThread(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetThreadRestRequest $request)
    {
        return $this->notifyGetThreadAsync($request)->wait();
    }

    public function notifyGetThreadAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetThreadRestRequest $request)
    {
        return $this->callOperationAsync('NotifyGetThread', $request);
    }

    public function notifyReadThread(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadThreadRestRequest $request)
    {
        return $this->notifyReadThreadAsync($request)->wait();
    }

    public function notifyReadThreadAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadThreadRestRequest $request)
    {
        return $this->callOperationAsync('NotifyReadThread', $request);
    }

    public function createOrgRepoDeprecated(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateOrgRepoDeprecatedRestRequest $request)
    {
        return $this->createOrgRepoDeprecatedAsync($request)->wait();
    }

    public function createOrgRepoDeprecatedAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateOrgRepoDeprecatedRestRequest $request)
    {
        return $this->callOperationAsync('CreateOrgRepoDeprecated', $request);
    }

    public function orgGetAll(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetAllRestRequest $request)
    {
        return $this->orgGetAllAsync($request)->wait();
    }

    public function orgGetAllAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetAllRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetAll', $request);
    }

    public function orgCreate(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateRestRequest $request)
    {
        return $this->orgCreateAsync($request)->wait();
    }

    public function orgCreateAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateRestRequest $request)
    {
        return $this->callOperationAsync('OrgCreate', $request);
    }

    public function orgGet(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetRestRequest $request)
    {
        return $this->orgGetAsync($request)->wait();
    }

    public function orgGetAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetRestRequest $request)
    {
        return $this->callOperationAsync('OrgGet', $request);
    }

    public function orgDelete(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteRestRequest $request)
    {
        return $this->orgDeleteAsync($request)->wait();
    }

    public function orgDeleteAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteRestRequest $request)
    {
        return $this->callOperationAsync('OrgDelete', $request);
    }

    public function orgEdit(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditRestRequest $request)
    {
        return $this->orgEditAsync($request)->wait();
    }

    public function orgEditAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditRestRequest $request)
    {
        return $this->callOperationAsync('OrgEdit', $request);
    }

    public function orgListHooks(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListHooksRestRequest $request)
    {
        return $this->orgListHooksAsync($request)->wait();
    }

    public function orgListHooksAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListHooksRestRequest $request)
    {
        return $this->callOperationAsync('OrgListHooks', $request);
    }

    public function orgCreateHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateHookRestRequest $request)
    {
        return $this->orgCreateHookAsync($request)->wait();
    }

    public function orgCreateHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateHookRestRequest $request)
    {
        return $this->callOperationAsync('OrgCreateHook', $request);
    }

    public function orgGetHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetHookRestRequest $request)
    {
        return $this->orgGetHookAsync($request)->wait();
    }

    public function orgGetHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetHookRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetHook', $request);
    }

    public function orgDeleteHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteHookRestRequest $request)
    {
        return $this->orgDeleteHookAsync($request)->wait();
    }

    public function orgDeleteHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteHookRestRequest $request)
    {
        return $this->callOperationAsync('OrgDeleteHook', $request);
    }

    public function orgEditHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditHookRestRequest $request)
    {
        return $this->orgEditHookAsync($request)->wait();
    }

    public function orgEditHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditHookRestRequest $request)
    {
        return $this->callOperationAsync('OrgEditHook', $request);
    }

    public function orgListLabels(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListLabelsRestRequest $request)
    {
        return $this->orgListLabelsAsync($request)->wait();
    }

    public function orgListLabelsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListLabelsRestRequest $request)
    {
        return $this->callOperationAsync('OrgListLabels', $request);
    }

    public function orgCreateLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateLabelRestRequest $request)
    {
        return $this->orgCreateLabelAsync($request)->wait();
    }

    public function orgCreateLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateLabelRestRequest $request)
    {
        return $this->callOperationAsync('OrgCreateLabel', $request);
    }

    public function orgGetLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetLabelRestRequest $request)
    {
        return $this->orgGetLabelAsync($request)->wait();
    }

    public function orgGetLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetLabelRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetLabel', $request);
    }

    public function orgDeleteLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteLabelRestRequest $request)
    {
        return $this->orgDeleteLabelAsync($request)->wait();
    }

    public function orgDeleteLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteLabelRestRequest $request)
    {
        return $this->callOperationAsync('OrgDeleteLabel', $request);
    }

    public function orgEditLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditLabelRestRequest $request)
    {
        return $this->orgEditLabelAsync($request)->wait();
    }

    public function orgEditLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditLabelRestRequest $request)
    {
        return $this->callOperationAsync('OrgEditLabel', $request);
    }

    public function orgListMembers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListMembersRestRequest $request)
    {
        return $this->orgListMembersAsync($request)->wait();
    }

    public function orgListMembersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListMembersRestRequest $request)
    {
        return $this->callOperationAsync('OrgListMembers', $request);
    }

    public function orgIsMember(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgIsMemberRestRequest $request)
    {
        return $this->orgIsMemberAsync($request)->wait();
    }

    public function orgIsMemberAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgIsMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgIsMember', $request);
    }

    public function orgDeleteMember(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteMemberRestRequest $request)
    {
        return $this->orgDeleteMemberAsync($request)->wait();
    }

    public function orgDeleteMemberAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgDeleteMember', $request);
    }

    public function orgListPublicMembers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListPublicMembersRestRequest $request)
    {
        return $this->orgListPublicMembersAsync($request)->wait();
    }

    public function orgListPublicMembersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListPublicMembersRestRequest $request)
    {
        return $this->callOperationAsync('OrgListPublicMembers', $request);
    }

    public function orgIsPublicMember(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgIsPublicMemberRestRequest $request)
    {
        return $this->orgIsPublicMemberAsync($request)->wait();
    }

    public function orgIsPublicMemberAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgIsPublicMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgIsPublicMember', $request);
    }

    public function orgPublicizeMember(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgPublicizeMemberRestRequest $request)
    {
        return $this->orgPublicizeMemberAsync($request)->wait();
    }

    public function orgPublicizeMemberAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgPublicizeMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgPublicizeMember', $request);
    }

    public function orgConcealMember(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgConcealMemberRestRequest $request)
    {
        return $this->orgConcealMemberAsync($request)->wait();
    }

    public function orgConcealMemberAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgConcealMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgConcealMember', $request);
    }

    public function orgListRepos(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListReposRestRequest $request)
    {
        return $this->orgListReposAsync($request)->wait();
    }

    public function orgListReposAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListReposRestRequest $request)
    {
        return $this->callOperationAsync('OrgListRepos', $request);
    }

    public function createOrgRepo(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateOrgRepoRestRequest $request)
    {
        return $this->createOrgRepoAsync($request)->wait();
    }

    public function createOrgRepoAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateOrgRepoRestRequest $request)
    {
        return $this->callOperationAsync('CreateOrgRepo', $request);
    }

    public function orgListTeams(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamsRestRequest $request)
    {
        return $this->orgListTeamsAsync($request)->wait();
    }

    public function orgListTeamsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamsRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeams', $request);
    }

    public function orgCreateTeam(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateTeamRestRequest $request)
    {
        return $this->orgCreateTeamAsync($request)->wait();
    }

    public function orgCreateTeamAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgCreateTeamRestRequest $request)
    {
        return $this->callOperationAsync('OrgCreateTeam', $request);
    }

    public function teamSearch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\TeamSearchRestRequest $request)
    {
        return $this->teamSearchAsync($request)->wait();
    }

    public function teamSearchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\TeamSearchRestRequest $request)
    {
        return $this->callOperationAsync('TeamSearch', $request);
    }

    public function listPackages(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListPackagesRestRequest $request)
    {
        return $this->listPackagesAsync($request)->wait();
    }

    public function listPackagesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListPackagesRestRequest $request)
    {
        return $this->callOperationAsync('ListPackages', $request);
    }

    public function getPackage(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetPackageRestRequest $request)
    {
        return $this->getPackageAsync($request)->wait();
    }

    public function getPackageAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetPackageRestRequest $request)
    {
        return $this->callOperationAsync('GetPackage', $request);
    }

    public function deletePackage(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\DeletePackageRestRequest $request)
    {
        return $this->deletePackageAsync($request)->wait();
    }

    public function deletePackageAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\DeletePackageRestRequest $request)
    {
        return $this->callOperationAsync('DeletePackage', $request);
    }

    public function listPackageFiles(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListPackageFilesRestRequest $request)
    {
        return $this->listPackageFilesAsync($request)->wait();
    }

    public function listPackageFilesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListPackageFilesRestRequest $request)
    {
        return $this->callOperationAsync('ListPackageFiles', $request);
    }

    public function issueSearchIssues(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueSearchIssuesRestRequest $request)
    {
        return $this->issueSearchIssuesAsync($request)->wait();
    }

    public function issueSearchIssuesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueSearchIssuesRestRequest $request)
    {
        return $this->callOperationAsync('IssueSearchIssues', $request);
    }

    public function repoMigrate(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMigrateRestRequest $request)
    {
        return $this->repoMigrateAsync($request)->wait();
    }

    public function repoMigrateAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMigrateRestRequest $request)
    {
        return $this->callOperationAsync('RepoMigrate', $request);
    }

    public function repoSearch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSearchRestRequest $request)
    {
        return $this->repoSearchAsync($request)->wait();
    }

    public function repoSearchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSearchRestRequest $request)
    {
        return $this->callOperationAsync('RepoSearch', $request);
    }

    public function repoGet(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRestRequest $request)
    {
        return $this->repoGetAsync($request)->wait();
    }

    public function repoGetAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRestRequest $request)
    {
        return $this->callOperationAsync('RepoGet', $request);
    }

    public function repoDelete(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteRestRequest $request)
    {
        return $this->repoDeleteAsync($request)->wait();
    }

    public function repoDeleteAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteRestRequest $request)
    {
        return $this->callOperationAsync('RepoDelete', $request);
    }

    public function repoEdit(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditRestRequest $request)
    {
        return $this->repoEditAsync($request)->wait();
    }

    public function repoEditAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditRestRequest $request)
    {
        return $this->callOperationAsync('RepoEdit', $request);
    }

    public function repoGetArchive(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetArchiveRestRequest $request)
    {
        return $this->repoGetArchiveAsync($request)->wait();
    }

    public function repoGetArchiveAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetArchiveRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetArchive', $request);
    }

    public function repoGetAssignees(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetAssigneesRestRequest $request)
    {
        return $this->repoGetAssigneesAsync($request)->wait();
    }

    public function repoGetAssigneesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetAssigneesRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetAssignees', $request);
    }

    public function repoListBranchProtection(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListBranchProtectionRestRequest $request)
    {
        return $this->repoListBranchProtectionAsync($request)->wait();
    }

    public function repoListBranchProtectionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoListBranchProtection', $request);
    }

    public function repoCreateBranchProtection(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateBranchProtectionRestRequest $request)
    {
        return $this->repoCreateBranchProtectionAsync($request)->wait();
    }

    public function repoCreateBranchProtectionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateBranchProtection', $request);
    }

    public function repoGetBranchProtection(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetBranchProtectionRestRequest $request)
    {
        return $this->repoGetBranchProtectionAsync($request)->wait();
    }

    public function repoGetBranchProtectionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetBranchProtection', $request);
    }

    public function repoDeleteBranchProtection(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteBranchProtectionRestRequest $request)
    {
        return $this->repoDeleteBranchProtectionAsync($request)->wait();
    }

    public function repoDeleteBranchProtectionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteBranchProtection', $request);
    }

    public function repoEditBranchProtection(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditBranchProtectionRestRequest $request)
    {
        return $this->repoEditBranchProtectionAsync($request)->wait();
    }

    public function repoEditBranchProtectionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditBranchProtectionRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditBranchProtection', $request);
    }

    public function repoListBranches(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListBranchesRestRequest $request)
    {
        return $this->repoListBranchesAsync($request)->wait();
    }

    public function repoListBranchesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListBranchesRestRequest $request)
    {
        return $this->callOperationAsync('RepoListBranches', $request);
    }

    public function repoCreateBranch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateBranchRestRequest $request)
    {
        return $this->repoCreateBranchAsync($request)->wait();
    }

    public function repoCreateBranchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateBranchRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateBranch', $request);
    }

    public function repoGetBranch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetBranchRestRequest $request)
    {
        return $this->repoGetBranchAsync($request)->wait();
    }

    public function repoGetBranchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetBranchRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetBranch', $request);
    }

    public function repoDeleteBranch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteBranchRestRequest $request)
    {
        return $this->repoDeleteBranchAsync($request)->wait();
    }

    public function repoDeleteBranchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteBranchRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteBranch', $request);
    }

    public function repoListCollaborators(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListCollaboratorsRestRequest $request)
    {
        return $this->repoListCollaboratorsAsync($request)->wait();
    }

    public function repoListCollaboratorsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListCollaboratorsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListCollaborators', $request);
    }

    public function repoCheckCollaborator(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCheckCollaboratorRestRequest $request)
    {
        return $this->repoCheckCollaboratorAsync($request)->wait();
    }

    public function repoCheckCollaboratorAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCheckCollaboratorRestRequest $request)
    {
        return $this->callOperationAsync('RepoCheckCollaborator', $request);
    }

    public function repoAddCollaborator(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddCollaboratorRestRequest $request)
    {
        return $this->repoAddCollaboratorAsync($request)->wait();
    }

    public function repoAddCollaboratorAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddCollaboratorRestRequest $request)
    {
        return $this->callOperationAsync('RepoAddCollaborator', $request);
    }

    public function repoDeleteCollaborator(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteCollaboratorRestRequest $request)
    {
        return $this->repoDeleteCollaboratorAsync($request)->wait();
    }

    public function repoDeleteCollaboratorAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteCollaboratorRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteCollaborator', $request);
    }

    public function repoGetRepoPermissions(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRepoPermissionsRestRequest $request)
    {
        return $this->repoGetRepoPermissionsAsync($request)->wait();
    }

    public function repoGetRepoPermissionsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRepoPermissionsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetRepoPermissions', $request);
    }

    public function repoGetAllCommits(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetAllCommitsRestRequest $request)
    {
        return $this->repoGetAllCommitsAsync($request)->wait();
    }

    public function repoGetAllCommitsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetAllCommitsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetAllCommits', $request);
    }

    public function repoGetCombinedStatusByRef(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetCombinedStatusByRefRestRequest $request)
    {
        return $this->repoGetCombinedStatusByRefAsync($request)->wait();
    }

    public function repoGetCombinedStatusByRefAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetCombinedStatusByRefRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetCombinedStatusByRef', $request);
    }

    public function repoListStatusesByRef(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStatusesByRefRestRequest $request)
    {
        return $this->repoListStatusesByRefAsync($request)->wait();
    }

    public function repoListStatusesByRefAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStatusesByRefRestRequest $request)
    {
        return $this->callOperationAsync('RepoListStatusesByRef', $request);
    }

    public function repoGetContentsList(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetContentsListRestRequest $request)
    {
        return $this->repoGetContentsListAsync($request)->wait();
    }

    public function repoGetContentsListAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetContentsListRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetContentsList', $request);
    }

    public function repoGetContents(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetContentsRestRequest $request)
    {
        return $this->repoGetContentsAsync($request)->wait();
    }

    public function repoGetContentsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetContentsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetContents', $request);
    }

    public function repoUpdateFile(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdateFileRestRequest $request)
    {
        return $this->repoUpdateFileAsync($request)->wait();
    }

    public function repoUpdateFileAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdateFileRestRequest $request)
    {
        return $this->callOperationAsync('RepoUpdateFile', $request);
    }

    public function repoCreateFile(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateFileRestRequest $request)
    {
        return $this->repoCreateFileAsync($request)->wait();
    }

    public function repoCreateFileAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateFileRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateFile', $request);
    }

    public function repoDeleteFile(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteFileRestRequest $request)
    {
        return $this->repoDeleteFileAsync($request)->wait();
    }

    public function repoDeleteFileAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteFileRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteFile', $request);
    }

    public function repoApplyDiffPatch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoApplyDiffPatchRestRequest $request)
    {
        return $this->repoApplyDiffPatchAsync($request)->wait();
    }

    public function repoApplyDiffPatchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoApplyDiffPatchRestRequest $request)
    {
        return $this->callOperationAsync('RepoApplyDiffPatch', $request);
    }

    public function repoGetEditorConfig(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetEditorConfigRestRequest $request)
    {
        return $this->repoGetEditorConfigAsync($request)->wait();
    }

    public function repoGetEditorConfigAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetEditorConfigRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetEditorConfig', $request);
    }

    public function listForks(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListForksRestRequest $request)
    {
        return $this->listForksAsync($request)->wait();
    }

    public function listForksAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\ListForksRestRequest $request)
    {
        return $this->callOperationAsync('ListForks', $request);
    }

    public function createFork(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateForkRestRequest $request)
    {
        return $this->createForkAsync($request)->wait();
    }

    public function createForkAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateForkRestRequest $request)
    {
        return $this->callOperationAsync('CreateFork', $request);
    }

    public function GetBlob(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetBlobRestRequest $request)
    {
        return $this->GetBlobAsync($request)->wait();
    }

    public function GetBlobAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetBlobRestRequest $request)
    {
        return $this->callOperationAsync('GetBlob', $request);
    }

    public function repoGetSingleCommit(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetSingleCommitRestRequest $request)
    {
        return $this->repoGetSingleCommitAsync($request)->wait();
    }

    public function repoGetSingleCommitAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetSingleCommitRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetSingleCommit', $request);
    }

    public function repoDownloadCommitDiffOrPatch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDownloadCommitDiffOrPatchRestRequest $request)
    {
        return $this->repoDownloadCommitDiffOrPatchAsync($request)->wait();
    }

    public function repoDownloadCommitDiffOrPatchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDownloadCommitDiffOrPatchRestRequest $request)
    {
        return $this->callOperationAsync('RepoDownloadCommitDiffOrPatch', $request);
    }

    public function repoGetNote(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetNoteRestRequest $request)
    {
        return $this->repoGetNoteAsync($request)->wait();
    }

    public function repoGetNoteAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetNoteRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetNote', $request);
    }

    public function repoListAllGitRefs(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListAllGitRefsRestRequest $request)
    {
        return $this->repoListAllGitRefsAsync($request)->wait();
    }

    public function repoListAllGitRefsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListAllGitRefsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListAllGitRefs', $request);
    }

    public function repoListGitRefs(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListGitRefsRestRequest $request)
    {
        return $this->repoListGitRefsAsync($request)->wait();
    }

    public function repoListGitRefsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListGitRefsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListGitRefs', $request);
    }

    public function GetAnnotatedTag(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetAnnotatedTagRestRequest $request)
    {
        return $this->GetAnnotatedTagAsync($request)->wait();
    }

    public function GetAnnotatedTagAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetAnnotatedTagRestRequest $request)
    {
        return $this->callOperationAsync('GetAnnotatedTag', $request);
    }

    public function GetTree(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetTreeRestRequest $request)
    {
        return $this->GetTreeAsync($request)->wait();
    }

    public function GetTreeAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetTreeRestRequest $request)
    {
        return $this->callOperationAsync('GetTree', $request);
    }

    public function repoListHooks(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListHooksRestRequest $request)
    {
        return $this->repoListHooksAsync($request)->wait();
    }

    public function repoListHooksAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListHooksRestRequest $request)
    {
        return $this->callOperationAsync('RepoListHooks', $request);
    }

    public function repoCreateHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateHookRestRequest $request)
    {
        return $this->repoCreateHookAsync($request)->wait();
    }

    public function repoCreateHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateHook', $request);
    }

    public function repoListGitHooks(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListGitHooksRestRequest $request)
    {
        return $this->repoListGitHooksAsync($request)->wait();
    }

    public function repoListGitHooksAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListGitHooksRestRequest $request)
    {
        return $this->callOperationAsync('RepoListGitHooks', $request);
    }

    public function repoGetGitHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetGitHookRestRequest $request)
    {
        return $this->repoGetGitHookAsync($request)->wait();
    }

    public function repoGetGitHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetGitHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetGitHook', $request);
    }

    public function repoDeleteGitHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteGitHookRestRequest $request)
    {
        return $this->repoDeleteGitHookAsync($request)->wait();
    }

    public function repoDeleteGitHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteGitHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteGitHook', $request);
    }

    public function repoEditGitHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditGitHookRestRequest $request)
    {
        return $this->repoEditGitHookAsync($request)->wait();
    }

    public function repoEditGitHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditGitHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditGitHook', $request);
    }

    public function repoGetHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetHookRestRequest $request)
    {
        return $this->repoGetHookAsync($request)->wait();
    }

    public function repoGetHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetHook', $request);
    }

    public function repoDeleteHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteHookRestRequest $request)
    {
        return $this->repoDeleteHookAsync($request)->wait();
    }

    public function repoDeleteHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteHook', $request);
    }

    public function repoEditHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditHookRestRequest $request)
    {
        return $this->repoEditHookAsync($request)->wait();
    }

    public function repoEditHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditHook', $request);
    }

    public function repoTestHook(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTestHookRestRequest $request)
    {
        return $this->repoTestHookAsync($request)->wait();
    }

    public function repoTestHookAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTestHookRestRequest $request)
    {
        return $this->callOperationAsync('RepoTestHook', $request);
    }

    public function repoGetIssueTemplates(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetIssueTemplatesRestRequest $request)
    {
        return $this->repoGetIssueTemplatesAsync($request)->wait();
    }

    public function repoGetIssueTemplatesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetIssueTemplatesRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetIssueTemplates', $request);
    }

    public function issueListIssues(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssuesRestRequest $request)
    {
        return $this->issueListIssuesAsync($request)->wait();
    }

    public function issueListIssuesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssuesRestRequest $request)
    {
        return $this->callOperationAsync('IssueListIssues', $request);
    }

    public function issueCreateIssue(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueRestRequest $request)
    {
        return $this->issueCreateIssueAsync($request)->wait();
    }

    public function issueCreateIssueAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateIssue', $request);
    }

    public function issueGetRepoComments(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetRepoCommentsRestRequest $request)
    {
        return $this->issueGetRepoCommentsAsync($request)->wait();
    }

    public function issueGetRepoCommentsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetRepoCommentsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetRepoComments', $request);
    }

    public function issueGetComment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentRestRequest $request)
    {
        return $this->issueGetCommentAsync($request)->wait();
    }

    public function issueGetCommentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetComment', $request);
    }

    public function issueDeleteComment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentRestRequest $request)
    {
        return $this->issueDeleteCommentAsync($request)->wait();
    }

    public function issueDeleteCommentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteComment', $request);
    }

    public function issueEditComment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditCommentRestRequest $request)
    {
        return $this->issueEditCommentAsync($request)->wait();
    }

    public function issueEditCommentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditCommentRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditComment', $request);
    }

    public function issueListIssueCommentAttachments(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssueCommentAttachmentsRestRequest $request)
    {
        return $this->issueListIssueCommentAttachmentsAsync($request)->wait();
    }

    public function issueListIssueCommentAttachmentsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssueCommentAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('IssueListIssueCommentAttachments', $request);
    }

    public function issueCreateIssueCommentAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueCommentAttachmentRestRequest $request)
    {
        return $this->issueCreateIssueCommentAttachmentAsync($request)->wait();
    }

    public function issueCreateIssueCommentAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueCommentAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateIssueCommentAttachment', $request);
    }

    public function issueGetIssueCommentAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueCommentAttachmentRestRequest $request)
    {
        return $this->issueGetIssueCommentAttachmentAsync($request)->wait();
    }

    public function issueGetIssueCommentAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueCommentAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetIssueCommentAttachment', $request);
    }

    public function issueDeleteIssueCommentAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueCommentAttachmentRestRequest $request)
    {
        return $this->issueDeleteIssueCommentAttachmentAsync($request)->wait();
    }

    public function issueDeleteIssueCommentAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueCommentAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteIssueCommentAttachment', $request);
    }

    public function issueEditIssueCommentAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueCommentAttachmentRestRequest $request)
    {
        return $this->issueEditIssueCommentAttachmentAsync($request)->wait();
    }

    public function issueEditIssueCommentAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueCommentAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditIssueCommentAttachment', $request);
    }

    public function issueGetCommentReactions(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentReactionsRestRequest $request)
    {
        return $this->issueGetCommentReactionsAsync($request)->wait();
    }

    public function issueGetCommentReactionsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentReactionsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetCommentReactions', $request);
    }

    public function issuePostCommentReaction(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssuePostCommentReactionRestRequest $request)
    {
        return $this->issuePostCommentReactionAsync($request)->wait();
    }

    public function issuePostCommentReactionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssuePostCommentReactionRestRequest $request)
    {
        return $this->callOperationAsync('IssuePostCommentReaction', $request);
    }

    public function issueDeleteCommentReaction(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentReactionRestRequest $request)
    {
        return $this->issueDeleteCommentReactionAsync($request)->wait();
    }

    public function issueDeleteCommentReactionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentReactionRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteCommentReaction', $request);
    }

    public function issueGetIssue(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueRestRequest $request)
    {
        return $this->issueGetIssueAsync($request)->wait();
    }

    public function issueGetIssueAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetIssue', $request);
    }

    public function issueDelete(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteRestRequest $request)
    {
        return $this->issueDeleteAsync($request)->wait();
    }

    public function issueDeleteAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteRestRequest $request)
    {
        return $this->callOperationAsync('IssueDelete', $request);
    }

    public function issueEditIssue(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueRestRequest $request)
    {
        return $this->issueEditIssueAsync($request)->wait();
    }

    public function issueEditIssueAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditIssue', $request);
    }

    public function issueListIssueAttachments(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssueAttachmentsRestRequest $request)
    {
        return $this->issueListIssueAttachmentsAsync($request)->wait();
    }

    public function issueListIssueAttachmentsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListIssueAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('IssueListIssueAttachments', $request);
    }

    public function issueCreateIssueAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueAttachmentRestRequest $request)
    {
        return $this->issueCreateIssueAttachmentAsync($request)->wait();
    }

    public function issueCreateIssueAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateIssueAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateIssueAttachment', $request);
    }

    public function issueGetIssueAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueAttachmentRestRequest $request)
    {
        return $this->issueGetIssueAttachmentAsync($request)->wait();
    }

    public function issueGetIssueAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetIssueAttachment', $request);
    }

    public function issueDeleteIssueAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueAttachmentRestRequest $request)
    {
        return $this->issueDeleteIssueAttachmentAsync($request)->wait();
    }

    public function issueDeleteIssueAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteIssueAttachment', $request);
    }

    public function issueEditIssueAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueAttachmentRestRequest $request)
    {
        return $this->issueEditIssueAttachmentAsync($request)->wait();
    }

    public function issueEditIssueAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditIssueAttachment', $request);
    }

    public function issueGetComments(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentsRestRequest $request)
    {
        return $this->issueGetCommentsAsync($request)->wait();
    }

    public function issueGetCommentsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetComments', $request);
    }

    public function issueCreateComment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateCommentRestRequest $request)
    {
        return $this->issueCreateCommentAsync($request)->wait();
    }

    public function issueCreateCommentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateCommentRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateComment', $request);
    }

    public function issueDeleteCommentDeprecated(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentDeprecatedRestRequest $request)
    {
        return $this->issueDeleteCommentDeprecatedAsync($request)->wait();
    }

    public function issueDeleteCommentDeprecatedAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteCommentDeprecatedRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteCommentDeprecated', $request);
    }

    public function issueEditCommentDeprecated(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditCommentDeprecatedRestRequest $request)
    {
        return $this->issueEditCommentDeprecatedAsync($request)->wait();
    }

    public function issueEditCommentDeprecatedAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditCommentDeprecatedRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditCommentDeprecated', $request);
    }

    public function issueEditIssueDeadline(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueDeadlineRestRequest $request)
    {
        return $this->issueEditIssueDeadlineAsync($request)->wait();
    }

    public function issueEditIssueDeadlineAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditIssueDeadlineRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditIssueDeadline', $request);
    }

    public function issueGetLabels(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetLabelsRestRequest $request)
    {
        return $this->issueGetLabelsAsync($request)->wait();
    }

    public function issueGetLabelsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetLabelsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetLabels', $request);
    }

    public function issueReplaceLabels(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueReplaceLabelsRestRequest $request)
    {
        return $this->issueReplaceLabelsAsync($request)->wait();
    }

    public function issueReplaceLabelsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueReplaceLabelsRestRequest $request)
    {
        return $this->callOperationAsync('IssueReplaceLabels', $request);
    }

    public function issueAddLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddLabelRestRequest $request)
    {
        return $this->issueAddLabelAsync($request)->wait();
    }

    public function issueAddLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueAddLabel', $request);
    }

    public function issueClearLabels(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueClearLabelsRestRequest $request)
    {
        return $this->issueClearLabelsAsync($request)->wait();
    }

    public function issueClearLabelsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueClearLabelsRestRequest $request)
    {
        return $this->callOperationAsync('IssueClearLabels', $request);
    }

    public function issueRemoveLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueRemoveLabelRestRequest $request)
    {
        return $this->issueRemoveLabelAsync($request)->wait();
    }

    public function issueRemoveLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueRemoveLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueRemoveLabel', $request);
    }

    public function issueGetIssueReactions(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueReactionsRestRequest $request)
    {
        return $this->issueGetIssueReactionsAsync($request)->wait();
    }

    public function issueGetIssueReactionsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetIssueReactionsRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetIssueReactions', $request);
    }

    public function issuePostIssueReaction(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssuePostIssueReactionRestRequest $request)
    {
        return $this->issuePostIssueReactionAsync($request)->wait();
    }

    public function issuePostIssueReactionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssuePostIssueReactionRestRequest $request)
    {
        return $this->callOperationAsync('IssuePostIssueReaction', $request);
    }

    public function issueDeleteIssueReaction(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueReactionRestRequest $request)
    {
        return $this->issueDeleteIssueReactionAsync($request)->wait();
    }

    public function issueDeleteIssueReactionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteIssueReactionRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteIssueReaction', $request);
    }

    public function issueDeleteStopWatch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteStopWatchRestRequest $request)
    {
        return $this->issueDeleteStopWatchAsync($request)->wait();
    }

    public function issueDeleteStopWatchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteStopWatchRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteStopWatch', $request);
    }

    public function issueStartStopWatch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueStartStopWatchRestRequest $request)
    {
        return $this->issueStartStopWatchAsync($request)->wait();
    }

    public function issueStartStopWatchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueStartStopWatchRestRequest $request)
    {
        return $this->callOperationAsync('IssueStartStopWatch', $request);
    }

    public function issueStopStopWatch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueStopStopWatchRestRequest $request)
    {
        return $this->issueStopStopWatchAsync($request)->wait();
    }

    public function issueStopStopWatchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueStopStopWatchRestRequest $request)
    {
        return $this->callOperationAsync('IssueStopStopWatch', $request);
    }

    public function issueSubscriptions(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueSubscriptionsRestRequest $request)
    {
        return $this->issueSubscriptionsAsync($request)->wait();
    }

    public function issueSubscriptionsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueSubscriptionsRestRequest $request)
    {
        return $this->callOperationAsync('IssueSubscriptions', $request);
    }

    public function issueCheckSubscription(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCheckSubscriptionRestRequest $request)
    {
        return $this->issueCheckSubscriptionAsync($request)->wait();
    }

    public function issueCheckSubscriptionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCheckSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('IssueCheckSubscription', $request);
    }

    public function issueAddSubscription(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddSubscriptionRestRequest $request)
    {
        return $this->issueAddSubscriptionAsync($request)->wait();
    }

    public function issueAddSubscriptionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('IssueAddSubscription', $request);
    }

    public function issueDeleteSubscription(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteSubscriptionRestRequest $request)
    {
        return $this->issueDeleteSubscriptionAsync($request)->wait();
    }

    public function issueDeleteSubscriptionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteSubscription', $request);
    }

    public function issueGetCommentsAndTimeline(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentsAndTimelineRestRequest $request)
    {
        return $this->issueGetCommentsAndTimelineAsync($request)->wait();
    }

    public function issueGetCommentsAndTimelineAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetCommentsAndTimelineRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetCommentsAndTimeline', $request);
    }

    public function issueTrackedTimes(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueTrackedTimesRestRequest $request)
    {
        return $this->issueTrackedTimesAsync($request)->wait();
    }

    public function issueTrackedTimesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueTrackedTimesRestRequest $request)
    {
        return $this->callOperationAsync('IssueTrackedTimes', $request);
    }

    public function issueAddTime(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddTimeRestRequest $request)
    {
        return $this->issueAddTimeAsync($request)->wait();
    }

    public function issueAddTimeAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueAddTimeRestRequest $request)
    {
        return $this->callOperationAsync('IssueAddTime', $request);
    }

    public function issueResetTime(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueResetTimeRestRequest $request)
    {
        return $this->issueResetTimeAsync($request)->wait();
    }

    public function issueResetTimeAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueResetTimeRestRequest $request)
    {
        return $this->callOperationAsync('IssueResetTime', $request);
    }

    public function issueDeleteTime(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteTimeRestRequest $request)
    {
        return $this->issueDeleteTimeAsync($request)->wait();
    }

    public function issueDeleteTimeAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteTimeRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteTime', $request);
    }

    public function repoListKeys(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListKeysRestRequest $request)
    {
        return $this->repoListKeysAsync($request)->wait();
    }

    public function repoListKeysAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListKeysRestRequest $request)
    {
        return $this->callOperationAsync('RepoListKeys', $request);
    }

    public function repoCreateKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateKeyRestRequest $request)
    {
        return $this->repoCreateKeyAsync($request)->wait();
    }

    public function repoCreateKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateKeyRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateKey', $request);
    }

    public function repoGetKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetKeyRestRequest $request)
    {
        return $this->repoGetKeyAsync($request)->wait();
    }

    public function repoGetKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetKeyRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetKey', $request);
    }

    public function repoDeleteKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteKeyRestRequest $request)
    {
        return $this->repoDeleteKeyAsync($request)->wait();
    }

    public function repoDeleteKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteKeyRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteKey', $request);
    }

    public function issueListLabels(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListLabelsRestRequest $request)
    {
        return $this->issueListLabelsAsync($request)->wait();
    }

    public function issueListLabelsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueListLabelsRestRequest $request)
    {
        return $this->callOperationAsync('IssueListLabels', $request);
    }

    public function issueCreateLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateLabelRestRequest $request)
    {
        return $this->issueCreateLabelAsync($request)->wait();
    }

    public function issueCreateLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateLabel', $request);
    }

    public function issueGetLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetLabelRestRequest $request)
    {
        return $this->issueGetLabelAsync($request)->wait();
    }

    public function issueGetLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetLabel', $request);
    }

    public function issueDeleteLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteLabelRestRequest $request)
    {
        return $this->issueDeleteLabelAsync($request)->wait();
    }

    public function issueDeleteLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteLabel', $request);
    }

    public function issueEditLabel(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditLabelRestRequest $request)
    {
        return $this->issueEditLabelAsync($request)->wait();
    }

    public function issueEditLabelAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditLabelRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditLabel', $request);
    }

    public function repoGetLanguages(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetLanguagesRestRequest $request)
    {
        return $this->repoGetLanguagesAsync($request)->wait();
    }

    public function repoGetLanguagesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetLanguagesRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetLanguages', $request);
    }

    public function repoGetRawFileOrLFS(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRawFileOrLFSRestRequest $request)
    {
        return $this->repoGetRawFileOrLFSAsync($request)->wait();
    }

    public function repoGetRawFileOrLFSAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRawFileOrLFSRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetRawFileOrLFS', $request);
    }

    public function issueGetMilestonesList(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetMilestonesListRestRequest $request)
    {
        return $this->issueGetMilestonesListAsync($request)->wait();
    }

    public function issueGetMilestonesListAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetMilestonesListRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetMilestonesList', $request);
    }

    public function issueCreateMilestone(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateMilestoneRestRequest $request)
    {
        return $this->issueCreateMilestoneAsync($request)->wait();
    }

    public function issueCreateMilestoneAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueCreateMilestoneRestRequest $request)
    {
        return $this->callOperationAsync('IssueCreateMilestone', $request);
    }

    public function issueGetMilestone(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetMilestoneRestRequest $request)
    {
        return $this->issueGetMilestoneAsync($request)->wait();
    }

    public function issueGetMilestoneAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueGetMilestoneRestRequest $request)
    {
        return $this->callOperationAsync('IssueGetMilestone', $request);
    }

    public function issueDeleteMilestone(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteMilestoneRestRequest $request)
    {
        return $this->issueDeleteMilestoneAsync($request)->wait();
    }

    public function issueDeleteMilestoneAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueDeleteMilestoneRestRequest $request)
    {
        return $this->callOperationAsync('IssueDeleteMilestone', $request);
    }

    public function issueEditMilestone(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditMilestoneRestRequest $request)
    {
        return $this->issueEditMilestoneAsync($request)->wait();
    }

    public function issueEditMilestoneAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\IssueEditMilestoneRestRequest $request)
    {
        return $this->callOperationAsync('IssueEditMilestone', $request);
    }

    public function repoMirrorSync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMirrorSyncRestRequest $request)
    {
        return $this->repoMirrorSyncAsync($request)->wait();
    }

    public function repoMirrorSyncAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMirrorSyncRestRequest $request)
    {
        return $this->callOperationAsync('RepoMirrorSync', $request);
    }

    public function notifyGetRepoList(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetRepoListRestRequest $request)
    {
        return $this->notifyGetRepoListAsync($request)->wait();
    }

    public function notifyGetRepoListAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyGetRepoListRestRequest $request)
    {
        return $this->callOperationAsync('NotifyGetRepoList', $request);
    }

    public function notifyReadRepoList(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadRepoListRestRequest $request)
    {
        return $this->notifyReadRepoListAsync($request)->wait();
    }

    public function notifyReadRepoListAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\NotifyReadRepoListRestRequest $request)
    {
        return $this->callOperationAsync('NotifyReadRepoList', $request);
    }

    public function repoListPullRequests(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPullRequestsRestRequest $request)
    {
        return $this->repoListPullRequestsAsync($request)->wait();
    }

    public function repoListPullRequestsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPullRequestsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListPullRequests', $request);
    }

    public function repoCreatePullRequest(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullRequestRestRequest $request)
    {
        return $this->repoCreatePullRequestAsync($request)->wait();
    }

    public function repoCreatePullRequestAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreatePullRequest', $request);
    }

    public function repoGetPullRequest(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestRestRequest $request)
    {
        return $this->repoGetPullRequestAsync($request)->wait();
    }

    public function repoGetPullRequestAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPullRequest', $request);
    }

    public function repoEditPullRequest(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditPullRequestRestRequest $request)
    {
        return $this->repoEditPullRequestAsync($request)->wait();
    }

    public function repoEditPullRequestAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditPullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditPullRequest', $request);
    }

    public function repoDownloadPullDiffOrPatch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDownloadPullDiffOrPatchRestRequest $request)
    {
        return $this->repoDownloadPullDiffOrPatchAsync($request)->wait();
    }

    public function repoDownloadPullDiffOrPatchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDownloadPullDiffOrPatchRestRequest $request)
    {
        return $this->callOperationAsync('RepoDownloadPullDiffOrPatch', $request);
    }

    public function repoGetPullRequestCommits(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestCommitsRestRequest $request)
    {
        return $this->repoGetPullRequestCommitsAsync($request)->wait();
    }

    public function repoGetPullRequestCommitsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestCommitsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPullRequestCommits', $request);
    }

    public function repoGetPullRequestFiles(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestFilesRestRequest $request)
    {
        return $this->repoGetPullRequestFilesAsync($request)->wait();
    }

    public function repoGetPullRequestFilesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullRequestFilesRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPullRequestFiles', $request);
    }

    public function repoPullRequestIsMerged(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoPullRequestIsMergedRestRequest $request)
    {
        return $this->repoPullRequestIsMergedAsync($request)->wait();
    }

    public function repoPullRequestIsMergedAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoPullRequestIsMergedRestRequest $request)
    {
        return $this->callOperationAsync('RepoPullRequestIsMerged', $request);
    }

    public function repoMergePullRequest(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMergePullRequestRestRequest $request)
    {
        return $this->repoMergePullRequestAsync($request)->wait();
    }

    public function repoMergePullRequestAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoMergePullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoMergePullRequest', $request);
    }

    public function repoCancelScheduledAutoMerge(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCancelScheduledAutoMergeRestRequest $request)
    {
        return $this->repoCancelScheduledAutoMergeAsync($request)->wait();
    }

    public function repoCancelScheduledAutoMergeAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCancelScheduledAutoMergeRestRequest $request)
    {
        return $this->callOperationAsync('RepoCancelScheduledAutoMerge', $request);
    }

    public function repoCreatePullReviewRequests(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullReviewRequestsRestRequest $request)
    {
        return $this->repoCreatePullReviewRequestsAsync($request)->wait();
    }

    public function repoCreatePullReviewRequestsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullReviewRequestsRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreatePullReviewRequests', $request);
    }

    public function repoDeletePullReviewRequests(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePullReviewRequestsRestRequest $request)
    {
        return $this->repoDeletePullReviewRequestsAsync($request)->wait();
    }

    public function repoDeletePullReviewRequestsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePullReviewRequestsRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeletePullReviewRequests', $request);
    }

    public function repoListPullReviews(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPullReviewsRestRequest $request)
    {
        return $this->repoListPullReviewsAsync($request)->wait();
    }

    public function repoListPullReviewsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPullReviewsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListPullReviews', $request);
    }

    public function repoCreatePullReview(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullReviewRestRequest $request)
    {
        return $this->repoCreatePullReviewAsync($request)->wait();
    }

    public function repoCreatePullReviewAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreatePullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreatePullReview', $request);
    }

    public function repoGetPullReview(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullReviewRestRequest $request)
    {
        return $this->repoGetPullReviewAsync($request)->wait();
    }

    public function repoGetPullReviewAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPullReview', $request);
    }

    public function repoSubmitPullReview(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSubmitPullReviewRestRequest $request)
    {
        return $this->repoSubmitPullReviewAsync($request)->wait();
    }

    public function repoSubmitPullReviewAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSubmitPullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoSubmitPullReview', $request);
    }

    public function repoDeletePullReview(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePullReviewRestRequest $request)
    {
        return $this->repoDeletePullReviewAsync($request)->wait();
    }

    public function repoDeletePullReviewAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeletePullReview', $request);
    }

    public function repoGetPullReviewComments(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullReviewCommentsRestRequest $request)
    {
        return $this->repoGetPullReviewCommentsAsync($request)->wait();
    }

    public function repoGetPullReviewCommentsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPullReviewCommentsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPullReviewComments', $request);
    }

    public function repoDismissPullReview(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDismissPullReviewRestRequest $request)
    {
        return $this->repoDismissPullReviewAsync($request)->wait();
    }

    public function repoDismissPullReviewAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDismissPullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoDismissPullReview', $request);
    }

    public function repoUnDismissPullReview(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUnDismissPullReviewRestRequest $request)
    {
        return $this->repoUnDismissPullReviewAsync($request)->wait();
    }

    public function repoUnDismissPullReviewAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUnDismissPullReviewRestRequest $request)
    {
        return $this->callOperationAsync('RepoUnDismissPullReview', $request);
    }

    public function repoUpdatePullRequest(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdatePullRequestRestRequest $request)
    {
        return $this->repoUpdatePullRequestAsync($request)->wait();
    }

    public function repoUpdatePullRequestAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdatePullRequestRestRequest $request)
    {
        return $this->callOperationAsync('RepoUpdatePullRequest', $request);
    }

    public function repoListPushMirrors(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPushMirrorsRestRequest $request)
    {
        return $this->repoListPushMirrorsAsync($request)->wait();
    }

    public function repoListPushMirrorsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListPushMirrorsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListPushMirrors', $request);
    }

    public function repoAddPushMirror(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddPushMirrorRestRequest $request)
    {
        return $this->repoAddPushMirrorAsync($request)->wait();
    }

    public function repoAddPushMirrorAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddPushMirrorRestRequest $request)
    {
        return $this->callOperationAsync('RepoAddPushMirror', $request);
    }

    public function repoPushMirrorSync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoPushMirrorSyncRestRequest $request)
    {
        return $this->repoPushMirrorSyncAsync($request)->wait();
    }

    public function repoPushMirrorSyncAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoPushMirrorSyncRestRequest $request)
    {
        return $this->callOperationAsync('RepoPushMirrorSync', $request);
    }

    public function repoGetPushMirrorByRemoteName(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPushMirrorByRemoteNameRestRequest $request)
    {
        return $this->repoGetPushMirrorByRemoteNameAsync($request)->wait();
    }

    public function repoGetPushMirrorByRemoteNameAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetPushMirrorByRemoteNameRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetPushMirrorByRemoteName', $request);
    }

    public function repoDeletePushMirror(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePushMirrorRestRequest $request)
    {
        return $this->repoDeletePushMirrorAsync($request)->wait();
    }

    public function repoDeletePushMirrorAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeletePushMirrorRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeletePushMirror', $request);
    }

    public function repoGetRawFile(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRawFileRestRequest $request)
    {
        return $this->repoGetRawFileAsync($request)->wait();
    }

    public function repoGetRawFileAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetRawFileRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetRawFile', $request);
    }

    public function repoListReleases(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListReleasesRestRequest $request)
    {
        return $this->repoListReleasesAsync($request)->wait();
    }

    public function repoListReleasesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListReleasesRestRequest $request)
    {
        return $this->callOperationAsync('RepoListReleases', $request);
    }

    public function repoCreateRelease(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateReleaseRestRequest $request)
    {
        return $this->repoCreateReleaseAsync($request)->wait();
    }

    public function repoCreateReleaseAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateRelease', $request);
    }

    public function repoGetLatestRelease(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetLatestReleaseRestRequest $request)
    {
        return $this->repoGetLatestReleaseAsync($request)->wait();
    }

    public function repoGetLatestReleaseAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetLatestReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetLatestRelease', $request);
    }

    public function repoGetReleaseByTag(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseByTagRestRequest $request)
    {
        return $this->repoGetReleaseByTagAsync($request)->wait();
    }

    public function repoGetReleaseByTagAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseByTagRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetReleaseByTag', $request);
    }

    public function repoDeleteReleaseByTag(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseByTagRestRequest $request)
    {
        return $this->repoDeleteReleaseByTagAsync($request)->wait();
    }

    public function repoDeleteReleaseByTagAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseByTagRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteReleaseByTag', $request);
    }

    public function repoGetRelease(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseRestRequest $request)
    {
        return $this->repoGetReleaseAsync($request)->wait();
    }

    public function repoGetReleaseAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetRelease', $request);
    }

    public function repoDeleteRelease(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseRestRequest $request)
    {
        return $this->repoDeleteReleaseAsync($request)->wait();
    }

    public function repoDeleteReleaseAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteRelease', $request);
    }

    public function repoEditRelease(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditReleaseRestRequest $request)
    {
        return $this->repoEditReleaseAsync($request)->wait();
    }

    public function repoEditReleaseAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditReleaseRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditRelease', $request);
    }

    public function repoListReleaseAttachments(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListReleaseAttachmentsRestRequest $request)
    {
        return $this->repoListReleaseAttachmentsAsync($request)->wait();
    }

    public function repoListReleaseAttachmentsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListReleaseAttachmentsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListReleaseAttachments', $request);
    }

    public function repoCreateReleaseAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateReleaseAttachmentRestRequest $request)
    {
        return $this->repoCreateReleaseAttachmentAsync($request)->wait();
    }

    public function repoCreateReleaseAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateReleaseAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateReleaseAttachment', $request);
    }

    public function repoGetReleaseAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseAttachmentRestRequest $request)
    {
        return $this->repoGetReleaseAttachmentAsync($request)->wait();
    }

    public function repoGetReleaseAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReleaseAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetReleaseAttachment', $request);
    }

    public function repoDeleteReleaseAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseAttachmentRestRequest $request)
    {
        return $this->repoDeleteReleaseAttachmentAsync($request)->wait();
    }

    public function repoDeleteReleaseAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteReleaseAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteReleaseAttachment', $request);
    }

    public function repoEditReleaseAttachment(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditReleaseAttachmentRestRequest $request)
    {
        return $this->repoEditReleaseAttachmentAsync($request)->wait();
    }

    public function repoEditReleaseAttachmentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditReleaseAttachmentRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditReleaseAttachment', $request);
    }

    public function repoGetReviewers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReviewersRestRequest $request)
    {
        return $this->repoGetReviewersAsync($request)->wait();
    }

    public function repoGetReviewersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetReviewersRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetReviewers', $request);
    }

    public function repoSigningKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSigningKeyRestRequest $request)
    {
        return $this->repoSigningKeyAsync($request)->wait();
    }

    public function repoSigningKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoSigningKeyRestRequest $request)
    {
        return $this->callOperationAsync('RepoSigningKey', $request);
    }

    public function repoListStargazers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStargazersRestRequest $request)
    {
        return $this->repoListStargazersAsync($request)->wait();
    }

    public function repoListStargazersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStargazersRestRequest $request)
    {
        return $this->callOperationAsync('RepoListStargazers', $request);
    }

    public function repoListStatuses(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStatusesRestRequest $request)
    {
        return $this->repoListStatusesAsync($request)->wait();
    }

    public function repoListStatusesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListStatusesRestRequest $request)
    {
        return $this->callOperationAsync('RepoListStatuses', $request);
    }

    public function repoCreateStatus(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateStatusRestRequest $request)
    {
        return $this->repoCreateStatusAsync($request)->wait();
    }

    public function repoCreateStatusAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateStatusRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateStatus', $request);
    }

    public function repoListSubscribers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListSubscribersRestRequest $request)
    {
        return $this->repoListSubscribersAsync($request)->wait();
    }

    public function repoListSubscribersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListSubscribersRestRequest $request)
    {
        return $this->callOperationAsync('RepoListSubscribers', $request);
    }

    public function userCurrentCheckSubscription(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckSubscriptionRestRequest $request)
    {
        return $this->userCurrentCheckSubscriptionAsync($request)->wait();
    }

    public function userCurrentCheckSubscriptionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentCheckSubscription', $request);
    }

    public function userCurrentPutSubscription(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutSubscriptionRestRequest $request)
    {
        return $this->userCurrentPutSubscriptionAsync($request)->wait();
    }

    public function userCurrentPutSubscriptionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPutSubscription', $request);
    }

    public function userCurrentDeleteSubscription(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteSubscriptionRestRequest $request)
    {
        return $this->userCurrentDeleteSubscriptionAsync($request)->wait();
    }

    public function userCurrentDeleteSubscriptionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteSubscriptionRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteSubscription', $request);
    }

    public function repoListTags(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTagsRestRequest $request)
    {
        return $this->repoListTagsAsync($request)->wait();
    }

    public function repoListTagsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTagsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListTags', $request);
    }

    public function repoCreateTag(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateTagRestRequest $request)
    {
        return $this->repoCreateTagAsync($request)->wait();
    }

    public function repoCreateTagAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateTagRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateTag', $request);
    }

    public function repoGetTag(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetTagRestRequest $request)
    {
        return $this->repoGetTagAsync($request)->wait();
    }

    public function repoGetTagAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetTagRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetTag', $request);
    }

    public function repoDeleteTag(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTagRestRequest $request)
    {
        return $this->repoDeleteTagAsync($request)->wait();
    }

    public function repoDeleteTagAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTagRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteTag', $request);
    }

    public function repoListTeams(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTeamsRestRequest $request)
    {
        return $this->repoListTeamsAsync($request)->wait();
    }

    public function repoListTeamsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTeamsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListTeams', $request);
    }

    public function repoCheckTeam(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCheckTeamRestRequest $request)
    {
        return $this->repoCheckTeamAsync($request)->wait();
    }

    public function repoCheckTeamAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCheckTeamRestRequest $request)
    {
        return $this->callOperationAsync('RepoCheckTeam', $request);
    }

    public function repoAddTeam(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddTeamRestRequest $request)
    {
        return $this->repoAddTeamAsync($request)->wait();
    }

    public function repoAddTeamAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddTeamRestRequest $request)
    {
        return $this->callOperationAsync('RepoAddTeam', $request);
    }

    public function repoDeleteTeam(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTeamRestRequest $request)
    {
        return $this->repoDeleteTeamAsync($request)->wait();
    }

    public function repoDeleteTeamAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTeamRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteTeam', $request);
    }

    public function repoTrackedTimes(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTrackedTimesRestRequest $request)
    {
        return $this->repoTrackedTimesAsync($request)->wait();
    }

    public function repoTrackedTimesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTrackedTimesRestRequest $request)
    {
        return $this->callOperationAsync('RepoTrackedTimes', $request);
    }

    public function userTrackedTimes(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserTrackedTimesRestRequest $request)
    {
        return $this->userTrackedTimesAsync($request)->wait();
    }

    public function userTrackedTimesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserTrackedTimesRestRequest $request)
    {
        return $this->callOperationAsync('UserTrackedTimes', $request);
    }

    public function repoListTopics(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTopicsRestRequest $request)
    {
        return $this->repoListTopicsAsync($request)->wait();
    }

    public function repoListTopicsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoListTopicsRestRequest $request)
    {
        return $this->callOperationAsync('RepoListTopics', $request);
    }

    public function repoUpdateTopics(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdateTopicsRestRequest $request)
    {
        return $this->repoUpdateTopicsAsync($request)->wait();
    }

    public function repoUpdateTopicsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoUpdateTopicsRestRequest $request)
    {
        return $this->callOperationAsync('RepoUpdateTopics', $request);
    }

    public function repoAddTopic(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddTopicRestRequest $request)
    {
        return $this->repoAddTopicAsync($request)->wait();
    }

    public function repoAddTopicAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoAddTopicRestRequest $request)
    {
        return $this->callOperationAsync('RepoAddTopic', $request);
    }

    public function repoDeleteTopic(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTopicRestRequest $request)
    {
        return $this->repoDeleteTopicAsync($request)->wait();
    }

    public function repoDeleteTopicAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteTopicRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteTopic', $request);
    }

    public function repoTransfer(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTransferRestRequest $request)
    {
        return $this->repoTransferAsync($request)->wait();
    }

    public function repoTransferAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoTransferRestRequest $request)
    {
        return $this->callOperationAsync('RepoTransfer', $request);
    }

    public function acceptRepoTransfer(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AcceptRepoTransferRestRequest $request)
    {
        return $this->acceptRepoTransferAsync($request)->wait();
    }

    public function acceptRepoTransferAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\AcceptRepoTransferRestRequest $request)
    {
        return $this->callOperationAsync('AcceptRepoTransfer', $request);
    }

    public function rejectRepoTransfer(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RejectRepoTransferRestRequest $request)
    {
        return $this->rejectRepoTransferAsync($request)->wait();
    }

    public function rejectRepoTransferAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RejectRepoTransferRestRequest $request)
    {
        return $this->callOperationAsync('RejectRepoTransfer', $request);
    }

    public function repoCreateWikiPage(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateWikiPageRestRequest $request)
    {
        return $this->repoCreateWikiPageAsync($request)->wait();
    }

    public function repoCreateWikiPageAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoCreateWikiPageRestRequest $request)
    {
        return $this->callOperationAsync('RepoCreateWikiPage', $request);
    }

    public function repoGetWikiPage(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPageRestRequest $request)
    {
        return $this->repoGetWikiPageAsync($request)->wait();
    }

    public function repoGetWikiPageAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPageRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetWikiPage', $request);
    }

    public function repoDeleteWikiPage(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteWikiPageRestRequest $request)
    {
        return $this->repoDeleteWikiPageAsync($request)->wait();
    }

    public function repoDeleteWikiPageAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoDeleteWikiPageRestRequest $request)
    {
        return $this->callOperationAsync('RepoDeleteWikiPage', $request);
    }

    public function repoEditWikiPage(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditWikiPageRestRequest $request)
    {
        return $this->repoEditWikiPageAsync($request)->wait();
    }

    public function repoEditWikiPageAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoEditWikiPageRestRequest $request)
    {
        return $this->callOperationAsync('RepoEditWikiPage', $request);
    }

    public function repoGetWikiPages(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPagesRestRequest $request)
    {
        return $this->repoGetWikiPagesAsync($request)->wait();
    }

    public function repoGetWikiPagesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPagesRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetWikiPages', $request);
    }

    public function repoGetWikiPageRevisions(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPageRevisionsRestRequest $request)
    {
        return $this->repoGetWikiPageRevisionsAsync($request)->wait();
    }

    public function repoGetWikiPageRevisionsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetWikiPageRevisionsRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetWikiPageRevisions', $request);
    }

    public function generateRepo(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GenerateRepoRestRequest $request)
    {
        return $this->generateRepoAsync($request)->wait();
    }

    public function generateRepoAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GenerateRepoRestRequest $request)
    {
        return $this->callOperationAsync('GenerateRepo', $request);
    }

    public function repoGetByID(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetByIDRestRequest $request)
    {
        return $this->repoGetByIDAsync($request)->wait();
    }

    public function repoGetByIDAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\RepoGetByIDRestRequest $request)
    {
        return $this->callOperationAsync('RepoGetByID', $request);
    }

    public function getGeneralAPISettings(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralAPISettingsRestRequest $request)
    {
        return $this->getGeneralAPISettingsAsync($request)->wait();
    }

    public function getGeneralAPISettingsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralAPISettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetGeneralAPISettings', $request);
    }

    public function getGeneralAttachmentSettings(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralAttachmentSettingsRestRequest $request)
    {
        return $this->getGeneralAttachmentSettingsAsync($request)->wait();
    }

    public function getGeneralAttachmentSettingsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralAttachmentSettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetGeneralAttachmentSettings', $request);
    }

    public function getGeneralRepositorySettings(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralRepositorySettingsRestRequest $request)
    {
        return $this->getGeneralRepositorySettingsAsync($request)->wait();
    }

    public function getGeneralRepositorySettingsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralRepositorySettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetGeneralRepositorySettings', $request);
    }

    public function getGeneralUISettings(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralUISettingsRestRequest $request)
    {
        return $this->getGeneralUISettingsAsync($request)->wait();
    }

    public function getGeneralUISettingsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetGeneralUISettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetGeneralUISettings', $request);
    }

    public function getSigningKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetSigningKeyRestRequest $request)
    {
        return $this->getSigningKeyAsync($request)->wait();
    }

    public function getSigningKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetSigningKeyRestRequest $request)
    {
        return $this->callOperationAsync('GetSigningKey', $request);
    }

    public function orgGetTeam(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetTeamRestRequest $request)
    {
        return $this->orgGetTeamAsync($request)->wait();
    }

    public function orgGetTeamAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetTeamRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetTeam', $request);
    }

    public function orgDeleteTeam(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteTeamRestRequest $request)
    {
        return $this->orgDeleteTeamAsync($request)->wait();
    }

    public function orgDeleteTeamAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgDeleteTeamRestRequest $request)
    {
        return $this->callOperationAsync('OrgDeleteTeam', $request);
    }

    public function orgEditTeam(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditTeamRestRequest $request)
    {
        return $this->orgEditTeamAsync($request)->wait();
    }

    public function orgEditTeamAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgEditTeamRestRequest $request)
    {
        return $this->callOperationAsync('OrgEditTeam', $request);
    }

    public function orgListTeamMembers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamMembersRestRequest $request)
    {
        return $this->orgListTeamMembersAsync($request)->wait();
    }

    public function orgListTeamMembersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamMembersRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeamMembers', $request);
    }

    public function orgListTeamMember(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamMemberRestRequest $request)
    {
        return $this->orgListTeamMemberAsync($request)->wait();
    }

    public function orgListTeamMemberAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeamMember', $request);
    }

    public function orgAddTeamMember(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgAddTeamMemberRestRequest $request)
    {
        return $this->orgAddTeamMemberAsync($request)->wait();
    }

    public function orgAddTeamMemberAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgAddTeamMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgAddTeamMember', $request);
    }

    public function orgRemoveTeamMember(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgRemoveTeamMemberRestRequest $request)
    {
        return $this->orgRemoveTeamMemberAsync($request)->wait();
    }

    public function orgRemoveTeamMemberAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgRemoveTeamMemberRestRequest $request)
    {
        return $this->callOperationAsync('OrgRemoveTeamMember', $request);
    }

    public function orgListTeamRepos(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamReposRestRequest $request)
    {
        return $this->orgListTeamReposAsync($request)->wait();
    }

    public function orgListTeamReposAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamReposRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeamRepos', $request);
    }

    public function orgListTeamRepo(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamRepoRestRequest $request)
    {
        return $this->orgListTeamRepoAsync($request)->wait();
    }

    public function orgListTeamRepoAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListTeamRepoRestRequest $request)
    {
        return $this->callOperationAsync('OrgListTeamRepo', $request);
    }

    public function orgAddTeamRepository(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgAddTeamRepositoryRestRequest $request)
    {
        return $this->orgAddTeamRepositoryAsync($request)->wait();
    }

    public function orgAddTeamRepositoryAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgAddTeamRepositoryRestRequest $request)
    {
        return $this->callOperationAsync('OrgAddTeamRepository', $request);
    }

    public function orgRemoveTeamRepository(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgRemoveTeamRepositoryRestRequest $request)
    {
        return $this->orgRemoveTeamRepositoryAsync($request)->wait();
    }

    public function orgRemoveTeamRepositoryAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgRemoveTeamRepositoryRestRequest $request)
    {
        return $this->callOperationAsync('OrgRemoveTeamRepository', $request);
    }

    public function topicSearch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\TopicSearchRestRequest $request)
    {
        return $this->topicSearchAsync($request)->wait();
    }

    public function topicSearchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\TopicSearchRestRequest $request)
    {
        return $this->callOperationAsync('TopicSearch', $request);
    }

    public function userGetCurrent(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetCurrentRestRequest $request)
    {
        return $this->userGetCurrentAsync($request)->wait();
    }

    public function userGetCurrentAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetCurrentRestRequest $request)
    {
        return $this->callOperationAsync('UserGetCurrent', $request);
    }

    public function userGetOauth2Application(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetOauth2ApplicationRestRequest $request)
    {
        return $this->userGetOauth2ApplicationAsync($request)->wait();
    }

    public function userGetOauth2ApplicationAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetOauth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserGetOauth2Application', $request);
    }

    public function userCreateOAuth2Application(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCreateOAuth2ApplicationRestRequest $request)
    {
        return $this->userCreateOAuth2ApplicationAsync($request)->wait();
    }

    public function userCreateOAuth2ApplicationAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCreateOAuth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserCreateOAuth2Application', $request);
    }

    public function userGetOAuth2Application(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetOAuth2ApplicationRestRequest $request)
    {
        return $this->userGetOAuth2ApplicationAsync($request)->wait();
    }

    public function userGetOAuth2ApplicationAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetOAuth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserGetOAuth2Application', $request);
    }

    public function userDeleteOAuth2Application(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteOAuth2ApplicationRestRequest $request)
    {
        return $this->userDeleteOAuth2ApplicationAsync($request)->wait();
    }

    public function userDeleteOAuth2ApplicationAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteOAuth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserDeleteOAuth2Application', $request);
    }

    public function userUpdateOAuth2Application(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserUpdateOAuth2ApplicationRestRequest $request)
    {
        return $this->userUpdateOAuth2ApplicationAsync($request)->wait();
    }

    public function userUpdateOAuth2ApplicationAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserUpdateOAuth2ApplicationRestRequest $request)
    {
        return $this->callOperationAsync('UserUpdateOAuth2Application', $request);
    }

    public function userListEmails(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListEmailsRestRequest $request)
    {
        return $this->userListEmailsAsync($request)->wait();
    }

    public function userListEmailsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListEmailsRestRequest $request)
    {
        return $this->callOperationAsync('UserListEmails', $request);
    }

    public function userAddEmail(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserAddEmailRestRequest $request)
    {
        return $this->userAddEmailAsync($request)->wait();
    }

    public function userAddEmailAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserAddEmailRestRequest $request)
    {
        return $this->callOperationAsync('UserAddEmail', $request);
    }

    public function userDeleteEmail(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteEmailRestRequest $request)
    {
        return $this->userDeleteEmailAsync($request)->wait();
    }

    public function userDeleteEmailAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteEmailRestRequest $request)
    {
        return $this->callOperationAsync('UserDeleteEmail', $request);
    }

    public function userCurrentListFollowers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListFollowersRestRequest $request)
    {
        return $this->userCurrentListFollowersAsync($request)->wait();
    }

    public function userCurrentListFollowersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListFollowersRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListFollowers', $request);
    }

    public function userCurrentListFollowing(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListFollowingRestRequest $request)
    {
        return $this->userCurrentListFollowingAsync($request)->wait();
    }

    public function userCurrentListFollowingAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListFollowingRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListFollowing', $request);
    }

    public function userCurrentCheckFollowing(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckFollowingRestRequest $request)
    {
        return $this->userCurrentCheckFollowingAsync($request)->wait();
    }

    public function userCurrentCheckFollowingAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckFollowingRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentCheckFollowing', $request);
    }

    public function userCurrentPutFollow(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutFollowRestRequest $request)
    {
        return $this->userCurrentPutFollowAsync($request)->wait();
    }

    public function userCurrentPutFollowAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutFollowRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPutFollow', $request);
    }

    public function userCurrentDeleteFollow(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteFollowRestRequest $request)
    {
        return $this->userCurrentDeleteFollowAsync($request)->wait();
    }

    public function userCurrentDeleteFollowAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteFollowRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteFollow', $request);
    }

    public function getVerificationToken(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetVerificationTokenRestRequest $request)
    {
        return $this->getVerificationTokenAsync($request)->wait();
    }

    public function getVerificationTokenAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetVerificationTokenRestRequest $request)
    {
        return $this->callOperationAsync('GetVerificationToken', $request);
    }

    public function userVerifyGPGKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserVerifyGPGKeyRestRequest $request)
    {
        return $this->userVerifyGPGKeyAsync($request)->wait();
    }

    public function userVerifyGPGKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserVerifyGPGKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserVerifyGPGKey', $request);
    }

    public function userCurrentListGPGKeys(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListGPGKeysRestRequest $request)
    {
        return $this->userCurrentListGPGKeysAsync($request)->wait();
    }

    public function userCurrentListGPGKeysAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListGPGKeysRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListGPGKeys', $request);
    }

    public function userCurrentPostGPGKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPostGPGKeyRestRequest $request)
    {
        return $this->userCurrentPostGPGKeyAsync($request)->wait();
    }

    public function userCurrentPostGPGKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPostGPGKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPostGPGKey', $request);
    }

    public function userCurrentGetGPGKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentGetGPGKeyRestRequest $request)
    {
        return $this->userCurrentGetGPGKeyAsync($request)->wait();
    }

    public function userCurrentGetGPGKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentGetGPGKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentGetGPGKey', $request);
    }

    public function userCurrentDeleteGPGKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteGPGKeyRestRequest $request)
    {
        return $this->userCurrentDeleteGPGKeyAsync($request)->wait();
    }

    public function userCurrentDeleteGPGKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteGPGKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteGPGKey', $request);
    }

    public function userCurrentListKeys(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListKeysRestRequest $request)
    {
        return $this->userCurrentListKeysAsync($request)->wait();
    }

    public function userCurrentListKeysAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListKeysRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListKeys', $request);
    }

    public function userCurrentPostKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPostKeyRestRequest $request)
    {
        return $this->userCurrentPostKeyAsync($request)->wait();
    }

    public function userCurrentPostKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPostKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPostKey', $request);
    }

    public function userCurrentGetKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentGetKeyRestRequest $request)
    {
        return $this->userCurrentGetKeyAsync($request)->wait();
    }

    public function userCurrentGetKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentGetKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentGetKey', $request);
    }

    public function userCurrentDeleteKey(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteKeyRestRequest $request)
    {
        return $this->userCurrentDeleteKeyAsync($request)->wait();
    }

    public function userCurrentDeleteKeyAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteKeyRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteKey', $request);
    }

    public function orgListCurrentUserOrgs(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListCurrentUserOrgsRestRequest $request)
    {
        return $this->orgListCurrentUserOrgsAsync($request)->wait();
    }

    public function orgListCurrentUserOrgsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListCurrentUserOrgsRestRequest $request)
    {
        return $this->callOperationAsync('OrgListCurrentUserOrgs', $request);
    }

    public function userCurrentListRepos(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListReposRestRequest $request)
    {
        return $this->userCurrentListReposAsync($request)->wait();
    }

    public function userCurrentListReposAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListReposRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListRepos', $request);
    }

    public function createCurrentUserRepo(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateCurrentUserRepoRestRequest $request)
    {
        return $this->createCurrentUserRepoAsync($request)->wait();
    }

    public function createCurrentUserRepoAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\CreateCurrentUserRepoRestRequest $request)
    {
        return $this->callOperationAsync('CreateCurrentUserRepo', $request);
    }

    public function getUserSettings(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetUserSettingsRestRequest $request)
    {
        return $this->getUserSettingsAsync($request)->wait();
    }

    public function getUserSettingsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetUserSettingsRestRequest $request)
    {
        return $this->callOperationAsync('GetUserSettings', $request);
    }

    public function updateUserSettings(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UpdateUserSettingsRestRequest $request)
    {
        return $this->updateUserSettingsAsync($request)->wait();
    }

    public function updateUserSettingsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UpdateUserSettingsRestRequest $request)
    {
        return $this->callOperationAsync('UpdateUserSettings', $request);
    }

    public function userCurrentListStarred(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListStarredRestRequest $request)
    {
        return $this->userCurrentListStarredAsync($request)->wait();
    }

    public function userCurrentListStarredAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListStarredRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListStarred', $request);
    }

    public function userCurrentCheckStarring(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckStarringRestRequest $request)
    {
        return $this->userCurrentCheckStarringAsync($request)->wait();
    }

    public function userCurrentCheckStarringAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentCheckStarringRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentCheckStarring', $request);
    }

    public function userCurrentPutStar(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutStarRestRequest $request)
    {
        return $this->userCurrentPutStarAsync($request)->wait();
    }

    public function userCurrentPutStarAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentPutStarRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentPutStar', $request);
    }

    public function userCurrentDeleteStar(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteStarRestRequest $request)
    {
        return $this->userCurrentDeleteStarAsync($request)->wait();
    }

    public function userCurrentDeleteStarAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentDeleteStarRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentDeleteStar', $request);
    }

    public function userGetStopWatches(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetStopWatchesRestRequest $request)
    {
        return $this->userGetStopWatchesAsync($request)->wait();
    }

    public function userGetStopWatchesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetStopWatchesRestRequest $request)
    {
        return $this->callOperationAsync('UserGetStopWatches', $request);
    }

    public function userCurrentListSubscriptions(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListSubscriptionsRestRequest $request)
    {
        return $this->userCurrentListSubscriptionsAsync($request)->wait();
    }

    public function userCurrentListSubscriptionsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentListSubscriptionsRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentListSubscriptions', $request);
    }

    public function userListTeams(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListTeamsRestRequest $request)
    {
        return $this->userListTeamsAsync($request)->wait();
    }

    public function userListTeamsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListTeamsRestRequest $request)
    {
        return $this->callOperationAsync('UserListTeams', $request);
    }

    public function userCurrentTrackedTimes(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentTrackedTimesRestRequest $request)
    {
        return $this->userCurrentTrackedTimesAsync($request)->wait();
    }

    public function userCurrentTrackedTimesAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCurrentTrackedTimesRestRequest $request)
    {
        return $this->callOperationAsync('UserCurrentTrackedTimes', $request);
    }

    public function userSearch(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserSearchRestRequest $request)
    {
        return $this->userSearchAsync($request)->wait();
    }

    public function userSearchAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserSearchRestRequest $request)
    {
        return $this->callOperationAsync('UserSearch', $request);
    }

    public function userGet(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetRestRequest $request)
    {
        return $this->userGetAsync($request)->wait();
    }

    public function userGetAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetRestRequest $request)
    {
        return $this->callOperationAsync('UserGet', $request);
    }

    public function userListFollowers(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListFollowersRestRequest $request)
    {
        return $this->userListFollowersAsync($request)->wait();
    }

    public function userListFollowersAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListFollowersRestRequest $request)
    {
        return $this->callOperationAsync('UserListFollowers', $request);
    }

    public function userListFollowing(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListFollowingRestRequest $request)
    {
        return $this->userListFollowingAsync($request)->wait();
    }

    public function userListFollowingAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListFollowingRestRequest $request)
    {
        return $this->callOperationAsync('UserListFollowing', $request);
    }

    public function userCheckFollowing(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCheckFollowingRestRequest $request)
    {
        return $this->userCheckFollowingAsync($request)->wait();
    }

    public function userCheckFollowingAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCheckFollowingRestRequest $request)
    {
        return $this->callOperationAsync('UserCheckFollowing', $request);
    }

    public function userListGPGKeys(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListGPGKeysRestRequest $request)
    {
        return $this->userListGPGKeysAsync($request)->wait();
    }

    public function userListGPGKeysAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListGPGKeysRestRequest $request)
    {
        return $this->callOperationAsync('UserListGPGKeys', $request);
    }

    public function userGetHeatmapData(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetHeatmapDataRestRequest $request)
    {
        return $this->userGetHeatmapDataAsync($request)->wait();
    }

    public function userGetHeatmapDataAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetHeatmapDataRestRequest $request)
    {
        return $this->callOperationAsync('UserGetHeatmapData', $request);
    }

    public function userListKeys(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListKeysRestRequest $request)
    {
        return $this->userListKeysAsync($request)->wait();
    }

    public function userListKeysAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListKeysRestRequest $request)
    {
        return $this->callOperationAsync('UserListKeys', $request);
    }

    public function orgListUserOrgs(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListUserOrgsRestRequest $request)
    {
        return $this->orgListUserOrgsAsync($request)->wait();
    }

    public function orgListUserOrgsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgListUserOrgsRestRequest $request)
    {
        return $this->callOperationAsync('OrgListUserOrgs', $request);
    }

    public function orgGetUserPermissions(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetUserPermissionsRestRequest $request)
    {
        return $this->orgGetUserPermissionsAsync($request)->wait();
    }

    public function orgGetUserPermissionsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\OrgGetUserPermissionsRestRequest $request)
    {
        return $this->callOperationAsync('OrgGetUserPermissions', $request);
    }

    public function userListRepos(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListReposRestRequest $request)
    {
        return $this->userListReposAsync($request)->wait();
    }

    public function userListReposAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListReposRestRequest $request)
    {
        return $this->callOperationAsync('UserListRepos', $request);
    }

    public function userListStarred(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListStarredRestRequest $request)
    {
        return $this->userListStarredAsync($request)->wait();
    }

    public function userListStarredAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListStarredRestRequest $request)
    {
        return $this->callOperationAsync('UserListStarred', $request);
    }

    public function userListSubscriptions(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListSubscriptionsRestRequest $request)
    {
        return $this->userListSubscriptionsAsync($request)->wait();
    }

    public function userListSubscriptionsAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserListSubscriptionsRestRequest $request)
    {
        return $this->callOperationAsync('UserListSubscriptions', $request);
    }

    public function userGetTokens(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetTokensRestRequest $request)
    {
        return $this->userGetTokensAsync($request)->wait();
    }

    public function userGetTokensAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserGetTokensRestRequest $request)
    {
        return $this->callOperationAsync('UserGetTokens', $request);
    }

    public function userCreateToken(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCreateTokenRestRequest $request)
    {
        return $this->userCreateTokenAsync($request)->wait();
    }

    public function userCreateTokenAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserCreateTokenRestRequest $request)
    {
        return $this->callOperationAsync('UserCreateToken', $request);
    }

    public function userDeleteAccessToken(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteAccessTokenRestRequest $request)
    {
        return $this->userDeleteAccessTokenAsync($request)->wait();
    }

    public function userDeleteAccessTokenAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\UserDeleteAccessTokenRestRequest $request)
    {
        return $this->callOperationAsync('UserDeleteAccessToken', $request);
    }

    public function getVersion(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetVersionRestRequest $request)
    {
        return $this->getVersionAsync($request)->wait();
    }

    public function getVersionAsync(\System\Base\Providers\BasepackagesServiceProvider\Packages\Api\Apis\Repo\Gitea\Operations\GetVersionRestRequest $request)
    {
        return $this->callOperationAsync('GetVersion', $request);
    }
}