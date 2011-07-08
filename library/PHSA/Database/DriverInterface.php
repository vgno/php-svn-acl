<?php
namespace PHSA\Database;

interface DriverInterface {
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
    function getAcls(array $repositories, array $users, array $groups, $role = null, $rule = null);
}
