<?php
namespace Test\Src;

use Schedule\Command;
use PHPUnit_Framework_TestCase;

class CommandTest extends PHPUnit_Framework_TestCase
{
    public function testParseFile()
    {
        $command = new Command(array('command' => 'balabalabala'));
        $this->assertEquals(
            'ENV=test HOME=/Users/tom balabalabala',
            $command->parse()
        );
    }

    public function testParseCommand()
    {
        $command = new Command(array('file' => 'command.php'));
        $this->assertEquals(
            'ENV=test HOME=/Users/tom php command.php',
            $command->parse()
        );
    }
}