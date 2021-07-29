<?php

use html_go\model\Config;
use html_go\exceptions\InternalException;

/*
 * All the anon funcs might need to be changed to real as they will need to be reproduced tohandle
 * a 'cancel'. Depending on the action, the sdtClass returned will be different for each type of action.
 * Thus the need for the anon functions to be real and also input fields to be populated.
 */
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
            'cb' => 'get_category_view_object'

            /*
            function (array $args): \stdClass {
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
            */
        ],
        CAT_INDEX_KEY.FWD_SLASH.'edit' => (object) [
            'cb' => 'get_category_edit_object'

            /*
            function (array $args): \stdClass {
                if (empty($args['id'])) {
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
            */
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
                if (empty($data['cancel']) === false) {
                    header('Location: '.get_config()->getString(Config::KEY_SITE_URL).FWD_SLASH.$data['context'].FWD_SLASH.'category');
                    return new \stdClass();
                }
                if (empty($data['action'])) {
                    return new \stdClass(); // Force not-found 404
                }

                switch ($data['action']) {
                    case 'cancel':
                        break;
                    default:
                        echo 'ACTION: '.$data['action'];
                        break;
                }
                print_r($data);
                exit;
                return new \stdClass();
            }
        ]
    ]
];
