<?php declare(strict_types=1);

get('index', function(): string {
    $vars = get_template_vars();

    $vars['site_title'] = 'HTML-go' . $vars['site_title'];
    return render('main.html', $vars);
});
