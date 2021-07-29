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
namespace GreenFedora\Tests\Console\Output;

use PHPUnit\Framework\TestCase;
use GreenFedora\Console\Output\ConsoleOutput;
use GreenFedora\Stdlib\Level;

/**
 * Tests for this package.
 */
final class OutputTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testConsoleOutput()
    {
        $tb = new ConsoleOutput(Level::DEBUG);

        $this->assertTrue(true);

        /*
        $tb->debug("This is a test debug");
        $tb->info("This is a test info");
        $tb->notice("This is a test notice");
        $tb->warning("This is a test warning");
        $tb->error("This is a test error");
        $tb->critical("This is a test critical");
        $tb->alert("This is a test alert");
        $tb->emergency("This is a test emergency");
        */
    }

    public function tearDown(): void
    {
    }
}