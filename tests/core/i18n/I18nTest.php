<?php declare(strict_types=1);
namespace html_go\i18n;

use PHPUnit\Framework\TestCase;

class I18nTest extends TestCase
{
    function testI18n(): void {
        $instance = new I18n(LANG_ROOT.DS.'en.messages.php');
        $this->assertSame("Oops! We can't find that!", $instance->getText('404_site_title'));
        $this->assertSame("!404_main!", $instance->getText('404_main'));
    }

    function testInvalidBundlePath(): void {
        $this->expectException(\InvalidArgumentException::class);
        new I18n(LANG_ROOT.DS.'en.message.php');
    }
}
