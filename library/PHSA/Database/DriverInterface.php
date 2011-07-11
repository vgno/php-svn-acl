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

    /**
     * Allow a user access to a repository
     *
     * @param string $user Username to allow access
     * @param string $repository Repository to allow access to
     * @param string $path Optional path in the repository
     *
     * @return boolean True on success or false otherwise
     */
    function allowUser($user, $repository, $path = null);

    /**
     * Deny a user access to a repository
     *
     * @param string $user Username to deny access
     * @param string $repository Repository to deny access to
     * @param string $path Optional path in the repository
     *
     * @return boolean True on success or false otherwise
     */
    function denyUser($user, $repository, $path = null);

    /**
     * Allow a group access to a repository
     *
     * @param string $group Name of group to allow access
     * @param string $repository Repository to allow access to
     * @param string $path Optional path in the repository
     *
     * @return boolean True on success or false otherwise
     */
    function allowGroup($group, $repository, $path = null);

    /**
     * Deny a group access to a repository
     *
     * @param string $group Name of group to deny access
     * @param string $repository Repository to deny access to
     * @param string $path Optional path in the repository
     *
     * @return boolean True on success or false otherwise
     */
    function denyGroup($group, $repository, $path = null);

    /**
     * Remove all rules from the database
     *
     * @return boolean True on success or false otherwise
     */
    function removeRules();
}
