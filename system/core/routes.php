<?php declare(strict_types=1);
use html_go\model\Config;

// main index of the site
get('index', function(string $uri): string {
    $template = 'main.html';
    if (get_config_bool(Config::KEY_STATIC_INDEX)) {
        $content = get_content_object('index');
    } else {
        $content = get_content_object('posts/index');
        $template = 'listing.html';
    }
    if ($content === null) {
        return not_found();
    }
    return render($template, get_template_context($content));
});
/*
// Single Category
get('category/:name', function(string $uri, string $name): string {
    if (($content = get_content_object($name)) === null) {
        return not_found();
    }
    return render('main.html', get_template_context($content));
});

// Single Tag
get('tag/:name', function(string $uri, string $name): string {
    if (($content = get_content_object($name)) === null) {
        return not_found();
    }
    return render('main.html', get_template_context($content));
});
*/
/*
 * Catch-all route. It does a regex check on the given URI, if it matches,
 * then the request is processed as a request for a blog post, otherwise
 * the request is processed as a request for a static page.
 */
get('.*', function (string $uri): string {
    echo 'Catch-all: ' . $uri . PHP_EOL;

    $template = 'main.html';

    $matches = [];
    if (\preg_match('/(\d{4})\/(\d{2})\/(.+)/i', $uri, $matches) === false) {
        throw new RuntimeException("preg_match() failed checking [$uri]"); // @codeCoverageIgnore
    }

    if (\count($matches) !== 4) {
        $content = get_content_object($uri);
    } else {
        $content = get_content_object($matches[1].FWD_SLASH.$matches[2].FWD_SLASH.$matches[3]);
        $template = 'post.html';
    }
    if ($content === null) {
        return not_found();
    }

    return render($template, get_template_context($content));
});
