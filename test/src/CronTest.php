<?php
namespace Test\Src;

use Schedule\Cron;
use PHPUnit_Framework_TestCase;

class CronTest extends PHPUnit_Framework_TestCase
{
    public function testMinute()
    {
        $this->assertEquals('*/1 * * * *', Cron::everyMinutes(1)->parse());
        $this->assertEquals(
            Cron::everyHours(1)->parse(),
            Cron::everyMinutes(60)->parse()
        );
    }

    public function testHour()
    {
        $this->assertEquals('0 */1 * * *', Cron::everyHours(1)->parse());
        $this->assertEquals(
            Cron::everyHours(24)->parse(),
            Cron::everyDays(1)->parse()
        );
        $this->assertEquals(
            '10 */4 * * *',
            Cron::everyHours(4)->minutes(10)->parse()
        );
        $this->assertEquals(
            '10,20 */4 * * *',
            Cron::everyHours(4)->minutes(array(10, 20))->parse()
        );

        $this->assertEquals(
            Cron::everyHours(24)->parse(),
            Cron::everyDays(1)->parse()
        );
    }

    public function testDay()
    {
        $this->assertEquals('0 0 */1 * *', Cron::everyDays(1)->parse());
        $this->assertEquals(
            '0 10,12 */1 * *',
            Cron::everyDays(1)->hours(array(10, 12))->parse()
        );
        $this->assertEquals(
            '30,40,50 20 */1 * *',
            Cron::everyDays(1)->hours(20)->minutes(array(30, 40, 50))->parse()
        );
    }
}