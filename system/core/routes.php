<?php declare(strict_types=1);
use html_go\model\Config;

/*
 * The main index page of the site (Home Page).
 * This will render either a static page ('home') or a post listing
 * depending on the config setting.
 */
/*
get('index', function(string $uri): string {
    $template = 'main.html';
    if (get_config()->getBool(Config::KEY_STATIC_INDEX)) {
        $content = get_content_object(HOME_INDEX_KEY);
    } else {
        $content = get_content_object(BLOG_INDEX_KEY,
                get_posts(get_pagination_pagenumber(), get_config()->getInt(Config::KEY_POSTS_PERPAGE)));
        $template = 'listing.html';
    }
    if ($content === null) {
        return not_found();
    }
    return render($template, get_template_context($content));
});
*/
/*
 * The category landing page, which is a special case page as the index key
 * is not the URI but has '/index' suffixed to the URI (i.e. 'category/index').
 */
/*
get('category', function (string $uri): string {
    $content = get_content_object(CAT_INDEX_KEY,
            get_categories(get_pagination_pagenumber(), get_config()->getInt(Config::KEY_POSTS_PERPAGE)));
    if ($content === null) {
        return not_found();
    }
    return render('listing.html', get_template_context($content));
});
*/
/*
 * The tag landing page, which is a special case page as the index key
 * is not the URI but has '/index' suffixed to the URI (e.g. 'category/index').
 */
/*
get('tag', function (string $uri): string {
    $content = get_content_object(TAG_INDEX_KEY,
            get_tags(get_pagination_pagenumber(), get_config()->getInt(Config::KEY_POSTS_PERPAGE)));
    if ($content === null) {
        return not_found();
    }
    return render('listing.html', get_template_context($content));
});
*/
/*
 * The Blog landing page (only used if front page is static).
 */
/*
get('blog', function (string $uri): string {
    $content = get_content_object(HOME_INDEX_KEY,
            get_posts(get_pagination_pagenumber(), get_config()->getInt(Config::KEY_POSTS_PERPAGE)));
    if ($content === null) {
        return not_found();
    }
    return render('main.html', get_template_context($content));
});
*/
/*
 * Catch all route. This should render single content pages only. Listing
 * pages such as landing pages, archives should be handled as a special
 * case and have its own route defined.
 */
/*
get('.*', function (string $uri): string {
    $result = \preg_match('/^\d{4}\/\d{2}\/.+/i', $uri);
    if ($result === false) {
        throw new RuntimeException("preg_match() failed checking [$uri]"); // @codeCoverageIgnore
    }
    if ($result === 0) {
        $content = get_content_object($uri);
        $template = 'main.html';
    } else {
        $content = get_content_object($uri);
        $template = 'post.html';
    }
    if ($content === null) {
        return not_found();
    }
    return render($template, get_template_context($content));
});
*/