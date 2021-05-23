<?php declare(strict_types=1);
namespace html_go\model;

use PHPUnit\Framework\TestCase;

class ModelFactoryTest extends TestCase
{
    function testCreat(): void {
        $obj = new \stdClass();
        $obj->key = 'key';
        $obj->path = __DIR__;
        $obj->section = 'post';
        $obj->category = 'uncategorized';
        $obj->type = 'post';
        $obj->tags = [];

        $cfg = new Config(TEST_APP_ROOT.DS.'test-data'.DS.'config');
        $factory = new ModelFactory($cfg);
        $this->assertNotNull($factory);
        $content = $factory->create($obj);
//        print_r($content);
    }
}
