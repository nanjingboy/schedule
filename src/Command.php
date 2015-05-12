<?php
namespace Schedule;

class Command
{
    private $_options;

    public function __construct($options)
    {
        $this->_options = $options;
    }

    private function _parseEnvironment()
    {
        $environmentFile = getenv('SCHEDULE_ENV_FILE');
        if (empty($environmentFile)) {
            $environment = getenv('ENV');
            if (empty($environment)) {
                $environment = 'development';
            }
            $environmentFile = getcwd() . DIRECTORY_SEPARATOR . $environment . '.env';
        }

        if (!file_exists($environmentFile)) {
            return null;
        }

        return str_replace("\n", ' ', file_get_contents($environmentFile));
    }

    public function parse()
    {
        $command = null;
        if (!empty($this->_options['command'])) {
            $command = $this->_options['command'];
        } else if (!empty($this->_options['file'])) {
            $command = 'php ' . $this->_options['file'];
        }

        if ($command === null) {
            return null;
        }

        $environment = $this->_parseEnvironment();
        if ($environment === null) {
            return $command;
        }

        return "{$environment} {$command}";
    }
}