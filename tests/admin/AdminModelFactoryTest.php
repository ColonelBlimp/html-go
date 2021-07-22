<?php declare(strict_types=1);
namespace html_go;

use PHPUnit\Framework\TestCase;
use html_go\indexing\IndexManager;
use html_go\markdown\ParsedownParser;
use html_go\model\Config;
use html_go\model\ModelFactory;

class AdminModelFactoryTest extends TestCase
{

    function testAdminContentTitleMissing(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The 'title:' parameter has not been set!");
        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $parser = new ParsedownParser();
        $manager = new IndexManager(TEST_DATA_ROOT);
        $factory = new ModelFactory($cfg, $parser, $manager);
        $factory->createAdminContentObject([]);
    }

    function testAdminContentObjectNoList(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $parser = new ParsedownParser();
        $manager = new IndexManager(TEST_DATA_ROOT);
        $factory = new ModelFactory($cfg, $parser, $manager);
        $content = $factory->createAdminContentObject(
            ['template' => 'dashboard.html', 'context' => 'admin', 'title' => 'Test Title', 'section' => CATEGORY_SECTION]);
        $this->assertNotNull($content);
        $this->assertEmpty($content->list);
        $this->assertIsArray($content->list);
    }

    function testAdminContentObjectWithList(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $parser = new ParsedownParser();
        $manager = new IndexManager(TEST_DATA_ROOT);
        $factory = new ModelFactory($cfg, $parser, $manager);
        $content = $factory->createAdminContentObject(
            ['template' => 'dashboard.html', 'context' => 'admin', 'title' => 'Test Title', 'list' => ['a', 'b'], 'section' => CATEGORY_SECTION]);
        $this->assertNotNull($content);
        $this->assertNotEmpty($content->list);
        $this->assertIsArray($content->list);
    }
}
