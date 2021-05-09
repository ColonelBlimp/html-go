<?php
namespace html_go;

use PHPUnit\Framework\TestCase;

class RouteFuncTest extends TestCase
{
    function testIndexRoute(): void {
        route('GET', '/index', function(): string {
            return render('main.html');
        });

        $retval = route('GET', '/index');
        $this->assertIsString($retval);
    }
}
