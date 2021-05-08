<?php declare(strict_types=1);

\define('GET', 'GET');
\define('POST', 'POST');
\define('REGEX', 'regex');
\define('HANDLER', 'handler');

function route(string $method, string $url_or_pattern, callable $handler = null): void {
    echo __FUNCTION__;
}

function get(string $pattern, callable $handler): void {
    route(GET, $pattern, $handler);
}

function dispatch(): void {
    route(\strtoupper($_SERVER['REQUEST_METHOD']), '/index');
}
