<?php declare(strict_types=1);
namespace html_go\indexing;

final class Element
{
    /**
     * @param string $key
     * @param string $path
     * @param string $section
     * @param string $category
     * @param string $type
     * @param string $username
     * @param string $date
     * @param array<int, string> $tagList
     */
    function __construct(
        private string $key,
        private string $path,
        private string $section,
        private string $category,
        private string $type,
        private string $username,
        private string $date,
        private array $tagList) {
    }
    function getKey(): string {
        return $this->key;
    }
    function getPath(): string {
        return $this->path;
    }
    function getSection(): string {
        return $this->section;
    }
    function getCategory(): string {
        return $this->category;
    }
    function getType(): string {
        return $this->type;
    }
    function getUsername(): string {
        return $this->username;
    }
    function getDate(): string {
        return $this->date;
    }
    function getTags(): array {
        return $this->tagList;
    }
}
