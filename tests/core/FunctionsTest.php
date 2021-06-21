<?php declare(strict_types=1);
namespace html_go;

use PHPUnit\Framework\TestCase;
use html_go\indexing\IndexManager;
use html_go\markdown\ParsedownParser;
use html_go\model\Config;
use html_go\model\ModelFactory;

class FunctionsTest extends TestCase
{
    function testConfigFunctions(): void {
        $this->assertIsInt(get_config()->getInt(Config::KEY_POSTS_PERPAGE));
        $this->assertSame(5, get_config()->getInt(Config::KEY_POSTS_PERPAGE));
    }

    function testGetTags(): void {
        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $parser = new ParsedownParser();
        $manager = new IndexManager(TEST_DATA_ROOT);
        $factory = new ModelFactory($cfg, $parser, $manager);

        $tagIndex = $manager->getTagIndex();
        $this->assertNotEmpty($tagIndex);

        $list = [];
        foreach ($tagIndex as $obj) {
            $list[] = $factory->createContentObject($obj);
        }
    }
}
