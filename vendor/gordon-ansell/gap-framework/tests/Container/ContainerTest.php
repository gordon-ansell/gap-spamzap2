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
namespace GreenFedora\Tests\Container;

use PHPUnit\Framework\TestCase;
use GreenFedora\Container\Container;
use GreenFedora\Stdlib\Arr\DottedArr;

class TestClass1 {};

class TestClass2 {
    public $tc = null;
    public function __construct(TestClass1 $tc)
    {
        $this->tc = $tc;
    }
}

class TestClass3 {
    public $tc = null;
    public $ts = null;
    public function __construct(TestClass2 $tc, string $teststringvalue)
    {
        $this->tc = $tc;
        $this->ts = $teststringvalue;
    }
}

/**
 * Tests for this package.
 */
final class ContainerTest extends TestCase
{
    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testValueResolver()
    {
        $this->container->registerValue('random', "Test string value");
        
        $this->assertEquals("Test string value", $this->container->get('random'));
        $this->assertEquals("Test string value", $this->container->random);
    }

    public function testCallableResolver()
    {
        $this->container->registerCallable('random1', function () {return "Test string value";});
        
        $this->assertEquals("Test string value", $this->container->get('random1'));
        $this->assertEquals("Test string value", $this->container->random1);

        $arg1 = "hello";

        $this->container->registerCallable('random2', function () use ($arg1) {return "Value is: " . $arg1;});
        $this->assertEquals("Value is: hello", $this->container->get('random2'));
        $this->assertEquals("Value is: hello", $this->container->random2);

        $this->container->registerCallable('random3', 
            function (string $name) use ($arg1) {return "Value is: " . $arg1 . ' ' . $name;});
        $this->assertEquals("Value is: hello Gordon", $this->container->get('random3', ["Gordon"]));
    }

    public function testClassResolver()
    {
        $this->container->registerClass('testclass1', TestClass1::class);

        $this->assertInstanceOf(TestClass1::class, $this->container->get('testclass1'));

        $this->container->registerClass('testclass2', TestClass2::class);

        $this->assertInstanceOf(TestClass2::class, $this->container->get('testclass2'));
        $this->assertInstanceOf(TestClass1::class, $this->container->get('testclass2')->tc);

        $this->container->registerClass('testclass3', TestClass3::class, false, [null, "Yikes"]);
        //$this->container->registerValue('teststringvalue', "Yikes");

        $this->assertInstanceOf(TestClass3::class, $this->container->get('testclass3'));
        $this->assertInstanceOf(TestClass2::class, $this->container->get('testclass3')->tc);
        $this->assertEquals("Yikes", $this->container->get('testclass3')->ts);

        $this->assertEquals("Good", $this->container->get('testclass3', [null, "Good"])->ts);

    }

    public function testSingletonResolver()
    {
        $this->container->register('testclass1', TestClass1::class);
        $this->container->registerClass('testclass2', TestClass2::class);
        $this->container->registerSingleton('testclass3', TestClass3::class, [null, "Yikes"]);

        $o1 = $this->container->singleton('testclass3');
        $o2 = $this->container->get('testclass3', [null, "Good"]);

        $this->assertEquals($o1, $o2);

        $this->assertEquals($o2, $this->container->singleton('testclass3'));

    }


    public function tearDown(): void
    {
    }
}