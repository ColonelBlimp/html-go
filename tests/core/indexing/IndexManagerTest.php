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

    /**
     * @depends testInstantiation
     */
    function testLandingPages(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertTrue($manager->elementExists(CAT_INDEX_KEY));
        $this->assertTrue($manager->elementExists(HOME_INDEX_KEY));
        $this->assertTrue($manager->elementExists(BLOG_INDEX_KEY));
    }
}
