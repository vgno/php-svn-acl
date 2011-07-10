<?php
namespace PHSA\Database;

/**
 * Interface for database drivers
 */
interface DriverInterface {
    /**#@+
     * Roles
     *
     * @var string
     */
    const ROLE_USER  = 'user';
    const ROLE_GROUP = 'group';
    /**#@-*/

    /**#@+
     * Rules
     *
     * @var string
     */
    const RULE_ALLOW = 'allow';
    const RULE_DENY  = 'deny';
    /**#@-*/

    /**
     * Get ACLs based on the arguments
     *
     * @param string[] $repositories Only include ACLs from these repositories
     * @param string[] $users Only include ACLs from these users
     * @param string[] $groups Only include ACLs from these groups
     * @param string $role Only include ACLs in this role ("user" or "group")
     * @param string $rule Only include ACLs with this rule ("allow" or "deny")
     *
     * @return PHSA\Acl\Ruleset
     */
    function getAcls(array $repositories = array(), array $users = array(), array $groups = array(), $role = null, $rule = null);
}
