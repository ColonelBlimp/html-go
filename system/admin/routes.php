<?php

use html_go\model\Config;

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
                return $content;
            },
            'action' => 'view'
        ],
        CAT_INDEX_KEY.FWD_SLASH.'edit' => (object) [
            'cb' => function (array $args): \stdClass {
                $params = [
                    'title' => get_i18n()->getText('admin.dashboard.title'),
                    'template' => 'admin-action.html',
                    'section' => CATEGORY_SECTION
                ];
                return get_model_factory()->createAdminContentObject(\array_merge($args, $params));
            },
            'action' => 'edit'
        ]
    ],
    HTTP_POST => []
];
