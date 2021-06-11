<?php declare(strict_types=1);
namespace html_go\templating;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class TwigTemplateEngine implements TemplateEngine
{
    private Environment $engine;
    private string $ext;

    /**
     * TwigTemplateEngine Constructor.
     * @param array<mixed> $templateDirs
     * @param array<mixed> $options
     * @param string $ext the template file extension
     */
    function __construct(array $templateDirs, array $options, string $ext = 'twig') {
        $loader = new FilesystemLoader($templateDirs);
        $this->engine = new Environment($loader, $options);
        $this->ext = $ext;
    }

    function render($template, array $vars): string {
        return $this->engine->render($template.'.'.$this->ext, $vars);
    }
}
