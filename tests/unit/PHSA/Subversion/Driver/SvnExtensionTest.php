<?php
namespace PHSA\Subversion\Driver;

class SvnExtensionTest extends \PHPUnit_Framework_TestCase {
    private $driver;

    public function setUp() {
        $this->driver = new SvnExtension();
    }

    public function tearDown() {
        $this->driver = null;
    }

    public function testValidRepository() {
        $this->assertFalse($this->driver->validRepository('/some/random/dir'));
        $this->assertTrue($this->driver->validRepository(TESTS_DATA_DIR . '/reposes/test1'));
    }
}
