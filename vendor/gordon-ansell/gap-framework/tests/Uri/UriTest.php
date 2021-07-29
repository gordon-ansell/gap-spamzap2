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
namespace GreenFedora\Tests\Uri;

use PHPUnit\Framework\TestCase;
use GreenFedora\Uri\Uri;

/**
 * Tests for this package.
 */
final class UriTest extends TestCase
{    
    protected $uri = null;
    protected $dn = "http://meuser:mypassword@somedomain.com:8081/path1/path2/file.php?q1=1&q2=2#frag";

    public function setUp(): void
    {
        $this->uri = new Uri($this->dn);
    }

    public function testConstruction()
    {
        $this->assertEquals(new Uri('http://meuser:mypassword@somedomain.com:8081/path1/path2/file.php?q1=1&q2=2#frag'), 
            $this->uri);
    }

    public function testPartsAllocatedProperly()
    {
        $expected = [
            'scheme'    => 'http',
            'host'      => 'somedomain.com',
            'port'      => 8081,
            'user'      => 'meuser',
            'pass'      => 'mypassword',
            'path'      => '/path1/path2/file.php',
            'query'     => 'q1=1&q2=2',
            'fragment'  => 'frag'
        ];

        $this->assertEquals($expected, $this->uri->getParts());

    }

    public function testIsWebScheme()
    {
        $this->assertEquals(true, Uri::isWebScheme('http'));
        $this->assertEquals(false, Uri::isWebScheme('file'));
    }

    public function testGetBasePath()
    {
        $this->assertEquals('/path1/path2/file.php?q1=1&q2=2#frag', $this->uri->getBasePath());
    }

    public function testGetAuthority()
    {
        $this->assertEquals('meuser:mypassword@somedomain.com:8081', $this->uri->getAuthority());
    }

    public function testBaseUri()
    {
        $this->uri->setBaseUri("http://meuser:mypassword@somedomain.com:8081/path1");
        $this->assertEquals("http://meuser:mypassword@somedomain.com:8081/path1", $this->uri->getBaseUri());
    }

    public function testGetRelativeUri()
    {
        $this->uri->setBaseUri("http://meuser:mypassword@somedomain.com:8081/path1");
        $this->assertEquals("/path2/file.php?q1=1&q2=2#frag", $this->uri->getRelative());
    }

    public function testToString()
    {
        $this->assertEquals("http://meuser:mypassword@somedomain.com:8081/path1/path2/file.php?q1=1&q2=2#frag",
            strval($this->uri));
    }

    public function testAccessAsProperty()
    {
        $this->assertEquals('/path1/path2/file.php', $this->uri->path);
        $this->assertEquals('meuser:mypassword@somedomain.com:8081', $this->uri->authority);
    }

}