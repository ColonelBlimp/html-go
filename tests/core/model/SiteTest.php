<?php declare(strict_types=1);
namespace html_go\model;

use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{
    function testInstantiation(): Site {
        $cfg = new Config(TEST_APP_ROOT.DS.'core'.DS.'test-data'.DS.'config');
        $this->assertNotNull($cfg);
        $site = new Site($cfg);
        $this->assertNotNull($site);
        return $site;
    }

    /**
     * @depends testInstantiation
     */
    function testGet(Site $site): void {
        $this->assertNotNull($site);
        $this->assertSame('http://localhost:8000', $site->getUrl());
        $this->assertSame('HTML-go', $site->getTitle());
        $this->assertSame('Powered by HTML-go, a databaseless, flat-file blogging platform', $site->getDescription());
        $this->assertSame('Another HTML-go Site', $site->getTagline());
        $this->assertSame('(c) Copyright, Your Name', $site->getCopyright());
    }
}
