<?php
namespace Schedule;

class Command
{
    private $_options;

    public function __construct($options)
    {
        foreach (array('log', 'standard_log', 'error_log') as $key) {
            if (array_key_exists($key, $options) && $options[$key] === null) {
                $options[$key] = '/dev/null';
            }
        }

        if (!empty($options['standard_log']) &&
            !empty($options['error_log']) &&
            $options['standard_log'] === $options['error_log']
        ) {
            $options['log'] = $options['standard_log'];
            unset($options['standard_log']);
            unset($options['error_log']);
        }

        $this->_options = $options;
    }

    private function _parseEnvironment()
    {
        $environmentFile = getenv('SCHEDULE_ENV_FILE');
        if (empty($environmentFile)) {
            $environmentFile = getcwd() . DIRECTORY_SEPARATOR . getenv('ENV') . '.env';
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

        $command = "{$environment} {$command}";

        if (!empty($this->_options['log'])) {
            return $command . ' >> ' . $this->_options['log'] . ' 2>&1';
        }

        if (!empty($this->_options['standard_log'])) {
            $command .= ' >> ' . $this->_options['standard_log'];
        }

        if (!empty($this->_options['error_log'])) {
            $command .= ' 2>> ' . $this->_options['error_log'];
        }

        return $command;
    }
}