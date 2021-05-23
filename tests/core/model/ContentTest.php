<?php declare(strict_types=1);
namespace html_go\model;

use PHPUnit\Framework\TestCase;
use html_go\indexing\Element;

final class ContentTest extends TestCase
{
    function testInstantiation(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'core'.DS.'test-data'.DS.'config');
        $this->assertNotNull($cfg);
        $site = new Site($cfg);
        $this->assertNotNull($site);
        $content = new Content($site, Element::createEmpty(), [
                        ModelFactory::KEY_MENUS_DATA => [],
                        ModelFactory::KEY_LIST_DATA => [],
                        ModelFactory::KEY_BODY_DATA => '',
                        ModelFactory::KEY_DESC_DATA => '',
                        ModelFactory::KEY_TITLE_DATA => ''
        ]);
        $this->assertNotNull($content);

        $this->assertIsArray($content->getMenus());
        $this->assertEmpty($content->getMenus());
        $this->assertIsArray($content->getContentList());
        $this->assertEmpty($content->getContentList());
        $this->assertEmpty($content->getRawBody());
    }
}