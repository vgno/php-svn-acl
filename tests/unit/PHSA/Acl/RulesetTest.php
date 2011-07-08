<?php
namespace PHSA\Acl;

class RulesetTest extends \PHPUnit_Framework_TestCase {
    private $ruleset;

    public function setUp() {
        $this->ruleset = new Ruleset();
    }

    public function tearDown() {
        $this->ruleset = null;
    }

    public function testAddRule() {
        $this->assertSame(0, $this->ruleset->count());
        $this->ruleset->addRule($this->getMock('PHSA\Acl\Rule'));
        $this->assertSame(1, $this->ruleset->count());
    }

    public function testCountable() {
        $this->assertSame(0, count($this->ruleset));
        $this->ruleset->addRule($this->getMock('PHSA\Acl\Rule'));
        $this->assertSame(1, count($this->ruleset));
    }

    public function testIterator() {
        $this->ruleset->addRule($this->getMock('PHSA\Acl\Rule'));
        $this->ruleset->addRule($this->getMock('PHSA\Acl\Rule'));
        $num = 0;

        foreach ($this->ruleset as $idx => $rule) {
            $num++;
            $this->assertInternalType('integer', $idx);
            $this->assertInstanceOf('PHSA\Acl\Rule', $rule);
        }

        $this->assertSame(2, $num);
    }
}
