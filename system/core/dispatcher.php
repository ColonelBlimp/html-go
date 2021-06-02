<?php declare(strict_types=1);

use html_go\model\Config;

\define('GET', 'GET');
\define('POST', 'POST');
\define('REGEX', 'regex');
\define('HANDLER', 'handler');

/**
 * The main entry point. Called from <code>index.php</code> in the application
 * root. The parameters are provided for testing thus they have default values.
 * @param string $uri
 * @param string $method
 * @return string The html to be rendered.
 */
function dispatch(string $uri = null, string $method = GET): string {
    if ($uri === null) {
        $uri = $_SERVER['REQUEST_URI']; // @codeCoverageIgnore
        $method = \strtoupper($_SERVER['REQUEST_METHOD']); // @codeCoverageIgnore
    }
    $uri = strip_url_parameters($uri);
    $uri = \trim($uri, FWD_SLASH);
    if (empty($uri)) {
        $uri = HOME_INDEX_KEY;
    }
    return route($uri, $method);
}

/**
 *
 * @param string $uri
 * @param string $method
 * @throws RuntimeException
 * @return string
 */
function route(string $uri, string $method): string {
    $result = \preg_match('/^\d{4}\/\d{2}\/.+/i', $uri);
    if ($result === false) {
        throw new RuntimeException("preg_match() failed checking [$uri]"); // @codeCoverageIgnore
    }
    if ($result === 0) { // some other resource
        $template = 'main.html';
        switch ($uri) {
            case HOME_INDEX_KEY:
                if (get_config()->getBool(Config::KEY_STATIC_INDEX)) {
                    $content = get_content_object($uri);
                } else {
                    $content = get_content_object($uri, get_posts());
                }
                if ($content === null) {
                    return not_found();
                }
                break;
            case BLOG_INDEX_KEY:
                if (($content = get_content_object($uri, get_posts())) === null) {
                    return not_found();
                }
                break;
            case CAT_INDEX_KEY:
                if (($content = get_content_object($uri, get_categories())) === null) {
                    return not_found();
                }
                break;
            default:
                if (($content = get_content_object($uri)) === null) {
                    return not_found();
                }
        }
//        print_r($content);
        // The template should be determined by the 'section'?
        if (isset($content->template)) {
            $template = $content->template;
        }

    } else { // blog article request
        if (($content = get_content_object($uri)) === null) {
            return not_found();
        }
        $template = 'post.html';
    }

    echo $template.PHP_EOL.$uri.PHP_EOL;
    return render($template, get_template_context($content));
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
