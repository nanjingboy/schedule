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
        $this->assertEquals('* * * * *', Cron::everyMinutes(1)->parse());
        foreach (Helper::range(2, 59) as $number) {
            $start = 0;
            if (intval(fmod(60, $number)) !== 0) {
                $start += $number;
            }

            $this->assertEquals(
                implode(',', Helper::range($start, 59, $number)) . ' * * * *',
                Cron::everyMinutes($number)->parse()
            );
        }

        $this->assertEquals(
            Cron::everyHours(1)->parse(),
            Cron::everyMinutes(60)->parse()
        );
    }

    public function testHour()
    {
        $this->assertEquals('0 * * * *', Cron::everyHours(1)->parse());
        foreach (Helper::range(2, 23) as $number) {
            $start = 0;
            if (intval(fmod(24, $number)) !== 0) {
                $start += $number;
            }

            $this->assertEquals(
                '0 ' . implode(',', Helper::range($start, 23, $number)) . ' * * *',
                Cron::everyHours($number)->parse()
            );
        }

        $this->assertEquals(
            '10 0,4,8,12,16,20 * * *',
            Cron::everyHours(4)->minutes(10)->parse()
        );
        $this->assertEquals(
            '10,20 0,4,8,12,16,20 * * *',
            Cron::everyHours(4)->minutes(array(10, 20))->parse()
        );
    }
}