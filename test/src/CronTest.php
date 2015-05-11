<?php
namespace Test\Src;

use Schedule\Cron;
use Schedule\Helper;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class CronTest extends PHPUnit_Framework_TestCase
{
    public function testMinute()
    {
        $this->assertEquals('* * * * *', Cron::minute(1));
        foreach (Helper::range(2, 59) as $number) {
            $start = 0;
            if (intval(fmod(60, $number)) !== 0) {
                $start += $number;
            }

            $minutes = Helper::range($start, 59, $number);
            $this->assertEquals(
                implode(',', Helper::range($start, 59, $number)) . ' * * * *',
                Cron::minute($number)
            );
        }

        try {
            Cron::minute(60);
        } catch(InvalidArgumentException $expected) {
            $this->assertEquals('Minutes must between 1 and 59', $expected->getMessage());
        }
    }
}