<?php
namespace PHSA\Database\Driver;

class MySQLTest extends \PHPUnit_Framework_TestCase {
    public function testGetPlaceHolderExpression() {
        $this->assertSame('(?)', MySQL::getPlaceHolderExpression(1));
        $this->assertSame('(?, ?)', MySQL::getPlaceHolderExpression(2));
        $this->assertSame('(?, ?, ?)', MySQL::getPlaceHolderExpression(3));
    }
}
