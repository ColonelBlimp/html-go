<?php declare(strict_types=1);

function get_admin_content_object(string ...$params): \stdClass {
    $content = get_model_factory()->createAdminContentObject($params);
    return $content;
}