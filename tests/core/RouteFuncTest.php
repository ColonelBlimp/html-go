<?php
namespace html_go;

use PHPUnit\Framework\TestCase;

class RouteFuncTest extends TestCase
{
    function testIndexRoute(): void {
        $this->assertIsString(route('GET', 'index'));
    }
/*
    function testUnknownRoute(): void {
        $this->assertStringContainsString('404', route('GET', '/test/unknown'));
    }

    function testRegisterTokenizedGetRoute(): void {
        get(':static', function(string $static): string {
            return $static . '.html';
        });
        $this->assertStringContainsString('testing.html', route('GET', 'http://localhost/testing'));
    }
*/
}
