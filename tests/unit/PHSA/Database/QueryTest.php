<?php
namespace PHSA\Database;

use PHSA\Acl\Rule;

class QueryTest extends \PHPUnit_Framework_TestCase {
    private $query;

    public function setUp() {
        $this->query = new Query();
    }

    public function tearDown() {
        $this->query = null;
    }

    public function testSetGetRepositories() {
        $reposes = array('a', 'b');
        $this->assertSame($this->query, $this->query->setRepositories($reposes));
        $this->assertSame($reposes, $this->query->getRepositories());
    }

    public function testSetGetGroups() {
        $groups = array('a', 'b');
        $this->assertSame($this->query, $this->query->setGroups($groups));
        $this->assertSame($groups, $this->query->getGroups());
    }

    public function testSetGetUsers() {
        $users = array('a', 'b');
        $this->assertSame($this->query, $this->query->setUsers($users));
        $this->assertSame($users, $this->query->getUsers());
    }

    public function testSetGetRole() {
        $role = 'user';
        $this->assertSame($this->query, $this->query->setRole($role));
        $this->assertSame($role, $this->query->getRole());
    }

    public function testSetGetRule() {
        $rule = 'allow';
        $this->assertSame($this->query, $this->query->setRule($rule));
        $this->assertSame($rule, $this->query->getRule());
    }

    public function testGetRules() {
        $ruleset = $this->getMock('PHSA\Acl\Ruleset');

        $driver = $this->getMock('PHSA\Database\DriverInterface');
        $driver->expects($this->once())->method('getRules')->with($this->query)->will($this->returnValue($ruleset));

        $result = $this->query->setRepositories(array('repos1'))
                              ->setUsers(array('christer'))
                              ->setGroups(array('vgdev'))
                              ->setRole(Rule::USER)
                              ->setRule(Rule::ALLOW)
                              ->getRules($driver);

        $this->assertSame($result, $ruleset);
    }

    public function testRemoveRules() {
        $ruleset = $this->getMock('PHSA\Acl\Ruleset');

        $driver = $this->getMock('PHSA\Database\DriverInterface');
        $driver->expects($this->once())->method('removeRules')->with($this->query)->will($this->returnValue($ruleset));

        $result = $this->query->setRepositories(array('repos1'))
                              ->setUsers(array('christer'))
                              ->setGroups(array('vgdev'))
                              ->setRole(Rule::USER)
                              ->setRule(Rule::ALLOW)
                              ->removeRules($driver);

        $this->assertSame($result, $ruleset);
    }
}
