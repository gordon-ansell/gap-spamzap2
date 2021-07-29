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
namespace GreenFedora\Tests\StdLib\Env;

use PHPUnit\Framework\TestCase;
use GreenFedora\StdLib\Env\Env;
use GreenFedora\StdLib\Env\Exception\RuntimeException;

/**
 * Tests for this package.
 */
final class EnvTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testEnvironmentVariables()
    {
        $env = new Env(__DIR__);
        $data = $env->getData();

        $this->assertEquals("Hello how are = you", $data['STR']);
        $this->assertIsString($data['STR']);
        $this->assertIsString($data['FORCEDSTR']);
        $this->assertIsBool($data['BOOL']);
        $this->assertIsInt($data['INT']);
        $this->assertIsFloat($data['FLOAT']);
    }

    public function testEnsureWeCannotOverwrite()
    {
        $this->expectException(RuntimeException::class);
        new Env(__DIR__, '.env2', false);
    }

    public function tearDown(): void
    {
    }
}