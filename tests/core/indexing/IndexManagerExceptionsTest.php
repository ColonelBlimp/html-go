<?php declare(strict_types=1);
namespace html_go\indexing;

use PHPUnit\Framework\TestCase;

class IndexManagerExceptionsTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    function testExceptions(): IndexManager {
        $manager = new IndexManager(TEST_DATA_ROOT);
        $this->assertNotNull($manager);
        return $manager;
    }

    /**
     * @runInSeparateProcess
     * @depends testExceptions
     */
    function testShortFilenameException(IndexManager $manager): void {
        $this->assertTrue(touch(TEST_DATA_ROOT.DS.'content'.DS.'user-data'.DS.'@testuser'.DS.'posts'.DS.'harvesting'.DS.'regular'.DS.'2021010100000__s'.CONTENT_FILE_EXT));
        $this->expectException(\InvalidArgumentException::class);
        $manager->reindex();
    }

    /**
     * @runInSeparateProcess
     * @depends testExceptions
     */
    function testSyntaxFilenameException(IndexManager $manager): void {
        $this->assertTrue(touch(TEST_DATA_ROOT.DS.'content'.DS.'user-data'.DS.'@testuser'.DS.'posts'.DS.'harvesting'.DS.'regular'.DS.'20210101000000_wibble'.CONTENT_FILE_EXT));
        $this->expectException(\InvalidArgumentException::class);
        $manager->reindex();
    }
}
