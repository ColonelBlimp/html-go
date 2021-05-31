<?php declare(strict_types=1);
namespace html_go\indexing;

use PHPUnit\Framework\TestCase;

class IndexManagerTest extends TestCase
{
    function testInstantiation(): IndexManager {
        $manager = new IndexManager(TEST_DATA_ROOT);
        $this->assertNotNull($manager);
        return $manager;
    }
}
