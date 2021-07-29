<?php declare(strict_types=1);

use html_go\exceptions\InternalException;
use html_go\model\Config;
require_once ADMIN_SYS_ROOT.DS.'functions.php';

/**
 * Main entry point for admin console requests.  This is call from there <code>route(...)</code>
 * function in the 'core/dispatcher.php' file.
 * @param string $method
 * @param string $context
 * @param string $uri
 * @throws InternalException If the HTTP Method is not supported.
 * @return \stdClass|NULL
 */
function admin_route(string $method, string $context, string $uri): ?\stdClass {
    $routes = require_once __DIR__.DS.'routes.php';

    if (isset($routes[$method])) {
        $content = admin_get_content_object($method, $context, $uri, $routes);
    } else {
       throw new InternalException("Unsupported HTTP Method [$method]");
    }

    return $content;
}

/**
 * Returns the content object for the given URI (if there is one), otherwise returns <code>null</code>.
 * @param string $method
 * @param string $context
 * @param string $uri
 * @param array<mixed> $routes
 * @throws InternalException
 * @return \stdClass|NULL
 */
function admin_get_content_object(string $method, string $context, string $uri, array $routes): ?\stdClass {
    $slug = \substr($uri, \strlen($context));
    $slug = normalize_uri($slug);

    $content = null;
    switch ($method) {
        case HTTP_GET:
            if (\array_key_exists($slug, $routes[$method])) {
                $object = $routes[$method][$slug];
                $params = [ADMIN_CONTEXT_STR => $context];
                $id = get_query_parameter('id');
                if ($id !== null) {
                    $params['id'] = $id;
                }
                $content = \call_user_func($object->cb, $params);
            }
            break;
        case HTTP_POST:
            if (\array_key_exists($slug, $routes[$method])) {
                $object = $routes[$method][$slug];
                if (($formData = \filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING)) === false) {
                    throw new InternalException("filter_input_array function failed!");
                }
                $formData[ADMIN_CONTEXT_STR] = $context;
                $content = \call_user_func($object->cb, $formData);
            }
            break;
        default:
            throw new InternalException("Unsupported HTTP Method [$method]");
    }
    return $content;
}
