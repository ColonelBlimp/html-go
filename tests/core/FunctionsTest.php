<?php declare(strict_types=1);
namespace html_go;

use PHPUnit\Framework\TestCase;
use html_go\model\Config;

class FunctionsTest extends TestCase
{
    function testConfigFunctions(): void {
        $this->assertIsInt(get_config()->getInt(Config::KEY_POSTS_PERPAGE));
        $this->assertSame(5, get_config()->getInt(Config::KEY_POSTS_PERPAGE));
    }
}
