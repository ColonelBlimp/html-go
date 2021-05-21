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
                    $argv[] = \trim(\urldecode($matches[$id]));
                }
            }

            if (\is_callable($data[HANDLER])) {
                // Add the originally requested URI so it can be passed to
                // the called function. Therefore, the anon function MUST
                // specify this parameter!!!
//                print_r($argv);
                $argv[] = $uri_or_pattern;
                $retval = \call_user_func_array($data[HANDLER], $argv);
            }

            break;
        }

        // No matching route was found, so send a 404
        //TODO: change this to handle errors only (false). The catch-all
        // route should send the user to not_found() if there is no matching
        // static page.
        if ($retval === null || $retval === false) {
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
    //TODO: refactor
    $regex = '@^' . $route . '$@i';
//    echo $regex . PHP_EOL;
    return $regex;
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
 * The main entry point. Called from <code>index.php</code> in the application
 * root. The parameters are provided for testing thus they have default values.
 * @param string $uri
 * @param string $method
 * @return string The html to be rendered.
 */
function dispatch(string $uri = null, string $method = GET): string {
    if ($uri === null) {
//        $uri = parse_uri($_SERVER['REQUEST_URI']);
        $uri = strip_url_parameters($_SERVER['REQUEST_URI']);
        $uri = \trim($uri, FWD_SLASH);
        $uri = empty($uri) ? 'home' : $uri;
        $method = \strtoupper($_SERVER['REQUEST_METHOD']);
    }
    if (($retval = route($method, $uri)) === null) {
        throw new RuntimeException("The route() function returned null!");
    }
    return $retval;
}

/**
 * Strips parameters from the given URL.
 * @param string $url
 * @return string returns the URL without the parameters.
 */
function strip_url_parameters(string $url): string {
    if (($pos = \strpos($url, '?')) === false) {
        return $url;
    }
    parse_query(\substr($url, $pos + 1));
    return \substr($url, 0, $pos);
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
