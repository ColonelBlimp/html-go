<?php declare(strict_types=1);

// main index of the site
get('index', function(string $uri): string {
    $vars = get_template_vars();
    $vars['site_title'] = get_config_string('site.title', 'HTML-go') . $vars['site_title'];
    return render('main.html', $vars);
});

// Single Category
get('category/:name', function(string $uri, string $name): string {
    $template = 'main.html';
    $vars = get_template_vars();

    if (($content = get_content_object($name)) === null) {
        return not_found();
    }
    $vars['content'] = $content;

    return render($template, $vars);
});

// Single Tag
get('tag/:name', function(string $uri, string $name): string {
    $template = 'main.html';
    $vars = get_template_vars();

    if (($model = get_content_object($name)) === null) {
        return not_found();
    }
    $vars['content'] = $model;

    return render($template, $vars);
});

/*
 * Catch-all route. It does a regex check on the given URI, if it matches,
 * then the request is processed as a request for a blog post, otherwise
 * the request is processed as a request for a static page.
 */
get('.*', function (string $uri): string {
    echo 'Catch-all: ' . $uri . PHP_EOL;

    $template = 'main.html';
    $vars = get_template_vars();

    $matches = [];
    if (\preg_match('/(\d{4})\/(\d{2})\/(.+)/i', $uri, $matches) === false) {
        throw new RuntimeException("preg_match() failed checking [$uri]");
    }

    if (\count($matches) !== 4) {
        $model = get_content_object($uri);
    } else {
        $model = get_post($matches[1], $matches[2], $matches[3]);
        $template = 'post.html';
    }
    if ($model === null) {
        return not_found();
    }

    return render($template, $vars);
});
