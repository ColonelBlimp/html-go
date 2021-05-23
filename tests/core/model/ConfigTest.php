<?php declare(strict_types=1);
namespace html_go\model;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    function testInstantiation(): void {
        $this->assertNotNull(new Config(TEST_APP_ROOT.DS.'core'.DS.'test-data'.DS.'config'));
    }

    function testIniFileNotFoundException(): void {
        $this->expectException(\InvalidArgumentException::class);
        new Config('');
    }

    function testInvalidConfigException(): void {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Configuration option 'site.url' not set.");
        new Config(TEST_APP_ROOT.DS.'core'.DS.'test-data'.DS.'config'.DS.'bad');
    }

    function testGetString(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'core'.DS.'test-data'.DS.'config');
        $this->assertEmpty($cfg->getString('unknown'));
        $this->assertSame("http://localhost:8000", $cfg->getString('site.url'));
    }

    function testGetInt(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'core'.DS.'test-data'.DS.'config');
        $this->assertSame(-1, $cfg->getInt('unknown'));
        $this->assertSame(8000, $cfg->getInt('var.int'));
    }

    function testGetBool(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'core'.DS.'test-data'.DS.'config');
        $this->assertSame(false, $cfg->getBool('unknown'));
        $this->assertSame(true, $cfg->getBool('var.bool'));
    }
}