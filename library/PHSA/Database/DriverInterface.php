<?php
namespace PHSA\Database;

/**
 * Interface for database drivers
 */
interface DriverInterface {
    /**
     * Get rules based on a query instance
     *
     * @param PHSA\Database\Query $query
     *
     * @return PHSA\Acl\Ruleset
     */
    function getRules(Query $query);

    /**
     * Get all rules from the database
     *
     * @return PHSA\Acl\Ruleset
     */
    function getAllRules();

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
     * Remove rules from the database
     *
     * @param PHSA\Database\Query $query
     *
     * @return int|boolean Returns the number of removed rules on success or false on error
     */
    function removeRules(Query $query);

    /**
     * Remove all rules from the database
     *
     * @return int|boolean Returns the number of removed rules on success or false on error
     */
    function removeAllRules();
}
