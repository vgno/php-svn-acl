<?php
namespace PHSA\Database;

/**
 * Query builder for the database drivers
 */
class Query {
    /**
     * Repositories to fetch
     *
     * @var string[]
     */
    private $repositories = array();

    /**
     * Users to fetch
     *
     * @var string[]
     */
    private $users = array();

    /**
     * Groups to fetch
     *
     * @var string[]
     */
    private $groups = array();

    /**
     * Roles to fetch ('user' or 'group')
     *
     * @var string
     */
    private $role;

    /**
     * Rules to fetch ('allow' or 'deny')
     *
     * @var string
     */
    private $rule;

    /**
     * Set the repositories to fetch from
     *
     * @var string[]
     * @return PHSA\Database\Query
     */
    public function setRepositories(array $repositories) {
        $this->repositories = $repositories;

        return $this;
    }

    /**
     * Get the repositories
     *
     * @return string[]
     */
    public function getRepositories() {
        return $this->repositories;
    }

    /**
     * Set the users to fetch from
     *
     * @var string[]
     * @return PHSA\Database\Query
     */
    public function setUsers(array $users) {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return string[]
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * Set the groups to fetch from
     *
     * @var string[]
     * @return PHSA\Database\Query
     */
    public function setGroups(array $groups) {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups
     *
     * @var string[]
     */
    public function getGroups() {
        return $this->groups;
    }

    /**
     * Set the role to fetch
     *
     * @var string
     * @return PHSA\Database\Query
     */
    public function setRole($role) {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the role
     *
     * @return string
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Set the rule to fetch
     *
     * @var string
     * @return PHSA\Database\Query
     */
    public function setRule($rule) {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get the rule
     *
     * @return string
     */
    public function getRule() {
        return $this->rule;
    }

    /**
     * Fetch rules using a driver
     *
     * @param PHSA\Database\DriverInterface $driver
     *
     * @return PHSA\Acl\Ruleset
     */
    public function getRules(DriverInterface $driver) {
        return $driver->getRules($this);
    }

    /**
     * Remove rules using a driver
     *
     * @param PHSA\Database\DriverInterface $driver
     *
     * @return boolean
     */
    public function removeRules(DriverInterface $driver) {
        return $driver->removeRules($this);
    }
}
