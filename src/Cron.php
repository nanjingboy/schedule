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

    private $_options = null;

    private $_minutes = '*';
    private $_hours = '*';
    private $_daysOfTheMonth = '*';
    private $_months = '*';
    private $_daysOfTheWeek = '*';

    final private function __construct($time, $isWeekly = false)
    {
        if ($isWeekly) {
            $this->_minutes = 0;
            $this->_hours = 0;
            $this->_daysOfTheMonth = '*';
            $this->_months = '*';
            $this->_options = array(
                'type' => 'EVERY_WEEK',
                'frequency' => implode(',', $time)
            );
        } else {
            $seconds = intval($time);
            if ($seconds < static::MINUTE_SECONDS) {
                throw new InvalidArgumentException('Time must be in minutes or higher');
            } else if ($seconds > static::YEAR_SECONDS) {
                throw new InvalidArgumentException('Time must be lower or equal 12 months');
            }

            if ($seconds >= static::MINUTE_SECONDS && $seconds < static::HOUR_SECONDS) {
                $this->_options = array(
                    'type' => 'EVERY_MINUTE',
                    'frequency' => '*/' . floor($seconds / static::MINUTE_SECONDS)
                );
            } else if ($seconds >= static::HOUR_SECONDS && $seconds < static::DAY_SECONDS) {
                $this->_minutes = 0;
                $this->_options = array(
                    'type' => 'EVERY_HOUR',
                    'frequency' => '*/' . floor($seconds / static::HOUR_SECONDS)
                );
            } else if ($seconds >= static::DAY_SECONDS && $seconds < static::MONTH_SECONDS) {
                $this->_minutes = 0;
                $this->_hours = 0;
                $this->_options = array(
                    'type' => 'EVERY_DAY',
                    'frequency' => '*/' . floor($seconds / static::DAY_SECONDS)
                );
            } else if ($seconds >= static::MONTH_SECONDS && $seconds <= static::YEAR_SECONDS) {
                $this->_minutes = 0;
                $this->_hours = 0;
                $this->_daysOfTheMonth = 1;
                $this->_daysOfTheWeek = '*';
                $this->_options = array(
                    'type' => 'EVERY_MONTH',
                    'frequency' => '*/' . floor($seconds / static::MONTH_SECONDS)
                );
            }
        }
    }

    final private function __clone()
    {
    }

    public static function everyMinutes($minutes = 1)
    {
        return new static(intval($minutes) * static::MINUTE_SECONDS);
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

    public static function everyYear()
    {
        return static::everyMonths(12);
    }

    public static function everyWeek()
    {
        return new static(array(1), true);
    }

    public static function everyWeekday()
    {
        return new static(array(1,2,3,4,5), true);
    }

    public static function everyWeekend()
    {
        return new static(array(0, 6), true);
    }

    public function __call($method, $arguments)
    {
        if (array_key_exists($method, self::$_ranges) === false) {
            throw new UndefinedMethodException($method, __class__);
        }

        if (count($arguments) === 0) {
            throw new MissingArgumentException($method, __class__);
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
                is_array($arguments[0]) ? $arguments[0] : array($arguments[0])
            )
        );
        $attribute = "_{$method}";
        $this->$attribute = implode(',', $times);
        return $this;
    }

    public function parse()
    {
        $sections = array_fill(0, 5, '*');
        $options = $this->_options;
        switch ($options['type']) {
            case 'EVERY_MINUTE':
                $sections[0] = $options['frequency'];
                break;
            case 'EVERY_HOUR':
                $sections[0] = $this->_minutes;
                $sections[1] = $options['frequency'];
                break;
            case 'EVERY_DAY':
                $sections[0] = $this->_minutes;
                $sections[1] = $this->_hours;
                $sections[2] = $options['frequency'];
                break;
            case 'EVERY_MONTH':
                $sections[0] = $this->_minutes;
                $sections[1] = $this->_hours;
                $sections[2] = $this->_daysOfTheMonth;
                $sections[3] = $options['frequency'];
                $sections[4] = $this->_daysOfTheWeek;
                break;
            case 'EVERY_WEEK':
                $sections[0] = $this->_minutes;
                $sections[1] = $this->_hours;
                $sections[3] = $this->_months;
                $sections[4] = $options['frequency'];
            default:
                break;
        }

        return implode(' ', $sections);
    }
}