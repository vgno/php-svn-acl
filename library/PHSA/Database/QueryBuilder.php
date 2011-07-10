<?php
namespace PHSA\Database;

/**
 * Query builder for the database drivers
 */
class QueryBuilder {
    /**
     * Driver to use
     *
     * @var PHSA\Database\DriverInterface
     */
    private $driver;

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
     * Class constructor
     *
     * @param PHSA\Database\DriverInterface $driver
     */
    public function __construct(DriverInterface $driver) {
        $this->driver = $driver;
    }

    /**
     * Set the repositories to fetch from
     *
     * @var string[]
     * @return PHSA\Database\QueryBuilder
     */
    public function setRepositories(array $repositories) {
        $this->repositories = $repositories;

        return $this;
    }

    /**
     * Set the users to fetch from
     *
     * @var string[]
     * @return PHSA\Database\QueryBuilder
     */
    public function setUsers(array $users) {
        $this->users = $users;

        return $this;
    }

    /**
     * Set the groups to fetch from
     *
     * @var string[]
     * @return PHSA\Database\QueryBuilder
     */
    public function setGroups(array $groups) {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Set the role to fetch
     *
     * @var string
     * @return PHSA\Database\QueryBuilder
     */
    public function setRole($role) {
        $this->role = $role;

        return $this;
    }

    /**
     * Set the rule to fetch
     *
     * @var string
     * @return PHSA\Database\QueryBuilder
     */
    public function setRule($rule) {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Fetch ACLs from the driver
     *
     * @return PHSA\Acl\Ruleset
     */
    public function getAcls() {
        return $this->driver->getAcls($this->repositories, $this->users, $this->groups, $this->role, $this->rule);
    }
}
