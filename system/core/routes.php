<?php declare(strict_types=1);

// main index of the site
get('index', function(string $uri): string {
//    echo __FUNCTION__ . 'index: ' . $uri;
    $vars = get_template_vars();
    $vars['site_title'] = config('site.title', 'HTML-go') . $vars['site_title'];
    return render('main.html', $vars);
});

// static pages

get('category/:name', function(string $static): string {
//    echo __FUNCTION__ . '/category/' . $static;
    $template = 'main.html';
    $vars = get_template_vars();

    switch($static) {
        default:
            $model = get_page($static);
            if (!$model) {
                not_found();
                exit;
            }
            $vars['content'] = $model;
    }

    return render($template, $vars);
});
/*
get(':parent/:child', function(string $parent, string $child): string {
    return static_page_handler($parent, $child);
});
*/
function static_page_handler(string $parent, string $child): string {
    $template = 'main.html';
    $vars = get_template_vars();

    $slug = $parent . FWD_SLASH . $child;
    $model = get_page($slug);
    $vars['content'] = $model;

    return render($template, $vars);
}

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