<?php declare(strict_types=1);
namespace html_go;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    function testConfigFunctions(): void {
        $this->assertIsInt(get_config_int('posts.per_page'));
        $this->assertSame(5, get_config_int('posts.per_page'));
    }
}
