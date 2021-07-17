<?php declare(strict_types=1);

function get_admin_content_object(mixed ...$params): \stdClass {
    return get_model_factory()->createAdminContentObject($params);
}
