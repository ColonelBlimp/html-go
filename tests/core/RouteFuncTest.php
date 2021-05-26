<?php
namespace html_go;

use PHPUnit\Framework\TestCase;

class RouteFuncTest extends TestCase
{
    function testHomePageRoute(): void {
        $this->assertStringContainsString('404', route('GET', 'home'));
        $this->assertStringNotContainsString('404', route('GET', 'index'));
    }

    function testGetCategory(): void {
        $result = route('GET', 'category/uncategorized');
        $this->assertStringNotContainsString('404', $result);
    }

    function testGetCategoryLandingPage(): void {
        $result = route('GET', 'category/index');
        $this->assertStringNotContainsString('404', $result);
    }
    /*
    function testGetStaticPage(): void {
        $this->assertStringContainsString('404', route('GET', '/test/unknown'));
    }

    function testGetCategory(): void {
        $this->assertStringNotContainsString('404', route('GET', '/category/uncategorized'));
    }

    function testGetPost(): void {
        $this->assertStringNotContainsString('404', route('GET', '/2021/10/testered'));
    }
    */
}
