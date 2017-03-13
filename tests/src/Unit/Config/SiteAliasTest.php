<?php

namespace Radcliffe\Tests\Unit\Config\Larama;

use PHPUnit\Framework\TestCase;
use Radcliffe\Larama\Config\SiteAlias;

/**
 * Tests the site alias model.
 *
 * @group laraman
 */
class SiteAliasTest extends TestCase
{
    public function testSiteAlias()
    {
        $model = new SiteAlias('example', []);
        $this->assertInstanceOf('\Radcliffe\Larama\Config\SiteAlias', $model);
    }

    public function testGetAlias()
    {
        $model = new SiteAlias('example', []);
        $this->assertEquals('example', $model->getAlias());
    }

    public function testGetBaseDir()
    {
        $model = new SiteAlias('example', ['webroot' => '/var/www/example']);
        $this->assertEquals('/var/www/example', $model->getBaseDir());
    }

    public function testGetBaseURL()
    {
        $model = new SiteAlias('example', ['url' => 'http://example.com']);
        $this->assertEquals('http://example.com', $model->getBaseURL());
    }

    /**
     *
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidURL()
    {
        $model = new SiteAlias('example', ['url' => 'aaaa']);
    }

    public function testGetFQDN()
    {
        $model = new SiteAlias('example', ['fqdn' => 'www.example.com']);
        $this->assertEquals('www.example.com', $model->getFQDN());
    }
}
