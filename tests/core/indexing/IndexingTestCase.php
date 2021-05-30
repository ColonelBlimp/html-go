<?php declare(strict_types=1);
namespace html_go\indexing;

use PHPUnit\Framework\TestCase;

abstract class IndexingTestCase extends TestCase
{
    static function setUpBeforeClass(): void {
        @unlink(\realpath(TEST_DATA_ROOT.DS.'content'.DS.'user-data'.DS.'@testuser'.DS.'posts'.DS.'harvesting'.DS.'regular'.DS.'2021010100000__s'.CONTENT_FILE_EXT));
        @unlink(\realpath(TEST_DATA_ROOT.DS.'content'.DS.'user-data'.DS.'@testuser'.DS.'posts'.DS.'harvesting'.DS.'regular'.DS.'20210101000000_wibble'.CONTENT_FILE_EXT));
    }
}
