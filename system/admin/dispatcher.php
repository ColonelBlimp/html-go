<?php
use html_go\exceptions\InternalException;
use html_go\model\Config;

echo __FILE__;

function admin_route(string $uri, string $method = HTTP_GET): string {
    $context = get_config()->getString(Config::KEY_ADMIN_CONTEXT);
    if (\str_starts_with($uri, $context) === false) {
        return null;
    }

    $content = null;
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

    if ($content === null) {
        not_found();
    }

    return render(get_template_context($content));
}

function admin_process_get_request(string $context, string $uri): ?\stdClass {
    $resource = \substr($uri, \strlen($context));
    if ($resource === '') {

    }

    return null;
}

function admin_process_post_request(string $uri): ?\stdClass {
    return null;
}
