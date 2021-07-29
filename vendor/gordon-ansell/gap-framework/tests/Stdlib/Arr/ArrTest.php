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
use GreenFedora\Stdlib\Arr\Arr;

class ArrExtended extends Arr {};

/**
 * Tests for this package.
 */
final class ArrTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testSequentialConstruction()
    {
        $arr = new Arr(array('a', 'b', 'c'));
        $this->assertEquals(new Arr(['a', 'b', 'c']), $arr);
        $this->assertEquals(array('a', 'b', 'c'), $arr->toArray());

        $arrex = new ArrExtended(array('a', 'b', 'c'));
        $this->assertEquals(new ArrExtended(['a', 'b', 'c']), $arrex);
        $this->assertNotEquals($arr, $arrex);
        $this->assertEquals($arr->toArray(), $arrex->toArray());
        
    }

    public function testAssociativeConstruction()
    {
        $t1 = ['a' => 'one', 'b' => 2]; 
        $arr = new Arr($t1);
        $this->assertEquals(new Arr($t1), $arr);
        $this->assertEquals($t1, $arr->toArray());

        $arrex = new ArrExtended($t1);
        $this->assertEquals(new ArrExtended($t1), $arrex);
        $this->assertNotEquals($arr, $arrex);
        $this->assertEquals($arr->toArray(), $arrex->toArray());
        
    }

    public function testObjectConstruction()
    {
        $t1 = ['a' => 'one', 'b' => 2]; 
        $ao = new \ArrayObject($t1);
        $arr = new Arr($t1);
        $this->assertEquals(new Arr($t1), $arr);
        $this->assertEquals($t1, $arr->toArray());

        $arrex = new ArrExtended($t1);
        $this->assertEquals(new ArrExtended($t1), $arrex);
        $this->assertNotEquals($arr, $arrex);
        $this->assertEquals($arr->toArray(), $arrex->toArray());
        
    }

    public function testComplexConstruction()
    {
        $t1 = ['a' => 'one', 'b' => new \ArrayObject(['level1' => 'blah'])];
        $real = ['a' => 'one', 'b' => ['level1' => 'blah']];

        $arr = new Arr($t1);
        $this->assertEquals(new Arr($t1), $arr);
        $this->assertEquals($real, $arr->toArray());
    }

    public function testArrayAccess()
    {
        $t1 = ['orange' => 2, 'sprout' => 3, 'apple' => 1];

        $arr = new Arr($t1);

        $this->assertEquals(2, $arr->orange);
        $this->assertEquals(2, $arr['orange']);

        $arr->orange = 42;
        $this->assertEquals(42, $arr->orange);
    }

    public function testSort()
    {
        $t1 = ['orange', 'sprout', 'apple'];
        $t2 = ['apple', 'orange', 'sprout'];

        $arr = new Arr($t1);
        $arr->sort();
        $this->assertEquals($t2, $arr->toArray());

        $t1 = ['orange' => 2, 'sprout' => 3, 'apple' => 1];
        $t2 = ['apple' => 1, 'orange' => 2, 'sprout' => 3];

        $arr = new Arr($t1);
        $arr->ksort();
        $this->assertEquals($t2, $arr->toArray());
    }

    public function testMergeReplace()
    {
        $t1 = [
            'one' => 1,
            'two' => 2,
            'three' => [
                'deep1' => 4,
                'deep2' => 5
            ]
        ];

        $result1 = [
            'one' => 999999,
            'two' => 2,
            'three' => [
                'deep1' => 4,
                'deep2' => 5
            ]
        ];

        $result2 = [
            'one' => 1,
            'two' => 2,
            'three' => [
                'deep1' => 4,
                'deep2' => 5
            ],
            'four' => 'hello'
        ];

        $result3 = [
            'one' => 1,
            'two' => 2,
            'three' => [
                'deep1' => 42,
                'deep2' => 5,
                'deep3' => 6
            ]
        ];

        $arr = new Arr($t1);
        $arr->mergeReplaceRecursive(['one' => 999999]);
        $this->assertEquals($result1, $arr->toArray());

        $arr = new Arr($t1);
        $arr->mergeReplaceRecursive(['four' => 'hello']);
        $this->assertEquals($result2, $arr->toArray());

        $arr = new Arr($t1);
        $arr->mergeReplaceRecursive(['three' => ['deep1' => 42, 'deep3' => 6]]);
        $this->assertEquals($result3, $arr->toArray());
    }

    public function tearDown(): void
    {
    }
}