<?php declare(strict_types=1);
namespace html_go\indexing;

final class Element
{
    function __construct(
        public string $key,
        public string $path,
        public string $section,
        public string $category,
        public string $type,
        public string $username,
        public string $date,
        public array $tagList) {
    }
}
