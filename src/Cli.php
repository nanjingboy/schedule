<?php
namespace Schedule;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Cli
{
    private static function _parseScheduleFilePath(InputInterface $input)
    {
        $path = $input->getOption('path');
        if (!empty($path)) {
            return $path;
        }

        return getcwd() . DIRECTORY_SEPARATOR . 'schedule.php';
    }

    public static function init(InputInterface $input, OutputInterface $output)
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

    public static function clear(InputInterface $input, OutputInterface $output)
    {
        $user = $input->getOption('user');
        if (empty($user)) {
            system('crontab -r > /dev/null 2>&1');
        } else {
            system("crontab -u {$user} -r > /dev/null 2>&1");
        }
        $output->writeln('<info>Crontab file has been cleared</info>');
    }

    public static function write(InputInterface $input, OutputInterface $output)
    {
        $path = self::_parseScheduleFilePath($input);
        if (file_exists($path)) {
            require $path;
            $jobs = Scheduler::instance()->parse();
            if (!empty($jobs)) {
                $user = $input->getOption('user');
                if (empty($usr)) {
                    $crontab = popen('crontab -', 'r+');
                } else {
                    $crontab = popen("crontab -u {$user} -", 'r+');
                }

                fwrite($crontab, "#BEGIN DEFINE CRON JOBS FROM:{$path}\n");
                foreach ($jobs as $job) {
                    fwrite($crontab, $job . "\n");
                }
                fwrite($crontab, "#END DEFINE CRON JOBS FROM:{$path}\n");
                pclose($crontab);
            }
        }
        $output->writeln('<info>Crontab file has been written</info>');
    }
}