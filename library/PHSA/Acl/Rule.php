<?php
namespace PHSA\Acl;

/**
 * A single rule
 */
class Rule {
    /**#@+
     * Rule
     *
     * @var string
     */
    const ALLOW = 'allow';
    const DENY  = 'deny';
    /**#@-*/

    /**
     * Name of the user
     *
     * @var string
     */
    private $user;

    /**
     * Name of the group
     *
     * @var string
     */
    private $group;

    /**
     * Name of the repos
     *
     * @var string
     */
    private $repos;

    /**
     * Path the rule affects
     *
     * @var string
     */
    private $path;

    /**
     * The rule itself. "allow" or "deny"
     *
     * @var string
     */
    private $rule;

    /**
     * Get the user
     *
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set the user
     *
     * @param string $user The user to set
     *
     * @return PHSA\Acl\Rule
     */
    public function setUser($user) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the group
     *
     * @return string
     */
    public function getGroup() {
        return $this->group;
    }

    /**
     * Set the group
     *
     * @param string $group The group to set
     *
     * @return PHSA\Acl\Rule
     */
    public function setGroup($group) {
        $this->group = $group;

        return $this;
    }

    /**
     * Get the repos
     *
     * @return string
     */
    public function getRepos() {
        return $this->repos;
    }

    /**
     * Set the repos
     *
     * @param string $repos The repos to set
     *
     * @return PHSA\Acl\Rule
     */
    public function setRepos($repos) {
        $this->repos = $repos;

        return $this;
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set the path
     *
     * @param string $path The path to set
     *
     * @return PHSA\Acl\Rule
     */
    public function setPath($path) {
        $this->path = '/' . ltrim($path, '/');

        return $this;
    }

    public function getRule() {
        return $this->rule;
    }

    /**
     * Set the rule
     *
     * @param string $rule The rule to set
     *
     * @return PHSA\Acl\Rule
     */
    public function setRule($rule) {
        $this->rule = $rule;

        return $this;
    }

    /**
     * See if the rule is a user rule
     *
     * @return boolean
     */
    public function isUserRule() {
        return empty($this->group) && !empty($this->user);
    }

    /**
     * See if the rule is a group rule
     *
     * @return boolean
     */
    public function isGroupRule() {
        return empty($this->user) && !empty($this->group);
    }

    /**
     * Check if the rule allows access
     *
     * @return boolean
     */
    public function ruleAllows() {
        return $this->rule === self::ALLOW;
    }

    /**
     * Check if the rule denies access
     *
     * @return boolean
     */
    public function ruleDenies() {
        return $this->rule !== self::ALLOW;
    }
}
