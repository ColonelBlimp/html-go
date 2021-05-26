<?php declare(strict_types=1);
use html_go\model\Config;

// main index of the site
get('index', function(string $uri): string {
    $template = 'main.html';
    if (get_config_bool(Config::KEY_STATIC_INDEX)) {
        $content = get_content_object('index');
    } else {
        $content = get_content_object('posts/index', get_posts());
        $template = 'listing.html';
    }
    if ($content === null) {
        return not_found();
    }
    return render($template, get_template_context($content));
});

// Catch all route
get('.*', function (string $uri): string {
    $template = 'main.html';
    $matches = [];
    $result = \preg_match('/(\d{4})\/(\d{2})\/(.+)/i', $uri, $matches);
    if ($result === false) {
        throw new RuntimeException("preg_match() failed checking [$uri]"); // @codeCoverageIgnore
    }
    if (\count($matches) !== 4 && $result !== 1) {
        $content = get_content_object($uri);
    } else {
        echo $uri.PHP_EOL;
        $content = get_content_object($matches[1].FWD_SLASH.$matches[2].FWD_SLASH.$matches[3]);
        $template = 'post.html';
    }
    if ($content === null) {
        return not_found();
    }
    return render($template, get_template_context($content));
});
