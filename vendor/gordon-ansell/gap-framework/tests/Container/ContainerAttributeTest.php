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

/**
 * Doc comments.
 * 
 * @param   string  $mystring   A string.
 * 
 * #[Inject (mystring: mystringvalue, myint: myintvalue, mycfgval: cfg|configthing)]
 */
class TestClassAttr1 {
    public $tc = null;
    public function __construct(string $mystring = null, int $myint = null, string $mycfgval)
    {
        $this->mystring = $mystring;
        $this->myint = $myint;
        $this->mycfgval = $mycfgval;
    }
}

class TestClassAttr2 {
    public $tc = null;
    /**
     * Doc comments.
     * 
     * @param   string  $mystring   A string.
     * 
     * #[Inject (mystring: mystringvalue, myint: myintvalue, mycfgval: cfg|configthing)]
     */
    public function __construct(string $mystring = null, int $myint = null, string $mycfgval)
    {
        $this->mystring = $mystring;
        $this->myint = $myint;
        $this->mycfgval = $mycfgval;
    }
}

/**
 * Tests for this package.
 */
final class ContainerAttributeTest extends TestCase
{
    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testAttributeInjection()
    {
        $this->container->registerValue('mystringvalue', "Test string value");
        $this->container->registerValue('myintvalue', 42);
        $this->container->getConfig()->set('configthing', 'good');

        $this->container->registerClass('myclass', TestClassAttr1::class);

        $cls = $this->container->get('myclass');

        $this->assertEquals('Test string value', $cls->mystring);
        $this->assertEquals(42, $cls->myint);
        $this->assertEquals('good', $cls->mycfgval);
    }

    public function testConstructorAttributeInjection()
    {
        $this->container->registerValue('mystringvalue', "Test string value");
        $this->container->registerValue('myintvalue', 42);
        $this->container->getConfig()->set('configthing', 'good');

        $this->container->registerClass('myclass2', TestClassAttr2::class);

        $cls = $this->container->get('myclass2');

        $this->assertEquals('Test string value', $cls->mystring);
        $this->assertEquals(42, $cls->myint);
        $this->assertEquals('good', $cls->mycfgval);
    }

    public function tearDown(): void
    {
    }
}