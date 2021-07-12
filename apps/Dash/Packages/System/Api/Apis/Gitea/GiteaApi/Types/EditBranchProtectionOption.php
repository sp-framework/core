<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\GiteaApi\Types;

use Apps\Dash\Packages\System\Api\Base\Types\BaseType;

class EditBranchProtectionOption extends BaseType
{
    private static $propertyTypes = [
        'approvals_whitelist_teams' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'approvals_whitelist_teams',
        ],
        'approvals_whitelist_username' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'approvals_whitelist_username',
        ],
        'block_on_official_review_requests' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'block_on_official_review_requests',
        ],
        'block_on_outdated_branch' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'block_on_outdated_branch',
        ],
        'block_on_rejected_reviews' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'block_on_rejected_reviews',
        ],
        'dismiss_stale_approvals' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'dismiss_stale_approvals',
        ],
        'enable_approvals_whitelist' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enable_approvals_whitelist',
        ],
        'enable_merge_whitelist' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enable_merge_whitelist',
        ],
        'enable_push' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enable_push',
        ],
        'enable_push_whitelist' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enable_push_whitelist',
        ],
        'enable_status_check' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'enable_status_check',
        ],
        'merge_whitelist_teams' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'merge_whitelist_teams',
        ],
        'merge_whitelist_usernames' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'merge_whitelist_usernames',
        ],
        'protected_file_patterns' => [
          'type' =>       'string',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'protected_file_patterns',
        ],
        'push_whitelist_deploy_keys' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'push_whitelist_deploy_keys',
        ],
        'push_whitelist_teams' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'push_whitelist_teams',
        ],
        'push_whitelist_usernames' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'push_whitelist_usernames',
        ],
        'require_signed_commits' => [
          'type' => 'boolean',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'require_signed_commits',
        ],
        'required_approvals' => [
          'type' => 'integer',
          'repeatable' => false,
          'attribute' => false,
          'elementName' => 'required_approvals',
        ],
        'status_check_contexts' => [
          'type' =>       'string',
          'repeatable' => true,
          'attribute' => false,
          'elementName' => 'status_check_contexts',
        ],
      ];

    public function __construct(array $values = [])
    {
        list($parentValues, $childValues) = self::getParentValues(self::$propertyTypes, $values);

        parent::__construct($parentValues);

        if (!array_key_exists(__CLASS__, self::$properties)) {
            self::$properties[__CLASS__] = array_merge(self::$properties[get_parent_class()], self::$propertyTypes);
        }

        $this->setValues(__CLASS__, $childValues);
    }
}