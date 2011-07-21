<?php
namespace PHSA\Acl;

/**
 * A single rule
 */
class Rule {
    /**#@+
     * Roles
     *
     * @var string
     */
    const USER  = 'user';
    const GROUP = 'group';
    /**#@-*/

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
    public $user;

    /**
     * Name of the group
     *
     * @var string
     */
    public $group;

    /**
     * Name of the repos
     *
     * @var string
     */
    public $repos;

    /**
     * Path the rule affects
     *
     * @var string
     */
    public $path;

    /**
     * The rule itself. "allow" or "deny"
     *
     * @var string
     */
    public $rule;

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
}
