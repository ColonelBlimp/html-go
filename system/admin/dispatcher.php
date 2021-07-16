<?php declare(strict_types=1);

use html_go\exceptions\InternalException;
use html_go\model\Config;
require_once ADMIN_SYS_ROOT.DS.'functions.php';

function admin_route(string $uri, string $method = HTTP_GET): ?\stdClass {
    $context = get_config()->getString(Config::KEY_ADMIN_CONTEXT);
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
    $pageTitle = $i18n->getText('admin.title.prefix').$i18n->getText('admin.dashboard.title');
    if ($resource === DASHBOARD_INDEX_KEY) {
        $content = get_admin_content_object('dashboard.html', title: $pageTitle);
    } else {
        $content = null;
    }

    return $content;
}

function admin_process_post_request(string $uri): ?\stdClass {
    return null;
}