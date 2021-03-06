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

    function testInstantiationException(): void {
        $this->expectException(\InvalidArgumentException::class);
        new IndexManager(__DIR__);
    }

    /**
     * @depends testInstantiation
     */
    function testLandingPages(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertTrue($manager->elementExists(CATEGORY_SECTION));
        $this->assertTrue($manager->elementExists(HOME_INDEX_KEY));
        $this->assertTrue($manager->elementExists(POST_INDEX_KEY));
    }

    /**
     * @depends testInstantiation
     */
    function testCategoryIndex(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertIsArray($manager->getCategoriesIndex());
        $this->assertCount(2, $manager->getCategoriesIndex());
    }

    /**
     * @depends testInstantiation
     */
    function testPageIndex(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertIsArray($manager->getPageIndex());
        $this->assertCount(9, $manager->getPageIndex());
    }

    /**
     * @depends testInstantiation
     */
    function testTagIndex(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertIsArray($manager->getTagIndex());
        $this->assertCount(3, $manager->getTagIndex());
    }

    /**
     * @depends testInstantiation
     */
    function testPostIndex(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertIsArray($manager->getPostsIndex());
        $this->assertCount(3, $manager->getPostsIndex());
    }

    /**
     * @depends testInstantiation
     */
    function testMenuIndex(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertIsArray($manager->getMenusIndex());
        $this->assertCount(2, $manager->getMenusIndex());
    }

    /**
     * @depends testInstantiation
     */
    function testElementExists(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertFalse($manager->elementExists('unknown'));
        $this->assertTrue($manager->elementExists(CAT_INDEX_KEY.FWD_SLASH.'harvesting'));
    }

    /**
     * @depends testInstantiation
     */
    function testGetElementException(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->expectException(\InvalidArgumentException::class);
        $manager->getElementFromSlugIndex(CAT_INDEX_KEY.FWD_SLASH.'wibble');
    }

    /**
     * @depends testInstantiation
     */
    function testPostsForCategoryIndex(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $index = $manager->getPostsForCategoryIndex();
        $this->assertNotEmpty($index);
        $list = $index[CAT_INDEX_KEY.FWD_SLASH.'uncategorized'];
        $this->assertIsArray($list);
    }

    /**
     * @depends testInstantiation
     */
    function testPostsForTagIndex(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $index = $manager->getPostsForTagIndex();
        $this->assertNotEmpty($index);
        $list = $index[TAG_INDEX_KEY.FWD_SLASH.'tagone'];
        $this->assertIsArray($list);
    }
}
