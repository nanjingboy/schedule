<?php
namespace Schedule;

use InvalidArgumentException;

class Cron
{
    const MINUTE_SECONDS = 60;
    const HOUR_SECONDS = 3600;
    const DAY_SECONDS = 86400;
    const WEEK_SECONDS = 604800;
    const MONTH_SECONDS = 2592000;
    const YEAR_SECONDS = 31104000;

    private static $_ranges = array(
        'minutes' => array(
            'min' => 0,
            'max' => 59,
            'error' => 'Minute must between 0 and 59'
        ),
        'hours' => array(
            'min' => 0,
            'max' => 23,
            'error' => 'Hour must between 0 and 23'
        ),
        'daysOfTheMonth' => array(
            'min' => 1,
            'max' => 31,
            'error' => 'Day of the month must between 1 and 31'
        ),
        'months' => array(
            'min' => 1,
            'max' => 12,
            'error' => 'Month must between 1 and 12'
        ),
        'daysOfTheWeek' => array(
            'min' => 0,
            'max' => 7,
            'error' => 'Day of the week must between 0 and 7'
        )
    );

    private $_type = null;
    private $_minutes = '*';
    private $_hours = '*';
    private $_daysOfTheMonth = '*';
    private $_months = '*';
    private $_daysOfTheWeek = '*';

    public static function everyMinutes($minutes = 1)
    {
        return new Static(intval($minutes) * static::MINUTE_SECONDS);
    }

    public static function everyHours($hours = 1)
    {
        return new static(intval($hours) * static::HOUR_SECONDS);
    }

    public static function everyDays($days = 1)
    {
        return new static(intval($days) * static::DAY_SECONDS);
    }

    public static function everyMonths($months = 1)
    {
        return new static(intval($months) * static::MONTH_SECONDS);
    }

    public function __construct($seconds = null)
    {
        if ($seconds !== null) {
            $seconds = intval($seconds);
            if ($seconds < static::MINUTE_SECONDS) {
                throw new InvalidArgumentException('Time must be in minutes or higher');
            } else if ($seconds > static::YEAR_SECONDS) {
                throw new InvalidArgumentException('Time must be lower or equal 12 months');
            }

            if ($seconds >= static::MINUTE_SECONDS && $seconds < static::HOUR_SECONDS) {
                $this->_minutes = '*/' . floor($seconds / static::MINUTE_SECONDS);
                $this->_type = 'EVERY_MINUTE';
            } else if ($seconds >= static::HOUR_SECONDS && $seconds < static::DAY_SECONDS) {
                $this->_minutes = 0;
                $this->_hours = '*/' . floor($seconds / static::HOUR_SECONDS);
                $this->_type = 'EVERY_HOUR';
            } else if ($seconds >= static::DAY_SECONDS && $seconds < static::MONTH_SECONDS) {
                $this->_minutes = 0;
                $this->_hours = 0;
                $this->_daysOfTheMonth = '*/' . floor($seconds / static::DAY_SECONDS);
                $this->_type = 'EVERY_DAY';
            } else if ($seconds >= static::MONTH_SECONDS && $seconds <= static::YEAR_SECONDS) {
                $this->_minutes = 0;
                $this->_hours = 0;
                $this->_daysOfTheMonth = 1;
                $this->_months = '*/' . floor($seconds / static::MONTH_SECONDS);
                $this->_daysOfTheWeek = '*';
                $this->_type = 'EVERY_MONTH';
            }
        }
    }

    public function __call($method, $arguments)
    {
        if (array_key_exists($method, self::$_ranges) === false) {
            throw new UndefinedMethodException($method, __class__);
        }

        if (count($arguments) > 0) {
            if (is_array($arguments[0])) {
                $times = $arguments[0];
            } else {
                $times = array($arguments[0]);
            }
        } else if (in_array($method, array('daysOfTheMonth', 'months'))) {
            $times = array(1);
        } else {
            $times = array(0);
        }

        $range = self::$_ranges[$method];
        $times = array_unique(
            array_map(
                function($time) use($range) {
                    $time = intval($time);
                    if ($time < $range['min'] || $time > $range['max']) {
                        throw new InvalidArgumentException($range['error']);
                    }
                    return $time;
                },
                $times
            )
        );
        $attribute = "_{$method}";
        $this->$attribute = implode(',', $times);
        return $this;
    }

    public function parse()
    {
        $sections = array_fill(0, 5, '*');
        switch ($this->_type) {
            case 'EVERY_MINUTE':
                $sections[0] = $this->_minutes;
                break;
            case 'EVERY_HOUR':
                $sections[0] = $this->_minutes;
                $sections[1] = $this->_hours;
                break;
            case 'EVERY_DAY':
                $sections[0] = $this->_minutes;
                $sections[1] = $this->_hours;
                $sections[2] = $this->_daysOfTheMonth;
                break;
            case 'EVERY_MONTH':
                $sections[0] = $this->_minutes;
                $sections[1] = $this->_hours;
                $sections[2] = $this->_daysOfTheMonth;
                $sections[3] = $this->_months;
                $sections[4] = $this->_daysOfTheWeek;
                break;
            default:
                break;
        }

        return implode(' ', $sections);
    }
}