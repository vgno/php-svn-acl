<?php
namespace PHSA\Database\Driver;

use PHSA\Database\Query;
use PHSA\Database\DriverInterface;

class MySQLIntegrationTest extends \PHPUnit_Framework_TestCase {
    /**
     * Driver
     *
     * @var PHSA\Database\Driver\MySQL
     */
    private $driver;

    public function setUp() {
        if (!defined('ENABLE_MYSQL_TESTS') || !ENABLE_MYSQL_TESTS) {
            $this->markTestSkipped('Set ENABLE_MYSQL_TESTS to true to enable these tests');
        }

        $params = array(
            'hostname' => PHSA_MYSQL_HOSTNAME,
            'username' => PHSA_MYSQL_USERNAME,
            'password' => PHSA_MYSQL_PASSWORD,
            'database' => PHSA_MYSQL_DATABASE,
        );
        $this->driver = new MySQL($params);
        $this->driver->getDb()->query('TRUNCATE TABLE rules');
        $this->driver->getDb()->query("INSERT INTO rules (username, groupname, repository, path, rule) VALUES
            ('christer', NULL,       'repos1', NULL,        'allow'),
            ('andrer',   NULL,       'repos1', 'some/path', 'deny'),
            ('ay',       NULL,       'repos2', 'file',      'allow'),
            (NULL,       'vgdev',    'repos1', NULL,        'allow'),
            (NULL,       'vgmobil',  'repos2', NULL,        'allow'),
            (NULL,       'vgmobil',  'repos',  NULL,        'deny')
        ");
    }

    public function tearDown() {
        if (defined('ENABLE_MYSQL_TESTS') && ENABLE_MYSQL_TESTS) {
            // Truncate table
            $this->driver->getDb()->query('TRUNCATE TABLE rules');

            // Reset the static pdo property
            $property = new \ReflectionProperty('PHSA\Database\Driver\MySQL', 'pdo');
            $property->setAccessible(true);
            $property->setValue($this->driver, null);

            // Destroy the driver
            $this->driver = null;
        }
    }

    public function testGetAllRules() {
        $ruleset = $this->driver->getAllRules();
        $this->assertSame(6, count($ruleset));
    }

    public function testGetRulesWithQuery() {
        $query = new Query();
        $query->setUsers(array('christer', 'ay'));
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(2, count($ruleset));

        $query = new Query();
        $query->setRepositories(array('repos', 'repos1'));
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(4, count($ruleset));

        $query = new Query();
        $query->setGroups(array('foobar', 'vgmobil'));
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(2, count($ruleset));

        $query = new Query();
        $query->setRole(DriverInterface::ROLE_USER);
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(3, count($ruleset));

        $query = new Query();
        $query->setRole(DriverInterface::ROLE_GROUP);
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(3, count($ruleset));

        $query = new Query();
        $query->setRule(DriverInterface::RULE_ALLOW);
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(4, count($ruleset));

        $query = new Query();
        $query->setRule(DriverInterface::RULE_DENY);
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(2, count($ruleset));
    }

    public function testAllowUser() {
        $this->assertTrue($this->driver->allowUser('someuser', 'somerepos'));
        $query = new Query();
        $query->setUsers(array('someuser'))->setRepositories(array('somerepos'))->setRule(DriverInterface::RULE_ALLOW)->setRole(DriverInterface::ROLE_USER);
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(1, count($ruleset));
    }

    public function testDenyUser() {
        $this->assertTrue($this->driver->denyUser('someuser', 'somerepos'));
        $query = new Query();
        $query->setUsers(array('someuser'))->setRepositories(array('somerepos'))->setRule(DriverInterface::RULE_DENY)->setRole(DriverInterface::ROLE_USER);
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(1, count($ruleset));
    }

    public function testAllowGroup() {
        $this->assertTrue($this->driver->allowGroup('somegroup', 'somerepos'));
        $query = new Query();
        $query->setGroups(array('somegroup'))->setRepositories(array('somerepos'))->setRule(DriverInterface::RULE_ALLOW)->setRole(DriverInterface::ROLE_GROUP);
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(1, count($ruleset));
    }

    public function testDenyGroup() {
        $this->assertTrue($this->driver->denyGroup('somegroup', 'somerepos'));
        $query = new Query();
        $query->setGroups(array('somegroup'))->setRepositories(array('somerepos'))->setRule(DriverInterface::RULE_DENY)->setRole(DriverInterface::ROLE_GROUP);
        $ruleset = $this->driver->getRules($query);
        $this->assertSame(1, count($ruleset));
    }

    public function testRemoveRules() {
        $query = new Query();
        $query->setUsers(array('christer', 'andrer'));
        $this->assertSame(2, $this->driver->removeRules($query));
    }

    public function testRemoveAllRules() {
        $this->assertSame(6, $this->driver->removeAllRules());
    }
}
