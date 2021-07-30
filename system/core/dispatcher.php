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
function dispatch(string $uri = null, string $method = HTTP_GET): ?string {
    if ($uri === null) {
        $uri = $_SERVER['REQUEST_URI']; // @codeCoverageIgnore
        $method = \strtoupper($_SERVER['REQUEST_METHOD']); // @codeCoverageIgnore
    }
    $uri = strip_url_parameters($uri);
    $uri = normalize_uri($uri);

    return route($uri, $method);
}

/**
 * @param string $uri
 * @return string
 */
function normalize_uri(string $uri): string {
    $uri = \trim($uri, FWD_SLASH);
    if (empty($uri)) {
        $uri = HOME_INDEX_KEY;
    }
    return $uri;
}

/**
 * Route the given HTTP request.
 * @param string $uri The requested URI
 * @param string $method the HTTP method
 * @throws InternalException
 * @return string|NULL If the return value is an empty string (''), then an 'Location: ...' has been
 * done by t
 */
function route(string $uri, string $method): ?string {
    $adminCtx = get_config()->getString(Config::KEY_ADMIN_CONTEXT);
    if (\str_starts_with($uri, $adminCtx)) {
        $content = process_admin_request($method, $uri);
    } else {
        $content = process_request($uri, get_pagination_pagenumber(), get_config()->getInt(Config::KEY_POSTS_PERPAGE));
    }

    if ($content === null) {
        not_found();
        return null;
    }

    return render(get_template_context($content));
}

/**
 * Process a request for a public resource. This method assumes HTTP_GET only as request are for
 * the public site, not the admin.
 * @param string $uri
 * @param int $pageNum
 * @param int $perPage
 * @return \stdClass|NULL Returns <code>null</code> if the resource is not found
 */
function process_request(string $uri, int $pageNum, int $perPage): ?\stdClass {
    $template = LIST_TEMPLATE;

    if (HOME_INDEX_KEY === $uri) {
        return get_homepage($pageNum, $perPage);
    }

    $list = is_landing_page($uri, $pageNum, $perPage);
    if (empty($list)) {
        $list = is_list_page($uri, $pageNum, $perPage);
        if (empty($list)) {
            $template = SINGLE_TEMPLATE;
        }
    }

    return get_content_object($uri, $list, $template);
}

/**
 * Process an admin console request.
 * @param string $method
 * @param string $uri
 * @return \stdClass|NULL
 */
function process_admin_request(string $method, string $uri): ?\stdClass {
    require_once ADMIN_SYS_ROOT.DS.'bootstrap.php';
    return admin_route($method, $uri);
}

/**
 * Checks if the given URI is a landing page (excluding the home page). If so, returns a list of the
 * appropriate content objects for that page.
 * @param string $uri
 * @return array<\stdClass>
 */
function is_landing_page(string $uri, int $pageNum = 1, int $perPage = 0): array {
    $list = [];
    switch (true) {
        case $uri === CAT_INDEX_KEY:
            $list = get_categories($pageNum, $perPage);
            break;
        case $uri === POST_INDEX_KEY:
            $list = get_posts($pageNum, $perPage);
            break;
        case $uri === TAG_INDEX_KEY:
            $list = get_tags($pageNum, $perPage);
            break;
        default:
            // Do nothing
    }
    return $list;
}

/**
 * Checks if the given URI is a list page (excluding the home page), if so returns a list of the
 * approriate content object for that page.
 * @param string $uri
 * @param int $pageNum
 * @param int $perPage
 * @return array<\stdClass>
 */
function is_list_page(string $uri, int $pageNum = 1, int $perPage = 0): array {
    $list = [];
    switch (true) {
        case \str_starts_with($uri, POST_INDEX_KEY):
            $list = get_posts($pageNum, $perPage);
            break;
        case \str_starts_with($uri, CAT_INDEX_KEY.FWD_SLASH):
            $list = get_posts_for_section(CATEGORY_SECTION, $uri, $pageNum, $perPage);
            break;
        case \str_starts_with($uri, TAG_INDEX_KEY.FWD_SLASH):
            $list = get_posts_for_section(TAG_SECTION, $uri, $pageNum, $perPage);
            break;
        default:
            // Do nothing
    }
    return $list;
}

/**
 * Returns the home page.
 * @param int $pageNum
 * @param int $perPage
 * @throws InternalException
 * @return \stdClass
 */
function get_homepage(int $pageNum, int $perPage): \stdClass {
    if (get_config()->getBool(Config::KEY_STATIC_INDEX)) {
        $content = get_content_object(HOME_INDEX_KEY);
    } else {
        $content = get_content_object(HOME_INDEX_KEY, get_posts($pageNum, $perPage), LIST_TEMPLATE);
    }

    if ($content === null) {
        throw new InternalException("Unable to find the home index page!");
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
