<?php
namespace Schedule;

use Exception;

class ScheduleException extends Exception
{
}

class UndefinedMethodException extends ScheduleException
{
    public function __construct($method, $class)
    {
        parent::__construct("Call to undefined method {$class}::{$method}()");
    }
}

class MissingArgumentException extends ScheduleException
{
    public function __construct($method, $class, $argumentNumber = 1)
    {
        parent::__construct("Missing at least {$argumentNumber} arguments for {$class}::{$method}()");
    }
}