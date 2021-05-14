<?php
namespace html_go\templating;

final class TwigTemplateEngine implements TemplateEngine
{

    function __construct() {

    }

    function render($template, array $vars): string {
        return 'twig template engine';
    }
}
