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

    public static function options()
    {
        return array(
            array(
                'name' => 'path',
                'description' => 'Path of the initial schedule.php file'
            )
        );
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
 * Schedule\Cron->everyMinutes(1)->command('/usr/bin/your_command');
 * Schedule\Cron->everyHours(1)->minutes(10)->file('/Users/tom/command.php');
 *
 * Get more information from: https://github.com/nanjingboy/schedule
 */
require __DIR__ . '/vendor/autoload.php';

use Schedule\Cron;
FILE;
            file_put_contents($path, $content);
            $output->writeln('<info>File created: </info>' . $path);
        }
    }
}