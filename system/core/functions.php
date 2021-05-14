<?php declare(strict_types=1);

use html_go\templating\TemplateEngine;
use html_go\templating\TwigTemplateEngine;

/**
 * Render the given template placing the given variables into the template context.
 * @param string $template
 * @param array<mixed> $vars
 * @return string
 */
function render(string $template, array $vars = []): string {
    return "render template [$template]";
}

/**
 * Helper function for 404 page.
 * @param string $title
 * @return string
 */
function not_found(string $title = '404 Not Found'): string {
    return render('404.html');
}

function get_template_engine(): TemplateEngine {
    return new TwigTemplateEngine();
}