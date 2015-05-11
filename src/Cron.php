<?php
namespace Schedule;

use InvalidArgumentException;

class Cron
{
    const MINUTE_SECONDS = 60;
    const HOUR_SECONDS = 3600;
    const DAY_SECONDS = 86400;
    const WEEK_SECONDS = 604800;
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

    private static function _parseCronSyntax($seconds)
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
        }

        return implode(' ', $sections);
    }

    public static function minute($minutes)
    {
        $minutes = intval($minutes);
        if ($minutes <= 0 || $minutes >= 60) {
            throw new InvalidArgumentException('Minutes must between 1 and 59');
        }

        return self::_parseCronSyntax($minutes * static::MINUTE_SECONDS);
    }
}