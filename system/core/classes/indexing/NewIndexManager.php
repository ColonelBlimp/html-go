<?php declare(strict_types=1);
namespace html_go\indexing;

final class NewIndexManager
{
    private string $appRoot;
    private string $commonDir;
    private string $userDataDir;

    private string $pageInxFile;
    private string $catInxFile;
    private string $postInxFile;
    private string $tagInxFile;
    private string $tag2postInxFile;
    private string $cat2postInxFile;
    private string $menuInxFile;

    private array $pageIndex;

    function __construct(string $appRoot) {
        if (\is_dir($appRoot) === false) {
            throw new \InvalidArgumentException("The application root cannot be found [$appRoot]");
        }
        if (($path = \realpath($appRoot)) === false) {
            throw new \RuntimeException("realpath() function failed on [$appRoot]");
        }
        $indexDir = $path.DS.'cache'.DS.'indexes';
        $this->appRoot = $path;
        $this->commonDir = $path.DS.'content'.DS.'common';
        $this->userDataDir = $path.DS.'content'.DS.'user-data';

        $this->pageInxFile = $indexDir.DS.'page.inx';
        $this->catInxFile = $indexDir.DS.'category.inx';
        $this->postInxFile = $indexDir.DS.'post.inx';
        $this->tagInxFile = $indexDir.DS.'tag.inx';
        $this->tag2postInxFile = $indexDir.DS.'tag2post.inx';
        $this->cat2postInxFile = $indexDir.DS.'cat2post.inx';
        $this->menuInxFile = $indexDir.DS.'menu.inx';

        $this->initialize();
    }

    function reindex(): void {

    }

    /**
     * @return array<Element>
     */
    private function buildCategoryIndex(): array {
        $index = [];
        foreach ($this->parseDirectory($this->commonDir.DS.'category'.DS.'*'.CONTENT_FILE_EXT) as $filepath) {
            $key = 'category'.FWD_SLASH.\pathinfo($filepath, PATHINFO_FILENAME);
            $index[$key] = $this->createElement($filepath, $key);
        }
        $this->writeIndex($this->root.DS.self::CAT_INDEX_FILE, $index);
        return $index;
    }

    private function initialize(): void {
        if ((\is_dir($this->indexDir)) === false) {
            $this->reindex();
        } else {

        }
    }

    private function loadIndex(string $filename): array {
        if (\file_exists($filename) === false) {
            throw new \RuntimeException("Index file does not exist [$filename]. Call 'redindex()'");
        }
        if (($data = \file_get_contents($filename)) === false) {
            throw new \ErrorException("file_get_contents() failed [$filename]"); // @codeCoverageIgnore
        }
        if (($data = \unserialize($data)) === false) {
            throw new \ErrorException("unserialize() failed [$filename]"); // @codeCoverageIgnore
        }
        return $data;
    }

    /**
     * @return array<int, string>
     */
    private function parseDirectory(string $pattern): array {
        if (($files = \glob($pattern, GLOB_NOSORT)) === false) {
            throw new \ErrorException("glob() failed [$pattern]"); // @codeCoverageIgnore
        }
        return $files;
    }
}
