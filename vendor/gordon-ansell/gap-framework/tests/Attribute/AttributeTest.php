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
namespace GreenFedora\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use GreenFedora\Attribute\AttributeClass;
use GreenFedora\Attribute\Attribute;

/**
 * Attributes class.
 * 
 * #[Inject (test:1, anothertest : 2)]
 */
class AttrClass
{
    /**
     * Constructor.
     */
    public function __construct(){}
}

/**
 * Attributes class 2.
 * 
 * #[Inject ()]
 */
class AttrClass2
{
    /**
     * Constructor.
     */
    public function __construct(){}
}

/**
 * Attributes class.
 * 
 */
class AttrClass3
{
    /**
     * Constructor.
     * #[Inject (testfirst:1)]
     * #[Inject (test:2, anothertest : 3)]
     */
    public function __construct(){}
}

/**
 * Tests for this package.
 */
final class AttributeTest extends TestCase
{

    public function testBasicAttributes()
    {
        $r = new AttributeClass(AttrClass::class);
        $attrs = $r->getAttributes();

        foreach ($attrs as $attr) {
            $this->assertInstanceOf(Attribute::class, $attr);
        }

        $this->assertEquals('Inject', $attrs[0]->getName());

        $args = $attrs[0]->getArguments();
        $expected = ["test:1", "anothertest : 2"];

        $this->assertEquals($expected, $args);
    }

    public function testAttributesWithNoArguments()
    {
        $r = new AttributeClass(AttrClass2::class);
        $attrs = $r->getAttributes();

        foreach ($attrs as $attr) {
            $this->assertInstanceOf(Attribute::class, $attr);
        }

        $this->assertEquals('Inject', $attrs[0]->getName());

        $args = $attrs[0]->getArguments();
        $expected = [];

        $this->assertEquals($expected, $args);
    }

    public function testAttributesOnConstructor()
    {
        $c = new AttributeClass(AttrClass3::class);
        $r = $c->getConstructor();
        $attrs = $r->getAttributes();

        foreach ($attrs as $attr) {
            $this->assertInstanceOf(Attribute::class, $attr);
            $this->assertEquals('Inject', $attr->getName());
        }

        $args = $attrs[0]->getArguments();
        $expected = ["testfirst:1"];

        $this->assertEquals($expected, $args);

        $args = $attrs[1]->getArguments();
        $expected = ["test:2", "anothertest : 3"];

        $this->assertEquals($expected, $args);
    }

}