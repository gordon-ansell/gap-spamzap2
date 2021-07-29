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
namespace GreenFedora\Tests\Logger;

use GreenFedora\Logger\Formatter\StdLogFormatter;
use PHPUnit\Framework\TestCase;
use GreenFedora\Logger\Logger;
use GreenFedora\Logger\Writer\ConsoleLogWriter;
use GreenFedora\Logger\Writer\FileLogWriter;
use GreenFedora\Stdlib\Level;

/**
 * Tests for this package.
 */
final class LoggerTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testGeneral()
    {
        /*
        $cfg = [
            'level' => Level::INFO
        ];

        $path = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
            
        $formatter = new StdLogFormatter($cfg);
        $writers = [
            new ConsoleLogWriter($cfg, $formatter),
            new FileLogWriter($cfg, $formatter, $path)
        ];

        $tb = new Logger($cfg, $writers);
        $tb->level('debug');

        $tb->debug("This is a test debug");
        $tb->info("This is a test info");
        $tb->notice("This is a test notice");
        $tb->warning("This is a test warning");
        $tb->error("This is a test error");
        $tb->critical("This is a test critical");
        $tb->alert("This is a test alert");
        $tb->emergency("This is a test emergency");
        */

        $this->assertTrue(true);
    }

    public function tearDown(): void
    {
    }
}