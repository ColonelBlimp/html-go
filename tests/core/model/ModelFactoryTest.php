<?php declare(strict_types=1);
namespace html_go\model;

use PHPUnit\Framework\TestCase;
use html_go\indexing\IndexManager;
use html_go\markdown\ParsedownParser;

class ModelFactoryTest extends TestCase
{
    function testContentObject(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $parser = new ParsedownParser();
        $manager = new IndexManager(TEST_DATA_ROOT);
        $factory = new ModelFactory($cfg, $parser, $manager);
        $this->assertTrue($manager->elementExists(CATEGORY_SECTION));
        $element = $manager->getElementFromSlugIndex(CATEGORY_SECTION);
        $content = $factory->createContentObject($element);
        $this->assertTrue(isset($content->key));
        $this->assertSame(CATEGORY_SECTION, $element->key);
        $this->assertTrue(isset($content->title));
        $this->assertSame('Categories', $content->title);
        $this->assertTrue(isset($content->description));
        $this->assertSame('Categories list', $content->description);
        $this->assertIsArray($content->tags);
        $this->assertIsArray($content->list);
        $this->assertTrue(isset($content->site));
        $this->assertTrue(isset($content->site->url));
        $this->assertSame('http://localhost:8000', $content->site->url);
        $this->assertTrue(isset($content->site->title));
        $this->assertSame(' | HTML-go', $content->site->title);
        $this->assertTrue(isset($content->site->description));
        $this->assertSame('Powered by HTML-go, a databaseless, flat-file blogging platform', $content->site->description);
        $this->assertTrue(isset($content->site->tagline));
        $this->assertSame('Another HTML-go website', $content->site->tagline);
        $this->assertTrue(isset($content->site->copyright));
        $this->assertSame('(c) Copyright, Your Name', $content->site->copyright);
    }

    function testManualSummary(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $parser = new ParsedownParser();
        $manager = new IndexManager(TEST_DATA_ROOT);
        $factory = new ModelFactory($cfg, $parser, $manager);
        $this->assertTrue($manager->elementExists('2021/01/harvest-time'));
        $element = $manager->getElementFromSlugIndex('2021/01/harvest-time');
        $content = $factory->createContentObject($element);
        $this->assertSame('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', $content->summary);
    }

    function testFrontMatterSummary(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $parser = new ParsedownParser();
        $manager = new IndexManager(TEST_DATA_ROOT);
        $factory = new ModelFactory($cfg, $parser, $manager);
        $this->assertTrue($manager->elementExists('2021/01/tested-quote'));
        $element = $manager->getElementFromSlugIndex('2021/01/tested-quote');
        $content = $factory->createContentObject($element);
        $this->assertSame('Something different.', $content->summary);
    }
}
