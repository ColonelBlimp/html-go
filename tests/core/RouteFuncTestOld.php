<?php
namespace html_go;

use PHPUnit\Framework\TestCase;

class RouteFuncTestOld extends TestCase
{
    function testIndexRoute(): void {
        $this->assertIsString(route('GET', 'home'));
    }

    function testGetStaticPage(): void {
        $this->assertStringContainsString('404', route('GET', '/test/unknown'));
    }

    function testGetCategory(): void {
        $this->assertIsString(route('GET', '/category/uncategorized'));
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
