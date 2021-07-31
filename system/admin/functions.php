<?php declare(strict_types=1);

use html_go\exceptions\InternalException;
use html_go\model\Config;

function get_site_object(): \stdClass {
    $config = get_config();
    $site = new \stdClass();
    $site->url = $config->getString(Config::KEY_SITE_URL);
    $site->name = $config->getString(Config::KEY_SITE_NAME);
    $site->title = $config->getString(Config::KEY_SITE_TITLE);
    $site->description = $config->getString(Config::KEY_SITE_DESCRIPTION);
    $site->tagline = $config->getString(Config::KEY_SITE_TAGLINE);
    $site->copyright = $config->getString(Config::KEY_SITE_COPYRIGHT);
    $site->language = $config->getString(Config::KEY_LANG);
    $site->theme = $config->getString(Config::KEY_THEME_NAME);
    $site->tpl_engine = $config->getString(Config::KEY_TPL_ENGINE);
    return $site;
}

/**
 *
 * @param string $titleLangKey
 * @param string $template
 * @param string $section
 * @param string $action
 * @param array<mixed> $params
 * @param array<\stdClass> $list
 * @return \stdClass
 */
function get_admin_content_object(string $titleLangKey, string $template, string $section, string $action, array $params = [], array $list = []): \stdClass {
    $data = [
        'title' => get_i18n()->getText($titleLangKey),
        'template' => $template,
        'context' => get_config()->getString(Config::KEY_ADMIN_CONTEXT),
        'section' => $section,
        'action' => $action,
        'list' => $list
    ];
    return get_model_factory()->createAdminContentObject(\array_merge($data, $params));
}

/**
 * Returns the admin dashboard view content object.
 * @param array<mixed> $args
 * @return \stdClass
 */
function get_dashboard_view_content_object(array $args): \stdClass {
    return get_admin_content_object(
        'admin.dashboard.title',
        'dashboard.html',
        CATEGORY_SECTION,
        ADMIN_ACTION_VIEW,
        $args);
}

/**
 * Persist content.
 * @param string $section
 * @param array<mixed> $data
 * @throws InternalException
 */
function save_content(string $section, array $data): void {
    switch ($section) {
        case CATEGORY_SECTION:
            $filename = URLify::slug(\strtolower($data['title']));
            $filePath = CATEGORY_ROOT.DS.$filename.CONTENT_FILE_EXT;
            break;
        default:
            throw new InternalException("Unknown section [$section]");
    }

    unset($data[ADMIn_KEY_STR], $data[ADMIN_ACTION_STR], $data['save'], $data[ADMIN_CONTEXT_STR], $data['errorKey']);

    if (($json = \json_encode($data, JSON_PRETTY_PRINT)) === false) {
        throw new InternalException("json_encode function failed!");
    }
    if (\file_put_contents($filePath, $json) === false) {
        throw new InternalException("file_put_contents function failed!");
    }

    get_index_manager()->reindex();
}
