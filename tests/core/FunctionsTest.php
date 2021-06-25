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

    /**
     * @runInSeparateProcess
     */
    function testGetTagsFunc(): void {
        $tags = get_tags();
        $this->assertEmpty($tags);

        $src = \realpath(__DIR__.DS.'..'.DS.'test-data'.DS.'func-data');
        $dst = \realpath(APP_ROOT.DS.'content'.DS.'user-data');
        $this->copyTestData($src, $dst);
        get_index_manager()->reindex();
        $tags = get_tags(perPage: 4);
        $this->assertNotEmpty($tags);
        $this->assertCount(4, $tags);
    }

    /**
     * @depends testGetTagsFunc
     */
    function testSlugExists(): void {
        $this->assertTrue(slug_exists('2021/06/this-is-the-title'));
    }

    function testI18n(): void {
        $this->assertSame('en', get_config()->getString(Config::KEY_LANG));
        $this->assertNotNull(get_i18n());
        $obj = get_i18n();
        $this->assertNotNull($obj);
        $this->assertSame('No tags found', $obj->getText('no_tags_found'));
    }

    /**
     * @runInSeparateProcess
     */
    function testGetCategoriesFunc(): void {
        $cats = get_categories(perPage: 1);
        $this->assertNotEmpty($cats);
        $this->assertCount(1, $cats);
    }

    /**
     * @runInSeparateProcess
     */
    function testGetPostsFunc(): void {
        $posts = get_posts();
        $this->assertNotEmpty($posts);
        $this->assertCount(1, $posts);
    }

    private function copyTestData(string $src, string $dst, string $childFolder = '') {
        $dir = \opendir($src);
        @mkdir($dst);
        if ($childFolder!='') {
            @mkdir($dst.'/'.$childFolder);

            while (false !== ( $file = \readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( \is_dir($src . '/' . $file) ) {
                        $this->copyTestData($src . '/' . $file,$dst.'/'.$childFolder . '/' . $file);
                    }
                    else {
                        \copy($src . '/' . $file, $dst.'/'.$childFolder . '/' . $file);
                    }
                }
            }
        } else {
            while (false !== ($file = \readdir($dir)) ) {
                if (( $file != '.' ) && ( $file != '..' )) {
                    if ( \is_dir($src . '/' . $file) ) {
                        $this->copyTestData($src . '/' . $file,$dst . '/' . $file);
                    }
                    else {
                        \copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
        }
        \closedir($dir);
    }
/*
    private function copyTestData(): void {
        $os = \php_uname('s');
        if (\str_starts_with(\strtoupper($os), 'WIN')) {
            $src = \realpath(__DIR__.DS.'..'.DS.'test-data'.DS.'func-data');
            $dst = \realpath(APP_ROOT.DS.'content'.DS.'user-data');
            $cmd = 'xcopy '.$src.' /E /Y /Q '.$dst;
        } else {
            $cmd = '';
        }
        \shell_exec($cmd);
    }
*/
}
