<?php
namespace Schedule;

class Cli
{
    private static function _parseScheduleFilePath($input)
    {
        $path = $input->getOption('path');
        if (!empty($path)) {
            return $path;
        }

        return getcwd() . DIRECTORY_SEPARATOR . 'schedule.php';
    }

    private static function _parseJobsWhichCanNotRemove($input)
    {
        $user = $input->getOption('user');
        if (empty($user)) {
            $command = 'crontab -l';
        } else {
            $command = 'crontab -l -u ' . $user;
        }

        $result = array();
        $removeable = false;
        $jobs = explode("\n", shell_exec($command . ' 2> /dev/null'));
        $path = self::_parseScheduleFilePath($input);
        foreach ($jobs as $job) {
            $job = trim($job);
            if (empty($job)) {
                continue;
            }

            if ($job === "#BEGIN DEFINE CRON JOBS FROM:{$path}") {
                $removeable = true;
                continue;
            }

            if ($job === "#END DEFINE CRON JOBS FROM:{$path}") {
                $removeable = false;
                continue;
            }

            if ($removeable === false) {
                array_push($result, $job);
            }
        }

        return $result;
    }

    private static function _writeCrontab($jobs, $input)
    {
        $user = $input->getOption('user');
        if (empty($jobs)) {
            if (empty($user)) {
                system('crontab -r > /dev/null 2>&1');
            } else {
                system("crontab -u {$user} -r > /dev/null 2>&1");
            }
            return true;
        }

        if (empty($usr)) {
            $crontab = popen('crontab -', 'r+');
        } else {
            $crontab = popen("crontab -u {$user} -", 'r+');
        }

        foreach ($jobs as $job) {
            fwrite($crontab, $job . "\n");
        }
        pclose($crontab);

        return true;
    }

    public static function init($input, $output)
    {
        $path = self::_parseScheduleFilePath($input);
        if (file_exists($path)) {
            $output->writeln('<comment>File already exists: </comment>' . $path);
        } else if (file_exists(dirname($path)) === false) {
            $output->writeln('<comment>Directory does not exist: </comment>' . dirname($path));
        } else {
            $content = <<<FILE
<?php
/**
 * Use this file to define your cron jobs.
 * Examples:
 *
 * use Schedule\Scheduler;
 *
 * Scheduler::instance()->cron()->everyMinutes(1)->command('/usr/bin/your_command');
 * Scheduler::instance()->cron()->everyHours(1)->minutes(10)->file('/Users/tom/command.php');
 *
 * Get more information from: https://github.com/nanjingboy/schedule
 */
require __DIR__ . '/vendor/autoload.php';

use Schedule\Scheduler;
FILE;
            file_put_contents($path, $content);
            $output->writeln('<info>File created: </info>' . $path);
        }
    }

    public static function clear($input, $output)
    {
        self::_writeCrontab(self::_parseJobsWhichCanNotRemove($input), $input);
        $output->writeln('<info>Crontab file has been cleared</info>');
    }

    public static function write($input, $output)
    {
        $path = self::_parseScheduleFilePath($input);
        $jobs = array();
        if (file_exists($path)) {
            require $path;
            $jobs = Scheduler::instance()->parse();
            if (!empty($jobs)) {
                array_unshift($jobs, "#BEGIN DEFINE CRON JOBS FROM:{$path}");
                array_push($jobs, "#END DEFINE CRON JOBS FROM:{$path}");
            }
        }
        self::_writeCrontab(
            array_merge(
                self::_parseJobsWhichCanNotRemove($input),
                $jobs
            ),
            $input
        );
        $output->writeln('<info>Crontab file has been written</info>');
    }
}