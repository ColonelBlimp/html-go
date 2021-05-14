<?php declare(strict_types=1);
namespace html_go\templating;

interface TemplateEngine
{
    /**
     * Render the given template placing the given array of variables into the template's context.
     * @param string $template
     * @param array<mixed> $vars
     * @return string
     */
    function render(string $template, array $vars): string;
}
