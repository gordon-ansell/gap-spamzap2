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
namespace GreenFedora\Tests\Stdlib\Text;

use PHPUnit\Framework\TestCase;
use GreenFedora\Stdlib\Text\ArrayTextBuffer;
use GreenFedora\Stdlib\Text\TextBuffer;
use GreenFedora\Stdlib\Text\TextDecoratorInterface;
use GreenFedora\Stdlib\Text\TextFormatterInterface;
use GreenFedora\Stdlib\Level;
use GreenFedora\Console\Output\ConsoleColourDecorator;

class TestDecorator implements TextDecoratorInterface {
    public function decorate(string $text, int $level = 0): string
    {
        return '---' . $text . '---';
    }
}

class TestFormatter implements TextFormatterInterface {
    public function format(string $text, int $level = 0): string
    {
        if (0 !== $level) {
            $text = ucfirst(Level::l2t($level)) . ': ' . $text;
        }
        return $text;
    }
}

/**
 * Tests for this package.
 */
final class TextTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testPlainTextBuffer()
    {
        $expected = [
            "one",
            "two split",
            "Number three",
            "",
            "four"
        ];

        $tb = new ArrayTextBuffer();
        $tb->writeln("one");
        $tb->write("two");
        $tb->writeln(" split");
        $tb->writeln("Number %s", 0, ["three"]);
        $tb->blank();
        $tb->writeln("four");

        $this->assertEquals($expected, $tb->getData());
    }

    public function testWithDecorator()
    {
        $expected = "---hello---";

        $tb = new ArrayTextBuffer(0, new TestDecorator());

        $tb->writeln("hello");

        $this->assertEquals([$expected], $tb->getData());
    }

    public function testWithFormatter()
    {
        $expected = "Error: hello";

        $tb = new ArrayTextBuffer(0, null, new TestFormatter());

        $tb->error("hello");

        $this->assertEquals([$expected], $tb->getData());
    }


    /*
    public function testWithColourDecorator()
    {
        $decorator = new ConsoleColourDecorator();
        $tb = new TextBuffer(Level::ALERT, $decorator);

        $tb->debug("This is a test debug");
        $tb->info("This is a test info");
        $tb->notice("This is a test notice");
        $tb->warning("This is a test warning");
        $tb->error("This is a test error");
        $tb->critical("This is a test critical");
        $tb->alert("This is a test alert");
        $tb->emergency("This is a test emergency");

        $this->assertTrue(true);
    }
    */

    public function tearDown(): void
    {
    }
}