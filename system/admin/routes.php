<?php

return [
    HTTP_GET => [
        '/' => (object) [
            'title' => 'Dashboard',
            'template' => 'dashboard.html'
        ],
        CAT_INDEX_KEY => (object) [
            'title' => 'Categories',
            'template' => 'admin-list.html'
        ]
    ],
    HTTP_POST => []
];
