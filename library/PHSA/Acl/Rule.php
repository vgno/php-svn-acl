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
     * ID of the rule (from the database)
     *
     * @var int
     */
    public $id;

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
}
