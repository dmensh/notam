<?php

namespace Rocket\Task\Tests\Service;

use PHPUnit_Framework_TestCase;
use Rocket\Task\Helpers;

class HelpersTest extends PHPUnit_Framework_TestCase
{
    public function testDecodeCoordinate()
    {
        list($lat, $lng) = Helpers::decodeCoordinate('5024N03027E');
        $this->assertEquals(50.40, $lat);
        $this->assertEquals(30.45, $lng);
    }

}