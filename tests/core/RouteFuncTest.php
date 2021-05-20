<?php
namespace html_go;

use PHPUnit\Framework\TestCase;

class RouteFuncTest extends TestCase
{
    function testIndexRoute(): void {
        $this->assertIsString(route('GET', 'index'));
    }

    function testCatchAllRoute(): void {
        $this->assertIsString(route('GET', '/test/unknown'));
    }

    function testTokenizedGetRoute(): void {
        $this->assertIsString(route('GET', '/category/uncategorized'));
    }

    function testGetPost(): void {
        $this->assertIsString(route('GET', '/2021/04/honey-processing'));
    }
}
