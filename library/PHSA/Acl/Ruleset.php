<?php
namespace PHSA\Acl;

/**
 * A collection of rules
 */
class Ruleset implements \Iterator, \Countable {
    /**
     * Internal counter
     *
     * @var int
     */
    private $counter = 0;

    /**
     * Rules in the set
     *
     * @var PHSA\Acl\Rule[]
     */
    private $rules = array();

    /**
     * @see \Iterator::current()
     */
    public function current() {
        return $this->rules[$this->counter];
    }

    /**
     * @see \Iterator::next()
     */
    public function next() {
        $this->counter++;
    }

    /**
     * @see \Iterator::key()
     */
    public function key() {
        return $this->counter;
    }

    /**
     * @see \Iterator::valid()
     */
    public function valid() {
        return isset($this->rules[$this->counter]);
    }

    /**
     * @see \Iterator::rewind()
     */
    public function rewind() {
        $this->counter = 0;
    }

    /**
     * @see \Countable::count()
     */
    public function count() {
        return count($this->rules);
    }

    /**
     * Add a rule to the set
     *
     * @param PHSA\Acl\Rule $rule
     * @return PHSA\Acl\Ruleset
     */
    public function addRule(Rule $rule) {
        $this->rules[] = $rule;

        return $this;
    }
}
