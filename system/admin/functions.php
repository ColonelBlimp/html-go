<?php declare(strict_types=1);

function get_admin_content_object(string $template, string ...$params): \stdClass {
    $content = get_model_factory()->createAdminContentObject($params);
    $content->template = $template;
    return $content;
}