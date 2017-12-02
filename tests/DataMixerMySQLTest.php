<?php
namespace DataMixer\Tests;

use DataMixer\DataMixer;
use PHPUnit\Framework\TestCase;

class DataMixerMySQLTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->dataMixer = new DataMixer('mysql:dbname=test;host=localhost', 'root', '123');
        $sql = file_get_contents(dirname(__DIR__) . '/tests/data.sql');
        $this->dataMixer->pdo->exec($sql);
    }

    public function tearDown()
    {
        $this->dataMixer->pdo->query('DROP table users');
        parent::tearDown();
    }

    public function testGetRows()
    {
        $actual = $this->dataMixer->getRows('users');
        $this->assertEquals(['first_name' => 'Anna', 'last_name' => 'Algol', 'sex' => 'F'], $actual[1]);
    }

    public function testUpdateRows()
    {
        $mixed = $this->dataMixer->getMixed('users', ['first_name', 'last_name']);
        $actual = $this->dataMixer->updateRows($mixed);
        $this->assertEquals(5, $actual);
    }

    public function testGetSimpleMixed()
    {
        $actual = $this->dataMixer->getMixed('users', ['first_name', 'last_name']);
        $this->assertNotEquals(
            ['id' => 1, 'first_name' => 'Anna', 'last_name' => 'Algol', 'sex' => 'F'],
            $actual['users'][1]
        );
    }
}
