<?php
namespace html_go;

use PHPUnit\Framework\TestCase;

class RouteFuncTest extends TestCase
{
    function testIndexRoute(): void {
        $this->assertIsString(route('GET', 'index'));
    }

    function testGetStaticPage(): void {
        $this->assertIsString(route('GET', '/test/unknown'));
    }

    function testGetCategory(): void {
        $this->assertIsString(route('GET', '/category/uncategorized'));
    }

    function testGetTag(): void {
        $this->assertIsString(route('GET', '/tag/android-development'));
    }

    function testGetPost(): void {
        $this->assertIsString(route('GET', '/2021/04/honey-processing'));
    }
}
