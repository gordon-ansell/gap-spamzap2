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
namespace GreenFedora\Tests\Finder;

use PHPUnit\Framework\TestCase;
use GreenFedora\Finder\Finder;
use GreenFedora\Finder\Filter\FileExt;
use GreenFedora\Finder\Filter\FileNameStartsWith;

/**
 * Tests for this package.
 */
final class FinderTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testSimpleFilter()
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR . 'data1';

        $finder = new Finder($base);
        $finder->setRecurse(false);
        $finder->addFileFilterPositive(new FileExt('.md'));
        $expected = [$base . DIRECTORY_SEPARATOR . 'two.md'];
        $this->assertEquals($expected, $finder->filter());

        $finder = new Finder($base, '', null, null);
        $finder->setRecurse(false);
        $finder->addFileFilterNegative(new FileExt('.md'));
        $expected = [$base . DIRECTORY_SEPARATOR . 'one.txt', $base . DIRECTORY_SEPARATOR . 'three.txt'];
        $r = $finder->filter();
        $this->assertEquals($expected, $r);

        $finder = new Finder($base, '', null, null);
        $finder->setRecurse(false);
        $finder->addFileFilterPositive(new FileNameStartsWith('on'));
        $finder->addFileFilterNegative(new FileExt('.md'));
        $expected = [$base . DIRECTORY_SEPARATOR . 'one.txt'];
        $r = $finder->filter();
        $this->assertEquals($expected, $r);
    }

    public function testRecursing()
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR . 'data1';

        $finder = new Finder($base);
        $finder->addFileFilterPositive(new FileExt('.md'));
        $expected = [$base . DIRECTORY_SEPARATOR . 'two.md', $base . DIRECTORY_SEPARATOR . 'inner/innerthree.md'];
        $this->assertEquals($expected, $finder->filter());

        $finder = new Finder($base, '', null, null);
        $finder->addFileFilterNegative(new FileExt('.md'));
        $expected = [
            $base . DIRECTORY_SEPARATOR . 'one.txt', 
            $base . DIRECTORY_SEPARATOR . 'three.txt',
            $base . DIRECTORY_SEPARATOR . 'inner/innerone.txt', 
            $base . DIRECTORY_SEPARATOR . 'inner/innertwo.txt', 
        ];
        $r = $finder->filter();

        $base = __DIR__ . DIRECTORY_SEPARATOR . 'data1';
        $finder = new Finder($base, '', null, null);
        $finder->addFileFilterPositive(new FileNameStartsWith(['on', 'innero']));
        $finder->addFileFilterNegative(new FileExt('.md'));
        $expected = [$base . DIRECTORY_SEPARATOR . 'one.txt', $base . DIRECTORY_SEPARATOR . 'inner/innerone.txt'];
        $r = $finder->filter();
        $this->assertEquals($expected, $r);
    }

    public function testReturnFileinfo()
    {
        $base = __DIR__ . DIRECTORY_SEPARATOR . 'data1';

        $finder = new Finder($base);
        $finder->setRecurse(false);
        $finder->addFileFilterPositive(new FileExt('.md'));
        $expected = [new \SplFileInfo($base . DIRECTORY_SEPARATOR . 'two.md')];
        $actual = $finder->filter(true);
        $this->assertEquals($expected, $actual);

    }

    public function tearDown(): void
    {
    }
}