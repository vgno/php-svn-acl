<?php
namespace PHSA\Database\Driver;

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
        // Truncate table
        $this->driver->getDb()->query('TRUNCATE TABLE rules');

        // Reset the static pdo property
        $property = new \ReflectionProperty('PHSA\Database\Driver\MySQL', 'pdo');
        $property->setAccessible(true);
        $property->setValue($this->driver, null);

        // Destroy the driver
        $this->driver = null;
    }

    public function testGetAcls() {
        $result = $this->driver->getAcls();
        $this->assertSame(6, count($result));

        $result = $this->driver->getAcls(array('repos1'));
        $this->assertSame(3, count($result));

        $result = $this->driver->getAcls(array(), array('christer', 'andrer'));
        $this->assertSame(2, count($result));

        $result = $this->driver->getAcls(array(), array(), array('vgdev', 'vgmobil'));
        $this->assertSame(3, count($result));

        $result = $this->driver->getAcls(array(), array(), array(), DriverInterface::ROLE_USER);
        $this->assertSame(3, count($result));

        $result = $this->driver->getAcls(array(), array(), array(), DriverInterface::ROLE_GROUP);
        $this->assertSame(3, count($result));

        $result = $this->driver->getAcls(array(), array(), array(), null, DriverInterface::RULE_ALLOW);
        $this->assertSame(4, count($result));

        $result = $this->driver->getAcls(array(), array(), array(), null, DriverInterface::RULE_DENY);
        $this->assertSame(2, count($result));
    }
}
