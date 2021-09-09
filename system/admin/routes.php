<?php declare(strict_types=1);

use html_go\model\Config;
use html_go\Utils;

return [
    HTTP_GET => [
        ADMIN_DASHBOARD_KEY => (object) [
            'cb' => 'get_dashboard_view_content_object'
        ],
        CAT_INDEX_KEY => (object) [
            'cb' => 'get_category_listview_content_object'
        ],
        CAT_INDEX_KEY.FWD_SLASH.ADMIN_ACTION_EDIT => (object) [
            'cb' => 'get_category_edit_object'
        ],
        CAT_INDEX_KEY.FWD_SLASH.ADMIN_ACTION_ADD => (object) [
            'cb' => 'get_category_add_content_object'
        ],
        CAT_INDEX_KEY.FWD_SLASH.ADMIN_ACTION_DELETE => (object) [
            'cb' => 'get_category_delete_object'
        ]
    ],
    HTTP_POST => [
        CAT_INDEX_KEY => (object) [
            'cb' => function (array $data): \stdClass {
                if (empty($data[ADMIN_ACTION_CANCEL]) === false) {
                    header('Location: '.get_config()->getString(Config::KEY_SITE_URL).FWD_SLASH.$data[ADMIN_CONTEXT_STR].FWD_SLASH.'category');
                    return new \stdClass();
                }
                if (empty($data[ADMIN_ACTION_STR])) {
                    return new \stdClass(); // Force not-found 404
                }
                $content = new \stdClass();
                $action = $data[ADMIN_ACTION_STR];
                switch ($action) {
                    case ADMIN_ACTION_ADD:
                        if (save_category($data)) {
                            header('Location: '.get_config()->getString(Config::KEY_SITE_URL).FWD_SLASH.$data[ADMIN_CONTEXT_STR].FWD_SLASH.'category');
                        } else {
                            $content = get_category_add_content_object();
                            $content->list[0] = (object)$data;
                            print_r($data);
                        }
                        break;
                    case ADMIN_ACTION_EDIT:
                        if (update_category($data)) {
                            header('Location: '.get_config()->getString(Config::KEY_SITE_URL).FWD_SLASH.$data[ADMIN_CONTEXT_STR].FWD_SLASH.'category');
                        } else {
                            exit('Update: validation failed');
                        }
                        break;
                    case ADMIN_ACTION_DELETE:
                        break;
                    default:
                        break;
                }
                return $content;
            }
        ]
    ]
];
