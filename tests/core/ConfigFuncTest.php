<?php declare(strict_types=1);
namespace html_go;

use PHPUnit\Framework\TestCase;

class ConfigFuncTest extends TestCase
{
    function testTemplateEngineConfigs(): void {
        $this->assertStringContainsString('twig', config('template.engine'));
    }
}
