<?php declare(strict_types=1);

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
 * Returns an admin content <b>view</b> object for <i>categories</i>.
 * @param array<mixed> $args
 * @return \stdClass
 */
function get_category_view_object(array $args): \stdClass {
    $params = [
        'title' => get_i18n()->getText('admin.dashboard.title'),
        'template' => 'admin-list.html',
        'section' => CATEGORY_SECTION,
        'action' => ADMIN_ACTION_VIEW
    ];
    $content = get_model_factory()->createAdminContentObject(\array_merge($args, $params));
    $content->list = get_categories(get_pagination_pagenumber(), get_config()->getInt(Config::KEY_ADMIN_ITEMS_PER_PAGE));
    return $content;
}

/**
 * Return an admin content <b>edit</b> object for <i>categories</i>.
 * @param array<mixed> $args
 * @throws InvalidArgumentException
 * @throws InternalException
 * @return \stdClass
 */
function get_category_edit_object(array $args): \stdClass {
    if (empty($args['id'])) {
        throw new InvalidArgumentException("The args array must contain an id parameter.");
    }
    $manager = get_index_manager();
    if ($manager->elementExists($args['id']) === false) {
        $id = $args['id'];
        throw new InternalException("Element does not exist [$id]");
    }
    $element = get_model_factory()->createContentObject($manager->getElementFromSlugIndex($args['id']));
    $params = [
        'title' => get_i18n()->getText('admin.dashboard.title'),
        'template' => 'admin-action.html',
        'section' => CATEGORY_SECTION,
        'list' => [$element],
        'action' => ADMIN_ACTION_EDIT,
    ];
    return get_model_factory()->createAdminContentObject(\array_merge($args, $params));
}
