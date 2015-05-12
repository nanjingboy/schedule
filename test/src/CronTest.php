<?php
namespace Test\Src;

use Schedule\Cron;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class CronTest extends PHPUnit_Framework_TestCase
{
    public function testEvery()
    {
        $this->assertEquals('* * * * *', Cron::every()->parse());
        $this->assertEquals('1 * * * *', Cron::every()->minutes(1)->parse());
        $this->assertEquals('1 0 * * *', Cron::every()->minutes(1)->hours(0)->parse());
        $this->assertEquals(
            '1 0 1 * *',
            Cron::every()->minutes(1)->hours(0)->daysOfTheMonth(1)->parse()
        );
    }

    public function testEveryMinutes()
    {
        $this->assertEquals('*/1 * * * *', Cron::everyMinutes(1)->parse());
        $this->assertEquals(
            Cron::everyHours(1)->parse(),
            Cron::everyMinutes(60)->parse()
        );
    }

    public function testEveryHours()
    {
        $this->assertEquals('0 */1 * * *', Cron::everyHours(1)->parse());
        $this->assertEquals(
            Cron::everyHours(24)->parse(),
            Cron::everyDays(1)->parse()
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

    public function testEveryDays()
    {
        $this->assertEquals('0 0 */1 * *', Cron::everyDays(1)->parse());
        $this->assertEquals(
            '30,40,50 12,20 */1 * *',
            Cron::everyDays(1)->hours(array(12, 20))->minutes(array(30, 40, 50))->parse()
        );

        $this->assertEquals(
            Cron::everyDays(31)->parse(),
            Cron::everyMonths(1)->parse()
        );
    }

    public function testEveryMonths()
    {
        $this->assertEquals('0 0 1 */1 *', Cron::everyMonths(1)->parse());
        $this->assertEquals(
            '1 10 12,13 */1 *',
            Cron::everyMonths(1)->daysOfTheMonth(array(12, 13))->hours(10)->minutes(1)->parse()
        );

        try {
            Cron::everyMonths(13)->parse();
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Time must be lower or equal 12 months', $e->getMessage());
        }
    }

    public function testEveryWeek()
    {
        $this->assertEquals('0 0 * * 1', Cron::everyWeek()->parse());
        $this->assertEquals(
            '1 10 * 10 1',
            Cron::everyWeek()->months(10)->hours(10)->minutes(1)->parse()
        );
    }

    public function testEveryWeekday()
    {
        $this->assertEquals('0 0 * * 1,2,3,4,5', Cron::everyWeekday()->parse());
        $this->assertEquals(
            '1 10 * 10 1,2,3,4,5',
            Cron::everyWeekday()->months(10)->hours(10)->minutes(1)->parse()
        );
    }

    public function testEveryWeekend()
    {
        $this->assertEquals('0 0 * * 0,6', Cron::everyWeekend()->parse());
        $this->assertEquals(
            '1 10 * 10 0,6',
            Cron::everyWeekend()->months(10)->hours(10)->minutes(1)->parse()
        );
    }

    public function testCommand()
    {
        $this->assertEquals(
            '*/1 * * * * ENV=test HOME=/Users/tom balabalabala',
            Cron::everyMinutes(1)->command('balabalabala')->parse()
        );
    }

    public function testFile()
    {
        $this->assertEquals(
            '0 */1 * * * ENV=test HOME=/Users/tom php command.php',
            Cron::everyHours(1)->file('command.php')->parse()
        );
    }
}