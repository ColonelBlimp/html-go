<?php declare(strict_types=1);

/**
 * Returns a list of widgets
 * @return array<mixed>
 */
function get_widgets(): array {
    return [
        'recent_posts' => get_posts()
    ];
}
