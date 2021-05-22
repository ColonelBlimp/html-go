<?php declare(strict_types=1);
namespace html_go;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    function testGetPostContentListObject(): void {
        $content = get_content_list_object(POST_LIST_TYPE);
        $this->assertNotNull($content);
        $site = $content->getSite();
        $this->assertNotNull($site);
    }

    function testGetCatContentListObject(): void {
        $content = get_content_list_object(CAT_LIST_TYPE);
        $this->assertNotNull($content);
        $title = $content->getTitle();
        $this->assertEmpty($title);
    }

    function testGetTagContentListObject(): void {
        $content = get_content_list_object(TAG_LIST_TYPE);
        $this->assertNotNull($content);
        $desc = $content->getDescription();
        $this->assertEmpty($desc);
        $this->assertIsArray($content->getContentList());
    }

    function testUnknownListTypeException(): void {
        $this->expectException(\RuntimeException::class);
        get_content_list_object(3);
    }
}
