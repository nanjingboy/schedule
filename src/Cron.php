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
    const YEAR_SECONDS = 31557600;

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

    private $_seconds = 0;
    private $_minutes = 0;
    private $_hours = 0;
    private $_daysOfTheMonth = 0;
    private $_months = 0;
    private $_daysOfTheWeek = 0;

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

    public function __construct($seconds)
    {
        $this->_seconds = intval($seconds);
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
        $seconds = $this->_seconds;
        if ($seconds < static::MINUTE_SECONDS) {
            throw new InvalidArgumentException('Time must be in minutes or higher');
        }

        if ($seconds >= static::MINUTE_SECONDS && $seconds < static::HOUR_SECONDS) {
            $sections[0] = '*/' . floor($seconds / static::MINUTE_SECONDS);
        } else if ($seconds >= static::HOUR_SECONDS && $seconds < static::DAY_SECONDS) {
            $sections[0] = $this->_minutes;
            $sections[1] = '*/' . floor($seconds / static::HOUR_SECONDS);
        } else if ($seconds >= static::DAY_SECONDS && $seconds < static::MONTH_SECONDS) {
            $sections[0] = $this->_minutes;
            $sections[1] = $this->_hours;
            $sections[2] = '*/' . floor($seconds / static::DAY_SECONDS);
        }

        return implode(' ', $sections);
    }
}