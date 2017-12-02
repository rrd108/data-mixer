<?php
namespace DataMixer\Tests;

use DataMixer\DataMixer;
use PHPUnit\Framework\TestCase;

class DataMixerTest extends TestCase
{
    public function setUp()
    {
        $this->users = [
            1 => ['id' => 1, 'first_name' => 'Anna', 'last_name' => 'Algol', 'sex' => 'F'],
            2 => ['id' => 2, 'first_name' => 'Bella', 'last_name' => 'Basic', 'sex' => null],
            3 => ['id' => 3, 'first_name' => 'Cintia', 'last_name' => 'Csarph', 'sex' => 'F'],
            4 => ['id' => 4, 'first_name' => 'Daniel', 'last_name' => 'Dart', 'sex' => 'M'],
            6 => ['id' => 6, 'first_name' => 'Elen', 'last_name' => 'Ecmascript', 'sex' => 'F']
        ];

        $this->dataMixer = $this->getMockBuilder(DataMixer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRows'])
            ->getMock();

        $this->dataMixer->expects($this->any())
            ->method('getRows')
            ->willReturn($this->users);
    }

    public function testSimpleMix()
    {
        $actual = $this->dataMixer->getMixed('users', ['first_name', 'last_name']);
        $this->assertNotEquals(
            ['id' => 1, 'first_name' => 'Anna', 'last_name' => 'Algol', 'sex' => 'F'],
            $actual['users'][1]
        );
    }

    public function testDependentMix()
    {
        $actual = $this->dataMixer->getMixed('users', ['first_name' => 'sex', 'last_name']);
        $this->assertNotEquals(
            ['id' => 1, 'first_name' => 'Anna', 'last_name' => 'Algol', 'sex' => 'F'],
            $actual['users'][1]
        );
        //Bella and Daniel are unique on sex so they should not be mixed
        $this->assertEquals('Bella', $actual['users'][2]['first_name']);
        $this->assertEquals('Daniel', $actual['users'][4]['first_name']);
    }
}
