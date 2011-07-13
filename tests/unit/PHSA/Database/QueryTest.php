<?php
namespace PHSA\Database;

class QueryTest extends \PHPUnit_Framework_TestCase {
    private $query;

    public function setUp() {
        $this->query = new Query();
    }

    public function tearDown() {
        $this->query = null;
    }

    public function testQuery() {
        $ruleset = $this->getMock('PHSA\Acl\Ruleset');

        $driver = $this->getMock('PHSA\Database\DriverInterface');
        $driver->expects($this->once())->method('getRules')->with($this->query)->will($this->returnValue($ruleset));

        $result = $this->query->setRepositories(array('repos1'))
                              ->setUsers(array('christer'))
                              ->setGroups(array('vgdev'))
                              ->setRole(DriverInterface::ROLE_USER)
                              ->setRule(DriverInterface::RULE_ALLOW)
                              ->getRules($driver);

        $this->assertSame($result, $ruleset);
    }
}
