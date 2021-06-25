<?php declare(strict_types=1);

use html_go\exceptions\InternalException;
use html_go\model\Config;

/**
 * The main entry point. Called from <code>index.php</code> in the application
 * root. The parameters are provided for testing thus they have default values.
 * @param string $uri
 * @param string $method
 * @return string The html to be rendered.
 */
function dispatch(string $uri = null, string $method = HTTP_GET): string {
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
 * Route the given HTTP request.
 * @param string $uri The requested URI
 * @param string $method the HTTP method
 * @throws InternalException
 * @return string
 */
function route(string $uri, string $method): string {
    if ($method === HTTP_POST) {
        return not_found();
    }

    $result = \preg_match(POST_REQ_REGEX, $uri);
    if ($result === false) {
        throw new InternalException("preg_match() failed checking [$uri]"); // @codeCoverageIgnore
    }

    if ($result === 1) {
        $content = get_content_object($uri);
    } else {
        $content = process_request($uri, get_pagination_pagenumber(), get_config()->getInt(Config::KEY_POSTS_PERPAGE));
    }

    if ($content === null) {
        return not_found();
    }

    return render(vars: get_template_context($content));
}

/**
 * Process a request for a single blog-post.
 * @param string $uri
 * @param int $pageNum
 * @param int $perPage
 * @return \stdClass|NULL
 */
function process_request(string $uri, int $pageNum, int $perPage): ?\stdClass {
    $template = LIST_TEMPLATE;

    switch (true) {
        case \str_starts_with($uri, POST_INDEX_KEY.FWD_SLASH):
            $list = get_posts($pageNum, $perPage);
            break;
        case \str_starts_with($uri, CAT_INDEX_KEY.FWD_SLASH):
            $list = get_categories($pageNum, $perPage);
            break;
        case \str_starts_with($uri, TAG_INDEX_KEY.FWD_SLASH):
            $list = get_tags($pageNum, $perPage);
            break;
        default: // home page or static page
            $list = [];
            $template = SINGLE_TEMPLATE;
    }

    if ($uri === HOME_INDEX_KEY) {
        $content = get_homepage($pageNum, $perPage);
    } else {
        $content = get_content_object($uri, $list, $template);
    }

    return $content;
}

function get_homepage(int $pageNum, int $perPage): \stdClass {
    if (get_config()->getBool(Config::KEY_STATIC_INDEX)) {
        if (($content = get_content_object(HOME_INDEX_KEY, template: SINGLE_TEMPLATE)) === null) {
            throw new InternalException("Unable to find the home index page!");
        }
    } else {
        if (($content = get_content_object(HOME_INDEX_KEY, get_posts($pageNum, $perPage), LIST_TEMPLATE)) === null) {
            throw new InternalException("Unable to find the home index page!");
        }
    }
    return $content;
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
