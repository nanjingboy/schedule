<?php
namespace Test\Src;

use Schedule\Cron;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class CronTest extends PHPUnit_Framework_TestCase
{
    public function testMinute()
    {
        $this->assertEquals('* * * * *', Cron::minute(1));
        $this->assertEquals('0,5,10,15,20,25,30,35,40,45,50,55 * * * *', Cron::minute(5));
        $this->assertEquals('7,14,21,28,35,42,49,56 * * * *', Cron::minute(7));
        $this->assertEquals('0,30 * * * *', Cron::minute(30));
        $this->assertEquals('32 * * * *', Cron::minute(32));

        try {
            Cron::minute(60);
        } catch(InvalidArgumentException $expected) {
            $this->assertEquals('Minutes must between 1 and 59', $expected->getMessage());
        }
    }
}