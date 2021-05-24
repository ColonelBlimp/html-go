<?php
namespace html_go;

use PHPUnit\Framework\TestCase;

class RouteFuncTest extends TestCase
{
    function testIndexRoute(): void {
        $this->assertStringContainsString('404', route('GET', 'home'));
    }

    function testGetStaticPage(): void {
        $this->assertStringContainsString('404', route('GET', '/test/unknown'));
    }

    function testGetCategory(): void {
        $this->assertStringNotContainsString('404', route('GET', '/category/uncategorized'));
    }

    function testGetPost(): void {
        $this->assertStringNotContainsString('404', route('GET', '/2021/10/testered'));
    }
/*
    function testGetTag(): void {
        $this->assertStringContainsString('404', route('GET', '/tag/android-development'));
    }

    function testGetPost(): void {
        $this->assertStringContainsString('404', route('GET', '/2021/04/honey-processing'));
    }
*/
}
