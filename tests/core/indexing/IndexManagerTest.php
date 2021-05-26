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

    /**
     * @depends testInstantiation
     */
    function testLandingPages(IndexManager $manager): void {
        $this->assertTrue($manager->elementExists('posts/index'));
        $this->assertTrue($manager->elementExists('category/index'));
        $this->assertTrue($manager->elementExists('tag/index'));
        $this->assertTrue($manager->elementExists('index'));
        $element = $manager->getElementFromSlugIndex('index');
        $this->assertNotNull($element);
        $this->assertTrue(isset($element->key));
        $this->assertSame('index', $element->key);
        $this->assertTrue(isset($element->path));
        $this->assertTrue(\str_ends_with($element->path, 'common'.DS.'pages'.DS.'index.md'));
        $this->assertTrue(isset($element->section));
        $this->assertTrue(isset($element->type));
        $this->assertTrue(isset($element->username));
        $this->assertTrue(isset($element->date));
        $this->assertIsArray($element->tags);
    }

    /**
     * @depends testInstantiation
     */
    function testGetCategoriesIndex(IndexManager $manager): void {
        $this->assertNotNull($manager->getCategoriesIndex());
        $this->assertIsArray($manager->getCategoriesIndex());
    }


}
