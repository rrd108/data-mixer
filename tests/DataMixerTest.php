<?php
namespace DataMixer\Tests;

use DataMixer\DataMixer;
use PHPUnit\Framework\TestCase;

class DataMixerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->dataMixer = new DataMixer(
            [
                'dbHost' => 'localhost',
                'dbUser' => 'root',
                'dbPassword' => '123',
                'dbName' => 'test'
            ]
        );
        //TODO insert fixtures
    }

    public function testGetRows()
    {
        $actual = $this->dataMixer->getRows('users');
        $this->assertEquals(
            ['id' => 1, 'first_name' => 'Anna', 'last_name' => 'Algol', 'sex' => 'F'],
            $actual[1]
        );
    }

    public function testSimpleMix()
    {
        //TODO mock array_rand replace with array reverse
        $actual = $this->dataMixer->getMixed('users', ['first_name', 'last_name']);
        $this->assertNotEquals(
            ['id' => 1, 'first_name' => 'Anna', 'last_name' => 'Algol', 'sex' => 'F'],
            $actual['users'][1]
        );
    }

    public function testDependentMix()
    {
        //TODO mock array_rand replace with array reverse
        $actual = $this->dataMixer->getMixed('users', ['first_name'=> 'sex', 'last_name']);
        $this->assertNotEquals(
            ['id' => 1, 'first_name' => 'Anna', 'last_name' => 'Algol', 'sex' => 'F'],
            $actual['users'][1]
        );
        $this->assertEquals('Bella', $actual['users'][2]['first_name']);
        $this->assertEquals('Daniel', $actual['users'][4]['first_name']);
    }

    public function testUpdateRows()
    {
        $mixed = $this->dataMixer->getMixed('users', ['first_name'=> 'sex', 'last_name']);
        $actual = $this->dataMixer->updateRows($mixed);

        $mixed = $this->dataMixer->getMixed('users', ['first_name', 'last_name']);
        $actual = $this->dataMixer->updateRows($mixed);

    }
}
