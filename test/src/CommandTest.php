<?php
namespace Test\Src;

use Schedule\Command;
use PHPUnit_Framework_TestCase;

class CommandTest extends PHPUnit_Framework_TestCase
{
    public function testParseFile()
    {
        $command = new Command(
            array(
                'command' => 'balabalabala'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom balabalabala',
            $command->parse()
        );

        $command = new Command(
            array(
                'command' => 'balabalabala',
                'log' => null
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom balabalabala >> /dev/null 2>&1',
            $command->parse()
        );

        $command = new Command(
            array(
                'command' => 'balabalabala',
                'log' => 'balabalabala.log'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom balabalabala >> balabalabala.log 2>&1',
            $command->parse()
        );

        $command = new Command(
            array(
                'command' => 'balabalabala',
                'standard_log' => 'standard_log.log'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom balabalabala >> standard_log.log',
            $command->parse()
        );

        $command = new Command(
            array(
                'command' => 'balabalabala',
                'error_log' => 'error_log.log'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom balabalabala 2>> error_log.log',
            $command->parse()
        );

        $command = new Command(
            array(
                'command' => 'balabalabala',
                'standard_log' => 'balabalabala.log',
                'error_log' => 'balabalabala.log'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom balabalabala >> balabalabala.log 2>&1',
            $command->parse()
        );
    }

    public function testParseCommand()
    {
        $command = new Command(
            array(
                'file' => 'command.php'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom php command.php',
            $command->parse()
        );

        $command = new Command(
            array(
                'file' => 'command.php',
                'log' => null
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom php command.php >> /dev/null 2>&1',
            $command->parse()
        );

        $command = new Command(
            array(
                'file' => 'command.php',
                'log' => 'command.log'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom php command.php >> command.log 2>&1',
            $command->parse()
        );

        $command = new Command(
            array(
                'file' => 'command.php',
                'standard_log' => 'standard_log.log'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom php command.php >> standard_log.log',
            $command->parse()
        );

        $command = new Command(
            array(
                'file' => 'command.php',
                'error_log' => 'error_log.log'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom php command.php 2>> error_log.log',
            $command->parse()
        );


        $command = new Command(
            array(
                'file' => 'command.php',
                'standard_log' => 'command.log',
                'error_log' => 'command.log'
            )
        );
        $this->assertEquals(
            'ENV=test HOME=/Users/tom php command.php >> command.log 2>&1',
            $command->parse()
        );
    }
}