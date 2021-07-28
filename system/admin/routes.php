<?php

use html_go\model\Config;
use html_go\exceptions\InternalException;

return [
    HTTP_GET => [
        '/' => (object) [
            'cb' => function (array $args): \stdClass {
                $params = [
                    'title' => get_i18n()->getText('admin.dashboard.title'),
                    'template' => 'dashboard.html',
                    'section' => CATEGORY_SECTION,
                    'action' => 'view'
                ];
                return get_model_factory()->createAdminContentObject(\array_merge($args, $params));
            },
        ],
        CAT_INDEX_KEY => (object) [
            'cb' => function (array $args): \stdClass {
                $params = [
                    'title' => get_i18n()->getText('admin.dashboard.title'),
                    'template' => 'admin-list.html',
                    'section' => CATEGORY_SECTION,
                    'action' => 'view'
                ];
                $content = get_model_factory()->createAdminContentObject(\array_merge($args, $params));
                $content->list = get_categories(get_pagination_pagenumber(), get_config()->getInt(Config::KEY_ADMIN_ITEMS_PER_PAGE));
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
        ],
        CAT_INDEX_KEY.FWD_SLASH.'add' => (object) [
            'cb' => function (array $args): \stdClass {
                $params = [
                    'title' => get_i18n()->getText('admin.dashboard.title'),
                    'template' => 'admin-action.html',
                    'section' => CATEGORY_SECTION,
                    'action' => 'add',
                ];
                return get_model_factory()->createAdminContentObjectEmpty(\array_merge($args, $params));
            },
        ]
    ],
    HTTP_POST => [
        CAT_INDEX_KEY => (object) [
            'cb' => function (array $data): \stdClass {
                print_r($data);
                return new \stdClass();
            }
        ]
    ]
];
