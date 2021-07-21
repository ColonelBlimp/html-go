<?php

return [
    HTTP_GET => [
        '/' => (object) [
            'title' => get_i18n()->getText('admin.dashboard.title'),
            'template' => 'dashboard.html'
        ],
        CAT_INDEX_KEY => (object) [
            'cb' => function (int $pagenum=1, int $perpage=5): mixed {
            return get_categories($pagenum, $perpage);
            },
            'title' => get_i18n()->getText('admin.toolbar.category.title'),
            'template' => 'admin-list.html',
            'section' => CATEGORY_SECTION,
            'action' => 'view'
        ],
        CAT_INDEX_KEY.FWD_SLASH.'edit' => (object) [
            'cb' => function (int $id): mixed {
                return null;
            },
            'title' => get_i18n()->getText('admin.toolbar.category.title'),
            'template' => 'admin-single.html',
            'section' => CATEGORY_SECTION,
            'action' => 'edit'
        ]
    ],
    HTTP_POST => []
];
