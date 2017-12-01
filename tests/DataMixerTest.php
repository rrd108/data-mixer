<?php
namespace DataMixer\Tests;

use DataMixer\DataMixer;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DataMixerTest extends TestCase
{
    public function testConstruct()
    {
        $this->expectException(InvalidArgumentException::class);
        new DataMixer(
            [
                'host' => 'localhost',
                'dbUser' => 'user',
                'dbPassword' => 'supersecret',
                'database' => 'test'
            ]
        );
    }
}
