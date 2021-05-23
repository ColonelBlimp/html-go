<?php declare(strict_types=1);
namespace html_go\model;

use PHPUnit\Framework\TestCase;

final class ContentTest extends TestCase
{
    function testInstantiation(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $this->assertNotNull($cfg);
        $site = new Site($cfg);
        $this->assertNotNull($site);
        $content = new Content($site, new \stdClass());
        $this->assertNotNull($content);

        $this->assertIsArray($content->getMenus());
        $this->assertEmpty($content->getMenus());
        $this->assertIsArray($content->getListing());
        $this->assertEmpty($content->getListing());
        $this->assertSame(EMPTY_VALUE, $content->getRawBody());
    }
}
