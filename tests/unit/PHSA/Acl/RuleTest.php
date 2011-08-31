<?php
namespace PHSA\Acl;

class RuleTest extends \PHPUnit_Framework_TestCase {
    private $rule;

    public function setUp() {
        $this->rule = new Rule();
    }

    public function tearDown() {
        $this->rule = null;
    }

    public function testSetGetUser() {
        $value = 'christer';
        $this->assertSame($this->rule, $this->rule->setUser($value));
        $this->assertSame($value, $this->rule->getUser());
    }

    public function testSetGetGroup() {
        $value = 'dev';
        $this->assertSame($this->rule, $this->rule->setGroup($value));
        $this->assertSame($value, $this->rule->getGroup());
    }

    public function testSetGetRepos() {
        $value = 'somerepos';
        $this->assertSame($this->rule, $this->rule->setRepos($value));
        $this->assertSame($value, $this->rule->getRepos());
    }

    public function testSetGetPathWithoutLeadingSlash() {
        $value = 'path';
        $this->assertSame($this->rule, $this->rule->setPath($value));
        $this->assertSame('/' . $value, $this->rule->getPath());
    }

    public function testSetGetPathWithLeadingSlash() {
        $value = '/path';
        $this->assertSame($this->rule, $this->rule->setPath($value));
        $this->assertSame($value, $this->rule->getPath());
    }

    public function testSetGetRule() {
        $value = Rule::ALLOW;
        $this->assertSame($this->rule, $this->rule->setRule($value));
        $this->assertSame($value, $this->rule->getRule());
    }

    public function testIsUserRule() {
        $this->rule->setUser('someuser');
        $this->assertTrue($this->rule->isUserRule());
        $this->assertFalse($this->rule->isGroupRule());
    }

    public function testIsGroupRule() {
        $this->rule->setGroup('somegroup');
        $this->assertTrue($this->rule->isGroupRule());
        $this->assertFalse($this->rule->isUserRule());
    }

    public function testRuleAllows() {
        $this->assertFalse($this->rule->ruleAllows());
        $this->rule->setRule(Rule::ALLOW);
        $this->assertTrue($this->rule->ruleAllows());
    }

    public function testRuleDenies() {
        $this->assertTrue($this->rule->ruleDenies());
        $this->rule->setRule(Rule::ALLOW);
        $this->assertFalse($this->rule->ruleDenies());
    }
}
