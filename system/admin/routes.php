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
        ADMIN_DASHBOARD_KEY => (object) [
            'cb' => 'get_dashboard_view_object'
        ],
        CAT_INDEX_KEY => (object) [
            'cb' => 'get_category_view_object'
        ],
        CAT_INDEX_KEY.FWD_SLASH.ADMIN_ACTION_EDIT => (object) [
            'cb' => 'get_category_edit_object'
        ],
        CAT_INDEX_KEY.FWD_SLASH.ADMIN_ACTION_ADD => (object) [
            'cb' => 'get_category_add_object'
        ],
        CAT_INDEX_KEY.FWD_SLASH.ADMIN_ACTION_DELETE => (object) [
            'cb' => 'get_category_delete_object'
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
                $action = $data['action'];
                print_r($data);
                exit;
                return new \stdClass();
            }
        ]
    ]
];
