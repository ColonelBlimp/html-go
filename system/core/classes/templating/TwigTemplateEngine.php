<?php declare(strict_types=1);
namespace html_go\templating;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigTemplateEngine implements TemplateEngine
{
    private Environment $engine;

    /**
     * TwigTemplateEngine Constructor.
     * @param array<mixed> $templateDirs
     * @param array<mixed> $options
     */
    function __construct(array $templateDirs, array $options) {
        $loader = new FilesystemLoader($templateDirs);
        $this->engine = new Environment($loader, $options);
    }

    function render($template, array $vars): string {
        return 'twig template engine: ' . $template;
    }
}
