<?php declare(strict_types=1);
namespace html_go\indexing;

use PHPUnit\Framework\TestCase;

final class IndexManagerTest extends TestCase
{
    function testInstantiation(): IndexManager {
        $manager = new IndexManager(TEST_DATA_ROOT);
        $this->assertNotNull($manager);
        return $manager;
    }

    function testInvalidContentDirException(): void {
        $this->expectException(\InvalidArgumentException::class);
        new IndexManager('unknown');
    }

    /**
     * @depends testInstantiation
     */
    function testIndexPageSlugs(IndexManager $manager): void {
        $this->assertNotNull($manager);
        $this->assertTrue($manager->elementExists('index'));
        $this->assertTrue($manager->elementExists('category/index'));
    }

    /**
     * @depends testInstantiation
     */
    function testSlugIndexException(IndexManager $manager): void {
        $this->expectException(\RuntimeException::class);
        $manager->getElementFromSlugIndex('unknown_slug');
    }

    /**
     * @depends testInstantiation
     */
    function testGetPostIndex(IndexManager $manager): void {
        $this->assertNotNull($manager->getPostsIndex());
        $this->assertIsArray($manager->getPostsIndex());
    }
}
