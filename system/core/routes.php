<?php declare(strict_types=1);

get('index', function(): string {
    return render('main.html', get_template_vars());
});
