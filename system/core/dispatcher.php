<?php declare(strict_types=1);

\define('GET', 'GET');
\define('POST', 'POST');
\define('REGEX', 'regex');
\define('HANDLER', 'handler');

function route(string $method, string $uri_or_pattern, callable $handler = null): ?string {

    static $routeMap = [
        GET => [],
        POST => []
    ];

    $retval = null;

    $uri_or_pattern = \trim($uri_or_pattern);

    if ($handler === null) {
        foreach ($routeMap[$method] as $def => $data) {
            $matches = [];
            if (!\preg_match($data[REGEX], $uri_or_pattern, $matches)) {
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

function get(string $pattern, callable $handler): void {
    route(GET, $pattern, $handler);
}

function dispatch(): string {
    route(\strtoupper($_SERVER['REQUEST_METHOD']), '/index');
}
