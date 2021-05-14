<?php declare(strict_types=1);

\define('GET', 'GET');
\define('POST', 'POST');
\define('REGEX', 'regex');
\define('HANDLER', 'handler');

/**
 * Registers a route or processes a request (uri) for a route.
 * @param string $method the HTTP method
 * @param string $uri_or_pattern the requested URI to be processed or route pattern to be registered
 * @param callable $handler when registering a route a callback must be provided to process the
 *                          the request for that route.
 * @return string|NULL <code>string</code> when processing a route request, otherwise
 *                     <code>null</code> when registering a route
 */
function route(string $method, string $uri_or_pattern, callable $handler = null): ?string {

    static $routeMap = [
        GET => [],
        POST => []
    ];

    $retval = null;

    $uri_or_pattern = \trim($uri_or_pattern, FWD_SLASH);

    if ($handler === null) {
        foreach ($routeMap[$method] as $def => $data) {
            $matches = [];
            $result = \preg_match($data[REGEX], $uri_or_pattern, $matches);
            if ($result === 0 || $result === false) {
                continue;
            }

            \array_shift($matches);
            $keys = [];
            \preg_match_all('@:([\w]+)@', $def, $keys, PREG_PATTERN_ORDER);
            $keys = \array_shift($keys);

            $argv = [];
            foreach ($keys as $id) {
                $id = \substr($id, 1);
                if (isset($matches[$id])) {
                    \array_push($argv, \trim(\urldecode($matches[$id])));
                }
            }

            if (\is_callable($data[HANDLER])) {
                $retval = \call_user_func_array($data[HANDLER], $argv);
            }

            break;
        }

        // No matching route was found, so send a 404
        if ($retval === null) {
            $retval = not_found();
        }
    } else {
        $routeMap[$method][$uri_or_pattern] = [
            REGEX => route_to_regex($uri_or_pattern),
            HANDLER => $handler
        ];
    }

    return $retval;
}

function route_to_regex(string $route): string {
    $route = \preg_replace_callback('@:[\w]+@i', function ($matches) {
        $token = \str_replace(':', '', $matches[0]);
        return '(?P<' . $token . '>[a-z0-9_\0-\.]+)';
    }, $route);
        return '@^' . $route . '$@i';
}

/**
 * Helper function to register a HTTP GET route.
 * @param string $pattern
 * @param callable $handler an anonymous function which will process the request for the registered
 *                          route.
 */
function get(string $pattern, callable $handler): void {
    route(GET, $pattern, $handler);
}

/**
 * The main entry point. Called from <code>index.php</code> in the application root.
 */
function dispatch(string $uri = null): string {
    if ($uri === null) {
        $uri = parse_uri($_SERVER['REQUEST_URI']);
        $uri = \trim($uri, FWD_SLASH);
        $uri = empty($uri) ? 'index' : $uri;
    }
    if (($retval = route(\strtoupper($_SERVER['REQUEST_METHOD']), $uri)) === null) {
        throw new RuntimeException("The route() function returned null!");
    }
    return $retval;
}

function parse_uri(string $uri): string {
    //TODO: can this be speeded up with array_filter(explode())?
    $_uri = \strtok($uri, '?');
    if ($_uri === false) {
        return $uri;
    }
    $query = \strtok('?');
    if ($query !== false) {
        parse_query($query);
    }
    return $_uri;
}

/**
 * @param string $query (Optional). Calling with this parameter set will populate the returned data array.
 * @return array<string, string>
 */
function parse_query(string $query = null): array {
    static $data = [];
    if (empty($data) && $query !== null) {
        \parse_str($query, $data);
    }
    return $data;
}

/**
 * Return the query string value for the given key.
 * @param string $key
 * @return string|NULL <code>null</code> if the key is not found
 */
function get_query_parameter(string $key): ?string {
    $params = parse_query();
    if (isset($params[$key])) {
        return $params[$key];
    }
    return null;
}
