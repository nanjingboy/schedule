<?php
namespace Schedule;

class Scheduler
{
    protected static $_instance = null;

    protected $_jobs = array();

    public static function instance()
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    public function job()
    {
        $job = new Job();
        array_push($this->_jobs, $job);
        return $job;
    }

    final private function __construct()
    {
    }

    final private function __clone()
    {
    }
}