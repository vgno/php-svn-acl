<?php
namespace PHSA;

class VersionTest extends \PHPUnit_Framework_TestCase {
    public function testVersion() {
        $number = Version::getVersionNumber();
        $string = Version::getVersionString();
        $this->assertSame('dev', $number);
        $this->assertContains($number, $string);
    }

    public function testGetNumberWhenNotInDevMode() {
        $property = new \ReflectionProperty('PHSA\Version', 'id');
        $property->setAccessible(true);
        $property->setValue('1.0');

        $number = Version::getVersionNumber();
        $string = Version::getVersionString();
        $this->assertSame('1.0', $number);
        $this->assertContains($number, $string);
    }
}
