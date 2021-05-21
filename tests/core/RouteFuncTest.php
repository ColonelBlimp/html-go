<?php
namespace html_go;

use PHPUnit\Framework\TestCase;

class RouteFuncTest extends TestCase
{
    function testIndexRoute(): void {
        $this->assertIsString(route('GET', 'index'));
    }

    function testGetStaticPage(): void {
        $this->assertStringContainsString('404', route('GET', '/test/unknown'));
    }
/*
    function testGetCategory(): void {
        $this->assertIsString(route('GET', '/category/uncategorized'));
        $this->assertStringContainsString('404', route('GET', '/category/unknown'));
    }

    function testGetTag(): void {
        $this->assertStringContainsString('404', route('GET', '/tag/android-development'));
    }

    function testGetPost(): void {
        $this->assertStringContainsString('404', route('GET', '/2021/04/honey-processing'));
    }
*/
}
