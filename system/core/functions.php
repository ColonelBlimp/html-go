<?php declare(strict_types=1);

use html_go\i18n\i18n;
use html_go\indexing\IndexManager;
use html_go\markdown\MarkdownParser;
use html_go\markdown\ParsedownParser;
use html_go\model\Config;
use html_go\model\Content;
use html_go\model\ModelFactory;
use html_go\templating\TemplateEngine;
use html_go\templating\TwigTemplateEngine;

/**
 * Returns the page number from the query string (if there is one).
 * @return int Default value is one (1)
 */
function get_pagination_pagenumber(): int {
    $page_num = 1;
    if (($page = get_query_parameter('page')) !== null) {
        if (\ctype_digit($page)) {
            $page_num = (int)$page;
        }
    }
    return $page_num;
}

/**
 * Returns a pagination page of tags.
 * @param int $page_number The page number
 * @param int $per_page Items per page. Default is 0 (zero) which means return all
 * @return array<Content>
 */
function get_tags(int $page_number = 1, int $per_page = 0): array {
    $list = [];
    //TODO: Implement me
    return $list;
}

/**
 * Returns a pagination page of categories.
 * @param int $page_number The page number
 * @param int $per_page Items per page. Default is 0 (zero) which means return all
 * @return array<Content> The resulting list of posts
 */
function get_categories(int $page_number = 1, int $per_page = 0): array {
    $cats = get_index_manager()->getCategoriesIndex();
    if ($per_page > 0) {
        $cats = \array_slice($cats, ($page_number - 1) * $per_page, $per_page);
    }
    $list = [];
    $factory = get_model_factory();
    foreach ($cats as $obj) {
        $list[] = $factory->createContentObject($obj);
    }
    return $list;
}

/**
 * Build and return the template context.
 * @param Content $content
 * @return array<mixed>
 */
function get_template_context(Content $content): array {
    return [
        'i18n' => get_i18n(),
        'content' => $content
    ];
}

/**
 * Return the <code>i18n</code> instance.
 * @return i18n
 */
function get_i18n(): i18n {
    static $object = null;
    if (empty($object)) {
        $object = new i18n(LANG_ROOT.DS.get_config_string('site.language').'.messages.php');
    }
    return $object;
}

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
    //TODO: Refactor
    /*
    $vars = get_template_vars();
    $vars['site_title'] = $title . $vars['site_title'];
    return render('404.html', $vars);
    */
    return '404';
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
 * Returns the instance of the <code>IndexManager</code>.
 * @return IndexManager
 */
function get_index_manager(): IndexManager {
    static $manager = null;
    if ($manager === null) {
        $manager = new IndexManager(APP_ROOT);
    }
    return $manager;
}

/**
 * Return the instance of the <code>Config</code>.
 * @return Config
 */
function get_config(): Config {
    static $config = null;
    if (empty($config)) {
        $config = new Config(CONFIG_ROOT);
    }
    return $config;
}

/**
 * Returns the instance of the <code>ModelFactory</code>.
 * @return ModelFactory
 */
function get_model_factory(): ModelFactory {
    static $factory = null;
    if (empty($factory)) {
        $factory = new ModelFactory(get_config(), get_markdown_parser());
    }
    return $factory;
}

/**
 * Returns the instance of the <code>MarkdownParser</code>.
 * @return MarkdownParser
 */
function get_markdown_parser(): MarkdownParser {
    static $parser = null;
    if (empty($parser)) {
        $parser = new ParsedownParser();
    }
    return $parser;
}

/**
 * Returns an string configuration option value.
 * @param string $key the key of the configuration value to return.
 * @param string $default an empty string
 * @return string the value
 */
function get_config_string(string $key, string $default = ''): string {
    return get_config()->getString($key, $default);
}

/**
 * Returns an integer configuration option value.
 * @param string $key the key of the configuration value to return.
 * @param int $default minus one (-1)
 * @return int
 */
function get_config_int(string $key, int $default = -1): int {
    return get_config()->getInt($key, $default);
}

/**
 * Returns a boolean configuration option value.
 * @param string $key the key of the configuration value to return.
 * @param bool $default <code>false</code>
 * @return bool
 */
function get_config_bool(string $key, bool $default = false): bool {
    return get_config()->getBool($key, $default);
}

/**
 * Get a <code>Content</code> object (if any) associated with the given slug.
 * @param string $slug
 * @param array<Content>$listing
 * @return Content|NULL if no content was found associated with the given slug <code>null</code> is returned.
 */
function get_content_object(string $slug, array $listing = []): ?Content {
    $manager = get_index_manager();
    if ($manager->elementExists($slug) === false) {
        return null;
    }
    $content = get_model_factory()->createContentObject($manager->getElementFromSlugIndex($slug));
    if (!empty($listing)) {
        $content->listing = $listing;
    }
    return $content;
}

/**
 * Returns a pagination page of posts.
 * @param int $page_number The page number
 * @param int $per_page Items per page
 * @return array<Content> The resulting list of posts
 */
function get_posts(int $page_number = 1, int $per_page = 5): array {
    $posts = get_index_manager()->getPostsIndex();
    $posts = \array_slice($posts, ($page_number - 1) * $per_page, $per_page);
    $list = [];
    $factory = get_model_factory();
    foreach ($posts as $post) {
        $list[] = $factory->createContentObject($post);
    }
    return $list;
}

/**
 * Checks if the given slug exists in the index manager (alias for <code>IndexManager::elementExists()</code>).
 * @param string $slug
 * @return bool
 */
function slug_exists(string $slug): bool {
    return get_index_manager()->elementExists($slug);
}
