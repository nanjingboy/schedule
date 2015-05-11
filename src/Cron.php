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

    private static function _parseCronFrequency($frequency, $max, $start = 0)
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

    private static function _parseCronSyntax($seconds, $at = array())
    {
        $sections = array_fill(0, 5, '*');
        $seconds = intval($seconds);
        if ($seconds < static::MINUTE_SECONDS) {
            throw new InvalidArgumentException('Time must be in minutes or higher');
        }

        if ($seconds >= static::MINUTE_SECONDS && $seconds < static::HOUR_SECONDS) {
            $sections[0] = self::_parseCronFrequency(
                floor($seconds / static::MINUTE_SECONDS), 59
            );
        } else if ($seconds >= static::HOUR_SECONDS && $seconds < static::DAY_SECONDS) {
            $sections[0] = (!empty($at['minute']) ? $at['minute'] : 0);
            $sections[1] = self::_parseCronFrequency(
                floor($seconds / static::HOUR_SECONDS), 23
            );
        }

        return implode(' ', $sections);
    }

    public static function minute($minutes)
    {
        return self::_parseCronSyntax(intval($minutes) * static::MINUTE_SECONDS);
    }

    public static function hour($hours, $minutes = 0)
    {
        if (!is_array($minutes)) {
            $minutes = array($minutes);
        }

        $minutes = array_unique(
            array_map(
                function($minute) {
                    $minute = intval($minute);
                    if ($minute < 0 || $minute > 59) {
                        throw new InvalidArgumentException(
                            'Minute must between 0 and 59'
                        );
                    }
                    return $minute;
                },
                $minutes
            )
        );

        return self::_parseCronSyntax(
            intval($hours) * static::HOUR_SECONDS,
            array('minute' => implode(',', $minutes))
        );
    }
}