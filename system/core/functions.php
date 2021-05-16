<?php declare(strict_types=1);

use html_go\templating\TemplateEngine;
use html_go\templating\TwigTemplateEngine;

/**
 * Render the given template placing the given variables into the template context.
 * @param string $template
 * @param array<mixed> $vars
 * @return string
 */
function render(string $template, array $vars = []): string {
    return get_template_engine()->render($template, $vars);
}

/**
 * Helper function for 404 page.
 * @param string $title
 * @return string
 */
function not_found(string $title = '404 Not Found'): string {
    return render('404.html');
}

/**
 * Get the configured template engine.
 * @return TemplateEngine
 */
function get_template_engine(): TemplateEngine {
    static $engine = null;
    if (empty($engine)) {
        $themeName = config('', 'default');
        $engineName = config('template.engine', 'twig');

        $caching = false;
        if (\boolval(config('template.engine.caching', "false"))) {
            $caching = CACHE_ROOT.DS.'template_cache';
        }
        $options = [
            'cache' => $caching,
            'strict_variables' => \boolval(config('template.engine.twig.strict_variables', "true"))
        ];
        $templateDirs = [THEMES_ROOT.DS.$engineName.DS.$themeName];
        $engine = new TwigTemplateEngine($templateDirs, $options);
    }
    return $engine;
}

/**
 * Fetch a configuration value associated with the given key.
 * @param string $key
 * @param string $default
 * @throws \RuntimeException
 * @return string
 */
function config(string $key, string $default = null): string {
    static $config = [];
    if (empty($config)) {
        if (!\file_exists(CONFIG_ROOT.DS.'config.ini')) {
            throw new \RuntimeException('file_exists() failed'); // @codeCoverageIgnore
        }
        $config = \parse_ini_file(CONFIG_ROOT.DS.'config.ini');
    }
    if (!\array_key_exists($key, $config)) {
        if (!empty($default)) {
            return $default;
        }
        throw new \RuntimeException("Configuration key does not exist [$key]");
    }
    return $config[$key];
}
