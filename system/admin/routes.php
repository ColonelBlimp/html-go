<?php

use html_go\model\Config;
use html_go\exceptions\InternalException;

return [
    /*
    HTTP_GET => [
        '/' => (object) [
            'title' => get_i18n()->getText('admin.dashboard.title'),
            'template' => 'dashboard.html',
            'action' => 'view'
        ],
        CAT_INDEX_KEY => (object) [
            'cb' => function (int $pagenum=1, int $perpage=5): mixed {
                return get_categories($pagenum, $perpage);
            },
            'title' => get_i18n()->getText('admin.toolbar.category.title'),
            'description' => 'Description',
            'template' => 'admin-list.html',
            'section' => CATEGORY_SECTION,
            'action' => 'view'
        ],
        CAT_INDEX_KEY.FWD_SLASH.'edit' => (object) [
            'cb' => function (string $key): mixed {
                $manager = get_index_manager();
                if ($manager->elementExists($key) === false) {
                    return null;
                }
                $indexElement = $manager->getElementFromSlugIndex($key);
                return get_model_factory()->createContentObject($indexElement);
            },
            'title' => get_i18n()->getText('admin.toolbar.category.title'),
            'template' => 'admin-action.html',
            'section' => CATEGORY_SECTION,
            'action' => 'edit'
        ]
    ],
    */
    HTTP_GET => [
        '/' => (object) [
            'cb' => function (array $args): \stdClass {
                $params = [
                    'title' => get_i18n()->getText('admin.dashboard.title'),
                    'template' => 'dashboard.html',
                    'section' => CATEGORY_SECTION
                ];
                return get_model_factory()->createAdminContentObject(\array_merge($args, $params));
            },
            'action' => 'view'
        ],
        CAT_INDEX_KEY => (object) [
            'cb' => function (array $args): \stdClass {
                $params = [
                    'title' => get_i18n()->getText('admin.dashboard.title'),
                    'template' => 'admin-list.html',
                    'section' => CATEGORY_SECTION
                ];
                $content = get_model_factory()->createAdminContentObject(\array_merge($args, $params));
                $content->list = get_categories(get_pagination_pagenumber(), get_config()->getInt(Config::KEY_ADMIN_ITEMS_PER_PAGE));
                $content->action = 'view';
                return $content;
            },
        ],
        CAT_INDEX_KEY.FWD_SLASH.'edit' => (object) [
            'cb' => function (array $args): \stdClass {
                if (empty('id')) {
                    throw new InvalidArgumentException("The args array must contain an id parameter.");
                }
                $manager = get_index_manager();
                if ($manager->elementExists($args['id']) === false) {
                    $id = $args['id'];
                    throw new InternalException("Element does not exist [$id]");
                }
                $element = get_model_factory()->createContentObject($manager->getElementFromSlugIndex($args['id']));
                $params = [
                    'title' => get_i18n()->getText('admin.dashboard.title'),
                    'template' => 'admin-action.html',
                    'section' => CATEGORY_SECTION,
                    'list' => [$element],
                    'action' => 'edit',
                ];
                return get_model_factory()->createAdminContentObject(\array_merge($args, $params));
            },
        ]
    ],
    HTTP_POST => []
];
