<?php
namespace PHSA\Database;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase {
    private $queryBuilder;
    private $driver;

    public function setUp() {
        $this->driver = $this->getMock('PHSA\Database\DriverInterface');
        $this->queryBuilder = new QueryBuilder($this->driver);
    }

    public function tearDown() {
        $this->driver = null;
        $this->queryBuilder = null;
    }

    public function testQueryBuilder() {
        $ruleset = $this->getMock('PHSA\Acl\Ruleset');

        $this->driver->expects($this->once())->method('getAcls')->with(
            array('repos1'), array('christer'), array('vgdev'), 'user', 'allow'
        )->will($this->returnValue($ruleset));

        $result = $this->queryBuilder->setRepositories(array('repos1'))
                                     ->setUsers(array('christer'))
                                     ->setGroups(array('vgdev'))
                                     ->setRole(DriverInterface::ROLE_USER)
                                     ->setRule(DriverInterface::RULE_ALLOW)
                                     ->getAcls();

        $this->assertSame($result, $ruleset);
    }
}
