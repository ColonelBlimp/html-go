<?php declare(strict_types=1);
namespace html_go\indexing;

final class Element
{
    private string $key;
    private string $path;
    private string $section;
    private string $category;
    private string $type;
    private string $username;
    private string $date;
    private array $tagList;

    private function __construct() {
    }

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
    static function fromData(string $key, string $path, string $section, string $category, string $type, string $username, string $date, array $tagList): Element {
        $instance = new self();
        $instance->loadFromData($key, $path, $section, $category, $type, $username, $date, $tagList);
        return $instance;
    }

    static function empty(): Element {
        return new self();
    }

    private function loadFromData(string $key, string $path, string $section, string $category, string $type, string $username, string $date, array $tagList): void {
        $this->key = $key;
        $this->path = $path;
        $this->section = $section;
        $this->category = $category;
        $this->type = $type;
        $this->username = $username;
        $this->date = $date;
        $this->tagList = $tagList;
    }

    /**
     * The unique name for this content.
     * @return string
     */
    function getKey(): string {
        return $this->key;
    }

    /**
     * The fullpath for the content file.
     * @return string
     */
    function getPath(): string {
        return $this->path;
    }

    /**
     * The section for this content. This will be either 'pages', 'posts' or 'categories'
     * @return string
     */
    function getSection(): string {
        return $this->section;
    }

    /**
     * This relates to 'posts' only and indicates the category for post.
     * @return string
     */
    function getCategory(): string {
        return $this->category;
    }

    /**
     * This relates to 'posts' only and indicates the type of post (regular, image, etc).
     * @return string
     */
    function getType(): string {
        return $this->type;
    }

    /**
     * The system username under which this content is located. This relates to 'posts' only.
     * @return string
     */
    function getUsername(): string {
        return $this->username;
    }

    /**
     * The date string from the filename. This relates to 'posts' only.
     * @return string
     */
    function getDate(): string {
        return $this->date;
    }

    /**
     * Returns an array of tag slugs. This relates to 'posts' only.
     * @return array<int, string>
     */
    function getTags(): array {
        return $this->tagList;
    }
}
