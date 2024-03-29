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
     * @param string $ext the template file extension. Default is <code>twig</code>.
     * @param array<mixed> $globals
     */
    public function __construct(array $templateDirs, array $options, string $ext = 'twig', array $globals = []) {
        $loader = new FilesystemLoader($templateDirs);
        $this->engine = new Environment($loader, $options);
        foreach ($globals as $name => $value) {
            $this->engine->addGlobal($name, $value);
        }
        $this->ext = $ext;
    }

    public function render($template, array $vars): string {
        return $this->engine->render($template.'.'.$this->ext, $vars);
    }
}
