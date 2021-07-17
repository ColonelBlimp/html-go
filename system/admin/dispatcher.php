<?php declare(strict_types=1);

use html_go\exceptions\InternalException;
require_once ADMIN_SYS_ROOT.DS.'functions.php';

function admin_route(string $context, string $uri, string $method = HTTP_GET): ?\stdClass {
    if (\str_starts_with($uri, $context) === false) {
        return null;
    }

    switch ($method) {
        case HTTP_GET:
            $content = admin_process_get_request($context, $uri);
            break;
        case HTTP_POST:
            $content = admin_process_post_request($uri);
            break;
        default:
            throw new InternalException("Unsupported HTTP Method [$method]");
    }

    return $content;
}

function admin_process_get_request(string $context, string $uri): ?\stdClass {
    $resource = \substr($uri, \strlen($context));
    $i18n = get_i18n();
    $pageTitlePrefix = $i18n->getText('admin.title.prefix');
    switch ($resource) {
        case DASHBOARD_INDEX_KEY:
            $content = get_admin_content_object(template: 'dashboard.html', context: $context, title: $pageTitlePrefix.$i18n->getText('admin.dashboard.title'));
            break;
        case CATEGORY_SECTION:
            $list = get_categories();
            $content = get_admin_content_object(
                template: 'list.html',
                context: $context,
                title: $pageTitlePrefix.$i18n->getText('admin.toolbar.category.title'),
                list: $list);
            break;
        default:
            $content = null;
    }

    return $content;
}

function admin_process_post_request(string $uri): ?\stdClass {
    return null;
}