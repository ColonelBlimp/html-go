<?php declare(strict_types=1);
namespace html_go\traits;

use html_go\model\Content;

if (!\defined('EMPTY_VALUE')) {
    \define('EMPTY_VALUE', '<empty>');
}

trait ElementCreator
{
    /**
     * Creates and populates a stdClass for an index element.
     * @param string $key The index key
     * @param string $path The filepath
     * @param string $section 'pages', 'posts', 'categories' or 'tags'
     * @param string $category
     * @param string $type
     * @param string $username
     * @param string $date
     * @param string $tagList
     * @param array<Content> $listing
     * @return object stdClass
     */
    private function createElementClass(string $key = EMPTY_VALUE, string $path = EMPTY_VALUE, string $section = EMPTY_VALUE, string $category = EMPTY_VALUE, string $type = EMPTY_VALUE, string $username = EMPTY_VALUE, string $date = EMPTY_VALUE, string $tagList = '', array $listing = []): object {
        $tags = [];
        if (!empty($tagList)) {
            $tags = \explode(',', $tagList);
        }
        $obj = new \stdClass();
        $obj->key = $key;
        $obj->path = $path;
        $obj->section = $section;
        $obj->category = $category;
        $obj->type = $type;
        $obj->username = $username;
        $obj->date = $date;
        $obj->tags = $tags;
        $obj->listing = $listing;
        return $obj;
    }
}
