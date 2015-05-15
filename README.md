### Schedule:

A simple tool that provides a easy way for writing and deploying cron jobs.

### Getting Started:

Here assume your application directory path is: /Users/tom/workspace/test

* cd /Users/tom/workspace/test
* Create composer.json file:

```json
{
    "require": {
        "php": ">=5.4.0",
        "nanjingboy/schedule": "*"
    },
    "config": {
        "bin-dir": "bin/"
    }
}
```
* Install it via [composer](https://getcomposer.org/doc/00-intro.md)
* bin/schedule init    #  Create an initial schedule.php file to define cron jobs

### Example schedule.php file:

```php
<?php
use Schedule\Scheduler;

$scheduler = Scheduler::instance();

$schedule->cron()->every()->months($months)->daysOfTheMonth($daysOfTheMonth)->daysOfTheWeek($daysOfTheWeek)->hours($hours)->minutes($minutes)->command($command, $logOptions = array());

$scheduler->cron()->everyMinutes($minutes)->command($command, $logOptions = array());

$scheduler->cron()->everyHours($hours)->minutes($minutes)->command($command, $logOptions = array());

$scheduler->cron()->everyDays($days)->hours($hours)->minutes($minutes)->command($command, $logOptions = array());

$scheduler->cron()->everyMonths($months)->daysOfTheMonth($daysOfTheMonth)->daysOfTheWeek($daysOfTheWeek)->hours($hours)->minutes($minutes)->command($command, $logOptions = array());

$scheduler->cron()->everyYear()->daysOfTheMonth($daysOfTheMonth)->daysOfTheWeek($daysOfTheWeek)->hours($hours)->minutes($minutes)->command($command, $logOptions = array());

$scheduler->cron()->everyWeek()->months($months)->hours($hours)->minutes($minutes)->command($command, $options = array());

$scheduler->cron()->everyWeekday()->months($months)->hours($hours)->minutes($minutes)->command($command, $logOptions = array());

$scheduler->cron()->everyWeekend()->months($months)->hours($hours)->minutes($minutes)->command($command, $logOptions = array());
```

* $minutes must between 1 and 59.
* $hours must between 1 and 23.
* $daysOfTheMonth must between 1 and 30.
* $daysOfTheWeek must between 0 and 7.
* $months must between 1 and 12.
* $logOptions:
    *  log: output STDOUT and STDERR to the given path.
    *  standard_log: output STDOUT to the given path.
    *  error_log: output STDERR to the given path.
    *  if set value of log,standard_log,error_log with null, it will output STDOUT or STDERR to /dev/null.
* The value of $minutes, $hours, $daysOfTheMonth, $daysOfTheWeek, $months can be set with a single integer or array, while methods which start with every only can be set with a single integer.
* Except methods which start with every you must invoke, the other is optional.
* You can replace command method with file, get more information from [file invoke](https://github.com/nanjingboy/schedule/blob/master/test/src/CronTest.php#L117).

### Environment:

* If a \*.env(file name will read from ENV environment) file exists in the current directory, the default environment will be read from it.
* You can also set SCHEDULE_ENV_FILE environment to special a \*.env file.
* If ENV and SCHEDULE_ENV_FILE neither be set, the default environment will be read from .env file(if it exists) in the current directory.
* Get a example from: [test.env](https://github.com/nanjingboy/schedule/blob/master/test/test.env)

### Cli:

```shell
$ bin/schedule

schedule version 0.1.0

Usage:
 command [options] [arguments]

Options:
 --help (-h)           Display this help message
 --quiet (-q)          Do not output any message
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version
 --ansi                Force ANSI output
 --no-ansi             Disable ANSI output
 --no-interaction (-n) Do not ask any interactive question

Available commands:
 clear   Clear cron jobs that defined in schedule.php file from crontab
 help    Displays help for a command
 init    Create an initial schedule.php file
 list    Lists commands
 write   Write cron jobs that defined in schedule.php file to crontab
```

### License:

MIT