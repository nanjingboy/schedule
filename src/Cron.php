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
        'minutes' => array('min' => 0, 'max' => 59),
        'hours' => array('min' => 0, 'max' => 23),
        'daysOfTheMonth' => array('min' => 1, 'max' => 31),
        'months' => array('min' => 1, 'max' => 12),
        'daysOfTheWeek' => array('min' => 0, 'max' => 7)
    );
    private static $_rangeErrors = array(
        'minutes' => 'Minute must between 0 and 59',
        'hours' => 'Hour must between 0 and 23',
        'daysOfTheMonth' => 'Day of the month must between 1 and 31',
        'months' => 'Month must between 1 and 12',
        'daysOfTheWeek' => 'Day of the week must between 0 and 7'
    );

    private $_seconds = 0;
    private $_minutes = 0;
    private $_hours = 0;
    private $_daysOfTheMonth = 0;
    private $_months = 0;
    private $_daysOfTheWeek = 0;

    private static function _parseFrequency($frequency, $max, $start = 0)
    {
        if (empty($frequency)) {
            return $start;
        }

        $frequency = intval($frequency);
        if ($frequency === 1) {
            return '*';
        }

        if ($frequency > ceil($max * 0.5)) {
            return $frequency;
        }

        $originalStart = $start;
        if (intval(fmod($max + 1, $frequency)) !== 0 && $start <= 0) {
            $start += $frequency;
        }

        $output = Helper::range($start, $max, $frequency);
        $maxOccurances = round(floatval($max) / floatval($frequency));
        if ($originalStart === 0) {
            $maxOccurances += 1;
        }
        return implode(',', array_slice($output, 0, $maxOccurances));
    }

    public static function everyMinutes($minutes = 1)
    {
        return new Static(intval($minutes) * static::MINUTE_SECONDS);
    }

    public static function everyHours($hours = 1)
    {
        return new static(intval($hours) * static::HOUR_SECONDS);
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
        $rangeError = self::$_rangeErrors[$method];
        $times = array_unique(
            array_map(
                function($time) use($range, $rangeError) {
                    $time = intval($time);
                    if ($time < $range['min'] || $time > $range['max']) {
                        throw new InvalidArgumentException($rangeError);
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
            $sections[0] = self::_parseFrequency(
                floor($seconds / static::MINUTE_SECONDS), 59
            );
        } else if ($seconds >= static::HOUR_SECONDS && $seconds < static::DAY_SECONDS) {
            $sections[0] = $this->_minutes;
            $sections[1] = self::_parseFrequency(
                floor($seconds / static::HOUR_SECONDS), 23
            );
        }

        return implode(' ', $sections);
    }
}