<?php declare(strict_types=1);

use html_go\i18n\i18n;
use html_go\indexing\IndexManager;
use html_go\model\Content;
use html_go\templating\TemplateEngine;
use html_go\templating\TwigTemplateEngine;
use html_go\model\Config;

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
        $themeName = get_config_string('theme.name', 'default');
        $engineName = get_config_string('template.engine', 'twig');

        $caching = false;
        $strict_vars = true;
        if (get_config_bool('template.engine.caching', false)) {
            $caching = CACHE_ROOT.DS.'template_cache';
        }
        if (get_config_bool('template.engine.twig.strict_variables', true) === false) {
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
            'i18n' => new i18n(LANG_ROOT.DS.get_config_string('site.language', 'en').'.messages.php'),
            'site_title' => get_config_string('site.title', "HTML-go"),
            'site_description' => get_config_string('site.description', "Another HTML-go website"),
            'site_copyright' => get_config_string('site.copyright', "&#169; Copyright ????.")
        ];
    }
    return \array_merge($site_vars, $vars);
}

/**
 * Returns the index manager.
 * @return IndexManager
 */
function get_index_manager(): IndexManager {
    static $manager = null;
    if ($manager === null) {
        $manager = new IndexManager(APP_ROOT);
    }
    return $manager;
}

function get_config(): Config {
    static $config = null;
    if (empty($config)) {
        $config = new Config(CONFIG_ROOT);
    }
    return $config;
}

function get_config_string(string $key, string $default = ''): string {
    return get_config()->getString($key, $default);
}

function get_config_int(string $key, int $default = -1): int {
    return get_config()->getInt($key, $default);
}

function get_config_bool(string $key, bool $default = false): bool {
    return get_config()->getBool($key, $default);
}

function get_post(string $year, string $month, string $title): ?Content {
    echo __FUNCTION__ . ': ' . $title . PHP_EOL;
    return null;
}

/**
 * Get a page.
 * @param string $slug
 * @return Content|NULL
 */
function get_page(string $slug): ?Content {
    echo __FUNCTION__ . ': ' . $slug . PHP_EOL;
    return null;
}

function get_category(string $slug): ?Content {
    echo __FUNCTION__ . ': ' . $slug . PHP_EOL;
    if (slug_exists($slug) === false) {
        return null;
    }
//    $element = $manager->getElementFromSlugIndex($slug);

    return null;
}

function get_tag(string $slug): ?Content {
    echo __FUNCTION__ . ': ' . $slug . PHP_EOL;
    return null;
}

function slug_exists(string $slug): bool {
    return get_index_manager()->elementExists($slug);
}
