<?php declare(strict_types=1);
namespace html_go;

use PHPUnit\Framework\TestCase;

class DispatcherFuncTest extends TestCase
{
    function testParseUrlWithQuery(): void {
        $this->assertStringContainsString("http://localhost", strip_url_parameters('http://localhost?test=1&next=wibble&id=none'));
        $params = parse_query();
        $this->assertIsArray($params);
        $this->assertCount(3, $params);
        $this->assertSame('1', get_query_parameter('test'));
        $this->assertSame('wibble', get_query_parameter('next'));
        $this->assertSame('none', get_query_parameter('id'));
    }

    /**
     * @runInSeparateProcess
     */
    function testParseUrlWithoutQuery(): void {
        $this->setPreserveGlobalState(false);
        $this->assertStringContainsString("http://localhost/2020/10/post_title", strip_url_parameters('http://localhost/2020/10/post_title'));
        $params = parse_query();
        $this->assertIsArray($params);
        $this->assertEmpty($params);
        $this->assertNull(get_query_parameter('test'));
    }
}
