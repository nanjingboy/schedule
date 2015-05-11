<?php
namespace Schedule;

class Helper
{
    public static function range($start, $end, $step = 1)
    {
        $range = array();
        for ($index = $start; $index <= $end; $index += $step) {
            array_push($range, $index);
        }
        return $range;
    }
}