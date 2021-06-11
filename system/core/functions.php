<?php declare(strict_types=1);

use html_go\i18n\I18n;
use html_go\indexing\IndexManager;
use html_go\markdown\MarkdownParser;
use html_go\markdown\ParsedownParser;
use html_go\model\Config;
use html_go\model\ModelFactory;
use html_go\templating\TemplateEngine;
use html_go\templating\TwigTemplateEngine;

/**
 * Returns a stdClass object populated with the author details.
 * @param string $name
 * @return \stdClass|NULL stdClass object, otherwise <code>null</code> if the
 * user does not exist.
 */
function get_author(string $name): ?\stdClass {
    $obj = new \stdClass();

    return $obj;
}

/**
 * Returns an multi-dimensional array. The first level is the menu name, the
 * second level is an array of stdClass objects each representing a menu node.
 * @return array<mixed>
 */
function get_menu(): array {
    return get_index_manager()->getMenusIndex();
}

/**
 * Returns the page number from the query string (if there is one).
 * @return int Default value is one (1)
 */
function get_pagination_pagenumber(): int {
    $pageNum = 1;
    if (($page = get_query_parameter('page')) !== null && \ctype_digit($page)) {
        $pageNum = (int)$page;
    }
    return $pageNum;
}

/**
 * Returns a pagination page of tags.
 * @param int $pageNum The page number
 * @param int $perPage Items per page. Default is 0 (zero) which means return all
 * @return array<\stdClass>
 */
function get_tags(int $pageNum = 1, int $perPage = 0): array {
    $tags = get_index_manager()->getTagIndex();
    if ($perPage > 0) {
        $tags = \array_slice($tags, ($pageNum - 1) * $perPage, $perPage);
    }
    return get_model_list($tags);
}

/**
 * Returns a pagination page of categories.
 * @param int $pageNum The page number
 * @param int $perPage Items per page. Default is 0 (zero) which means return all
 * @return array<\stdClass> The resulting list of posts
 */
function get_categories(int $pageNum = 1, int $perPage = 0): array {
    $cats = get_index_manager()->getCategoriesIndex();
    if ($perPage > 0) {
        $cats = \array_slice($cats, ($pageNum - 1) * $perPage, $perPage);
    }
    return get_model_list($cats);
}

/**
 * Takes an array of index <code>Element</code> object and converts them to an array of
 * <code>stdClass</code> objects.
 * @param array<string, html_go\indexing\Element> $indexList
 * @return array<\stdClass>
 */
function get_model_list(array $indexList): array {
    $list = [];
    $factory = get_model_factory();
    foreach ($indexList as $obj) {
        $list[] = $factory->createContentObject($obj);
    }
    return $list;
}

/**
 * Build and return the template context.
 * @param \stdClass $content
 * @return array<mixed>
 */
function get_template_context(\stdClass $content): array {
    $template = DEFAULT_TEMPLATE;
    if (isset($content->template)) {
        $template = $content->template;
    }
    return [
        'i18n' => get_i18n(),
        'content' => $content,
        TEMPLATE_TPLVAR_KEY => $template
    ];
}

/**
 * Return the <code>i18n</code> instance.
 * @return I18n
 */
function get_i18n(): I18n {
    static $object = null;
    if (empty($object)) {
        $object = new I18n(LANG_ROOT.DS.get_config()->getString(Config::KEY_LANG).'.messages.php');
    }
    return $object;
}

/**
 * Render the given template placing the given variables into the template context.
 * Note: template defined in the front matter of the content file takes precendence.
 * @param string $template Default is <code>null</code>
 * @param array<mixed> $vars
 * @return string
 */
function render(string $template = null, array $vars = []): string {
    $tpl = DEFAULT_TEMPLATE;
    if (!empty($template)) {
        $tpl = $template;
    }
    // Front matter from content file takes precendence
    if (isset($vars[TEMPLATE_TPLVAR_KEY])) {
        $tpl = $vars[TEMPLATE_TPLVAR_KEY];
    }
    return get_template_engine()->render($tpl, $vars);
}

/**
 * Helper function for 404 page.
 * @param string $title
 * @return string
 */
function not_found(string $title = '404 Not Found'): string {
    return render('404.html', ['i18n' => get_i18n()]);
}

/**
 * Get the configured template engine.
 * @return TemplateEngine
 */
function get_template_engine(): TemplateEngine {
    static $engine = null;
    if (empty($engine)) {
        $themeName = get_config()->getString(Config::KEY_THEME_NAME);
        $engineName = get_config()->getString(Config::KEY_TPL_ENGINE);

        $caching = false;
        $strict = true;
        if (get_config()->getBool(Config::KEY_TPL_CACHING)) {
            $caching = CACHE_ROOT.DS.'template_cache';
        }
        $strict = get_config()->getBool(Config::KEY_TPL_STRICT_VARS_TWIG);
        $options = [
            'cache' => $caching,
            'strict_variables' => $strict
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
 * Get a <code>Content</code> object (if any) associated with the given slug.
 * @param string $slug
 * @param array<\stdClass>$listing
 * @return \stdClass|NULL if no content was found associated with the given slug <code>null</code> is returned.
 */
function get_content_object(string $slug, array $listing = []): ?\stdClass {
    $manager = get_index_manager();
    if ($manager->elementExists($slug) === false) {
        return null;
    }
    $content = get_model_factory()->createContentObject($manager->getElementFromSlugIndex($slug));
    if (!empty($listing)) {
        $content->listing = $listing;
    }
    $content->menus = get_menu();
    return $content;
}

/**
 * Returns a pagination page of posts.
 * @param int $pageNum The page number
 * @param int $perPage Items per page
 * @return array<\stdClass> The resulting list of posts
 */
function get_posts(int $pageNum = 1, int $perPage = 5): array {
    $posts = get_index_manager()->getPostsIndex();
    $posts = \array_slice($posts, ($pageNum - 1) * $perPage, $perPage);
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
