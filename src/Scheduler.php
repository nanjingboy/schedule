<?php
namespace Schedule;

class Scheduler
{
    protected static $_instance = null;

    protected $_crons = array();

    public static function instance()
    {
        if (static::$_instance === null) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }

    public function cron()
    {
        $cron = new Cron();
        array_push($this->_crons, $cron);
        return $cron;
    }

    public function parse()
    {
        return array_map(
            function($cron) {
                return $cron->parse();
            },
            $this->_crons
        );
    }

    final private function __construct()
    {
    }

    final private function __clone()
    {
    }
}