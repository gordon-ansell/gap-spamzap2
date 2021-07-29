<?php 
/**
 * This file is part of the GreenFedora PHP framework.
 *
 * (c) Gordon Ansell <contact@gordonansell.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
namespace GreenFedora\Tests\GetOpt;

use PHPUnit\Framework\TestCase;
use GreenFedora\GetOpt\GetOpt;
use GreenFedora\GetOpt\Positional;
use GreenFedora\GetOpt\Parameter;
use GreenFedora\GetOpt\Option;

//use GreenFedora\Validator\Numericvalidator;

/**
 * Tests for this package.
 */
final class GetOptTest extends TestCase
{
    public function setUp(): void
    {
        $this->getOpt = new GetOpt();
    }

    public function testSinglePositional()
    {
        $argv = ['./ipm', 'reason'];

        $this->getOpt->addPositional('command', 'The command');

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertNull($ret);
        $this->assertEquals('reason', $this->getOpt->getPositional('command')->getValue());
    }

    public function testThreePositionals()
    {
        $argv = ['./ipm', 'reason', 'add', 'datum'];

        $this->getOpt->addPositional('command', 'The command')
            ->addPositional('action', 'The action')
            ->addPositional('summat', 'Something else');

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertNull($ret);
        $this->assertEquals('reason', $this->getOpt->getPositional('command')->getValue());
        $this->assertEquals('add', $this->getOpt->getPositional('action')->getValue());
        $this->assertEquals('datum', $this->getOpt->getPositional('summat')->getValue());
    }

    public function testMissingPositional()
    {
        $argv = ['./ipm', 'reason', 'add'];

        $this->getOpt->addPositional('command', 'The command')
            ->addPositional('action', 'The action')
            ->addPositional('summat', 'Something else');

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertEquals("You must specify a value for the 'summat' parameter.", $ret);

    }

    public function testOptionalPositional()
    {
        $argv = ['./ipm', 'reason', 'add'];

        $this->getOpt->addPositional('command', 'The command')
            ->addPositional('action', 'The action')
            ->addPositional('summat', 'Something else', Parameter::OPTIONAL);

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertEquals(null, $ret);
    }

    public function testArrayPositional()
    {
        $argv = ['./ipm', 'reason', 'add', 'blah', 'bonk'];

        $this->getOpt->addPositional('command', 'The command')
            ->addPositional('action', 'The action')
            ->addPositional('summat', 'Something else', Parameter::ARRAYVAL);

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertNull($ret);
        $this->assertEquals(['blah', 'bonk'], $this->getOpt->getPositional('summat')->getValue());

    }

    public function testValidator()
    {
        $argv = ['./ipm', 'reason'];

        $this->getOpt->addPositional('command', 'The command');

        //$this->getOpt->getPositional('command')->addValidator(\GreenFedora\Validator\Numericvalidator::class);
        $this->getOpt->getPositional('command')->addValidator('Numeric');

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertEquals("The 'command' field must be numeric.", $ret);
    }

    public function testChoices()
    {
        $argv = ['./ipm', 'reason'];

        $this->getOpt->addPositional('command', 'The command', 0, ['hi', 'lo']);

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertEquals("The 'command' parameter must be one of 'hi, lo'.", $ret);
    }

    public function testConditionalPositionals1()
    {
        $argv = ['./ipm', 'cmdval', 'actval'];

        $this->getOpt->addPositional('command', 'The command');
        $this->getOpt->addPositional('action', 'The action');
        $this->getOpt->addPositional('freeform', 'Freeform thing to test');

        $this->getOpt->getPositional('freeform')->addConditional('command', 'cmdval');

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertEquals("You must specify a value for the 'freeform' parameter.", $ret);
    }

    public function testConditionalPositionals2()
    {
        $argv = ['./ipm', 'notcmdval', 'actval'];

        $this->getOpt->addPositional('command', 'The command');
        $this->getOpt->addPositional('action', 'The action');
        $this->getOpt->addPositional('freeform', 'Freeform thing to test', Parameter::OPTIONAL);

        $this->getOpt->getPositional('freeform')->addConditional('command', 'cmdval');

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertNull($ret);

    }

    public function testConditionalOptions1()
    {
        $argv = ['./ipm', 'cmdval', 'actval'];

        $this->getOpt->addPositional('command', 'The command');
        $this->getOpt->addPositional('action', 'The action');
        $this->getOpt->addPositional('freeform', 'Freeform thing to test', Parameter::OPTIONAL);
        $this->getOpt->addOption('g', "The g option");

        $this->getOpt->getOption('g')->addConditional('command', 'cmdval');

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertEquals("You must specify a value for the 'g' option.", $ret);
    }

    public function testConditionalOptions2()
    {
        $argv = ['./ipm', 'notcmdval', 'actval'];

        $this->getOpt->addPositional('command', 'The command');
        $this->getOpt->addPositional('action', 'The action');
        $this->getOpt->addPositional('freeform', 'Freeform thing to test', Parameter::OPTIONAL);
        $this->getOpt->addOption('g', "The g option");

        $this->getOpt->getOption('g')->addConditional('command', 'cmdval');

        $ret = $this->getOpt->parseArgs($argv);

        $this->assertNull($ret);
    }

    public function testHelp()
    {
        $argv = ['./ipm', 'upd', 'actval'];

        $this->getOpt->addPositional('command', 'The command', 0, ['list', 'upd', 'add']);
        $this->getOpt->addPositional('action', 'The action');
        $this->getOpt->addPositional('freeform', 'Freeform thing to test', Parameter::OPTIONAL);
        $this->getOpt->addOption('g', "The g option");

        $this->getOpt->getOption('g')->addConditional('command', 'cmdval');

        $ret = $this->getOpt->parseArgs($argv);

        $this->getOpt->displayHelp('./test');

        $this->assertTrue(true);
    }

    public function testSomething()
    {
        $argv = [
            './ipm',
            'reason',
            'list',
            '-a',
            '1234',
            '-b=hhh',
            '-g',
            'zzzzzzzz',
            'yyyyyyyy',
            '-xyz',
            '--hello',
            'blah'
        ];
                
        // Add some arguments.
        $this->getOpt->addPositional('command', 'The action.')
            ->addPositional('action', "The action positional parameter.")
            ->addOption('a', 'The a option.', Parameter::COMPULSORY, null, 'numb')
            ->addOption('b', 'The b option')
            ->addOption('gonow', 'The go now option', null, 'g', 'great')
            ->addOption('hello', 'The hello option', null, 'h')
            ->addOption('x', 'The x option')
            ->addOption('y', 'The y option')
            ->addOption('z', 'The z option');

        $ret = $this->getOpt->parseArgs($argv);

        if (!is_null($ret)) {
            echo '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> ' . $ret . PHP_EOL;
        }

        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
    }
}