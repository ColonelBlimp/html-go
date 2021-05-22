<?php declare(strict_types=1);
namespace html_go\indexing;

use PHPUnit\Framework\TestCase;

abstract class IndexingTestCase extends TestCase
{
    const APP_ROOT = __DIR__.DS.'..'.DS.'test-data';

    static function setUpBeforeClass(): void {
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'cache'.DS.'indexes'.DS.'slugindex.inx');
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'cache'.DS.'indexes'.DS.'cat2posts.inx');
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'cache'.DS.'indexes'.DS.'posts.inx');
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'cache'.DS.'indexes'.DS.'pages.inx');
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'cache'.DS.'indexes'.DS.'categories.inx');
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'cache'.DS.'indexes'.DS.'tags.inx');
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'cache'.DS.'indexes'.DS.'tag2posts.inx');
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'content'.DS.'user-data'.DS.'@testuser'.DS.'posts'.DS.'harvesting'.DS.'regular'.DS.'2021010100000__s.md');
        @unlink(\realpath(IndexingTestCase::APP_ROOT).DS.'content'.DS.'user-data'.DS.'@testuser'.DS.'posts'.DS.'harvesting'.DS.'regular'.DS.'20210101000000_wibble.md');
        @rmdir(\realpath(IndexingTestCase::APP_ROOT).DS.'cache'.DS.'indexes');
    }
}
