<?php declare(strict_types=1);
namespace html_go\model;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    function testInstantiation(): void {
        $this->assertNotNull(new Config(TEST_APP_ROOT.DS.'core'.DS.'config'));
    }

    function testIniFileNotFoundException(): void {
        $this->expectException(\InvalidArgumentException::class);
        new Config('');
    }
}
