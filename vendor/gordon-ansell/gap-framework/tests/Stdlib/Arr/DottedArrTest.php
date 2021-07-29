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
namespace GreenFedora\Tests\Stdlib\Arr;

use PHPUnit\Framework\TestCase;
use GreenFedora\Stdlib\Arr\DottedArr;


/**
 * Tests for this package.
 */
final class DottedArrTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testConstruction()
    {
        $t1 = array('a' => 'one', 'b' => 'two', 'c' => 3);
        $arr = new DottedArr($t1);
        $this->assertEquals(new DottedArr($t1), $arr);
        $this->assertEquals($t1, $arr->toArray());
        
    }

    public function testDottedArrayAccessByDottedMethods()
    {
        $arr = new DottedArr();
        $arr->setDotted('first.second', 'two');

        $this->assertEquals('two', $arr->first->second);
        $this->assertEquals('two', $arr->dotted('first.second'));

        $arr->setDotted('test1.test2.test3', "hello");
        $this->assertEquals('hello', $arr->dotted('test1.test2.test3'));
        $arr->unsetDotted('test1.test2.test3');
        $this->assertEquals(null, $arr->dotted('test1.test2.test3'));
    }

    public function tearDown(): void
    {
    }
}