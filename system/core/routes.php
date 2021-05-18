<?php declare(strict_types=1);

// main index of the site
get('index', function(string $uri): string {
    $vars = get_template_vars();
    $vars['site_title'] = config('site.title', 'HTML-go') . $vars['site_title'];
    return render('main.html', $vars);
});

// Categories
get('category/:name', function(string $uri, string $name): string {
    $template = 'main.html';
    $vars = get_template_vars();

    switch($name) {
        default:
            $model = get_category($name);
            if (!$model) {
                not_found();
                exit;
            }
            $vars['content'] = $model;
    }

    return render($template, $vars);
});

// Tags
get('tag/:name', function(string $uri, string $name): string {
    $template = 'main.html';
    $vars = get_template_vars();
    return render($template, $vars);
});

// Catch-all route. If a static page is not found for the URI, then
// the user is routed to not_found()
get('.*', function (string $uri) {
//    $template = 'main.html';
//    $vars = get_template_vars();

//    $found = true;
// echo __FUNCTION__ . '.*: ' . $uri;
    switch ($uri) {
        default:
            $model = get_page($uri);
//            $vars['content'] = $model;
    }
    /*
    switch($uri) {
        default:
            $model = get_page($uri);
            if (!$model) {
                not_found();
                exit;
            }
            $vars['content'] = $model;
    }

    return render($template, $vars);
    */
    not_found();
});