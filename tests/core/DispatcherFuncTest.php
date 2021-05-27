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
        $this->assertSame(get_query_parameter('test'), '1');
        $this->assertSame(get_query_parameter('next'), 'wibble');
        $this->assertSame(get_query_parameter('id'), 'none');
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
        $this->assertNull(get_query_parameter('test'), '1');
    }

    function testLandingPages(): void {
        $result = dispatch('/');
        $this->assertStringNotContainsString('404', $result);
        $result = dispatch('/category');
        $this->assertStringNotContainsString('404', $result);
        $result = dispatch('/tag');
        $this->assertStringNotContainsString('404', $result);
        $result = dispatch('/blog');
        $this->assertStringNotContainsString('404', $result);
    }

    function testPagination(): void {
        $result = dispatch('/?page=1');
        $this->assertStringNotContainsString('404', $result);
    }
}
