<?php declare(strict_types=1);

use html_go\templating\TemplateEngine;
use html_go\templating\TwigTemplateEngine;
use html_go\i18n\i18n;
use html_go\model\ContentInterface;
use html_go\model\Content;

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
    $vars = get_template_vars();
    $vars['site_title'] = $title . $vars['site_title'];
    return render('404.html', $vars);
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
        $strict_vars = true;
        if (config('template.engine.caching', 'false') === 'true') {
            $caching = CACHE_ROOT.DS.'template_cache';
        }
        if (config('template.engine.twig.strict_variables', 'true') === 'false') {
            $strict_vars = false;
        }
        $options = [
            'cache' => $caching,
            'strict_variables' => $strict_vars
        ];
        $templateDirs = [THEMES_ROOT.DS.$engineName.DS.$themeName];
        $engine = new TwigTemplateEngine($templateDirs, $options);
    }
    return $engine;
}

/**
 * Returns all the variables to be placed into a template's context.
 * @param array<mixed> $vars user defined variables to be added
 * @return array<mixed>
 */
function get_template_vars(array $vars = []): array {
    static $site_vars = [];
    if (empty($site_vars)) {
        $site_vars = [
            'i18n' => new i18n(LANG_ROOT.DS.config('site.language', 'en').'.messages.php'),
            'site_title' => config('site.title', "HTML-go"),
            'site_description' => config('site.description', "Another HTML-go website"),
            'site_copyright' => config('site.copyright', "&#169; Copyright ????.")
        ];
    }
    return \array_merge($site_vars, $vars);
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

function get_page(string $slug): ?ContentInterface {
    return new Content();
}
