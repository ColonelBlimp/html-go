<?php declare(strict_types=1);
namespace html_go\indexing;

\define('ENUM_PAGE', 'page');
\define('ENUM_CATEGORY', 'category');
\define('ENUM_POST', 'post');
\define('ENUM_TAG', 'tag');

final class NewIndexManager
{
    private string $parentDir;
    private string $commonDir;
    private string $userDataDir;

    private string $pageInxFile;
    private string $catInxFile;
    private string $postInxFile;
    private string $tagInxFile;
    private string $tag2postInxFile;
    private string $cat2postInxFile;
    private string $menuInxFile;

    private array $catIndex;
    private array $pageIndex;

    function __construct(string $parentDir) {
        if (\is_dir($parentDir) === false) {
            throw new \InvalidArgumentException("The application root cannot be found [$parentDir]");
        }
        if (($path = \realpath($parentDir)) === false) {
            throw new \RuntimeException("realpath() function failed on [$parentDir]");
        }
        $indexDir = $path.DS.'cache'.DS.'indexes';
        $this->parentDir = $path;
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
        $this->catIndex = $this->buildCategoryIndex();
        print_r($this->catIndex);
    }

    /**
     * @return array<Element>
     */
    private function buildCategoryIndex(): array {
        $index = [];
        foreach ($this->parseDirectory($this->commonDir.DS.'category'.DS.'*'.CONTENT_FILE_EXT) as $filepath) {
            $root = \substr($filepath, \strlen($this->commonDir) + 1);
            $key = \str_replace(DS, FWD_SLASH, \substr($root, 0, \strlen($root) - CONTENT_FILE_EXT_LEN));
            $index[$key] = $this->createElement($key, $filepath, ENUM_CATEGORY);
        }
        $index['category'] = $this->createElement('category', $this->commonDir.DS.'landing'.DS.'category'.DS.'index.md', ENUM_CATEGORY);
//        $this->writeIndex($this->root.DS.self::CAT_INDEX_FILE, $index);

        return $index;
    }

    private function initialize(): void {
        if ((\is_dir($this->parentDir.DS.'cache'.DS.'indexes')) === false) {
            echo '2';
            $this->reindex();
        } else {
echo '1';
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

    private function createElement(string $key, string $filepath, string $type): Element {
        if (empty($key)) {
            throw new \RuntimeException("Key is empty for [$filepath]");
        }
        switch ($type) {
            case ENUM_CATEGORY:
            case ENUM_PAGE:
                return $this->createElementClass($key, $filepath, $type);
            default:
                if (\strlen($key) < 17) {
                    throw new \InvalidArgumentException("Post content filename is too short [$key]");
                }
                $dateString = \substr($key, 0, 14);
                $start = 15;
                if (($end = \strpos($key, '_', $start)) === false) {
                    throw new \InvalidArgumentException("Content filename syntax error [$key]");
                }
                $tagList = \substr($key, $start, $end-$start);
                $title = \substr($key, $end + 1);
                $year = \substr($dateString, 0, 4);
                $month = \substr($dateString, 4, 2);
                $key = $year.FWD_SLASH.$month.FWD_SLASH.$title;
                $pathinfo = \pathinfo($filepath);
                $parts = \explode(DS, $pathinfo['dirname']);
                $cnt = \count($parts);
                return $this->createElementClass($key, $filepath, ENUM_POST, $parts[$cnt - 2], $parts[$cnt - 1], $parts[$cnt - 4], $dateString, $tagList);
        }
    }

    /**
     * Creates and populates an index Element class.
     * @param string $key The index key
     * @param string $path The filepath
     * @param string $section 'pages', 'posts', 'categories' or 'tags'
     * @param string $category
     * @param string $type
     * @param string $username
     * @param string $date
     * @param string $tagList
     * @return Element stdClass
     */
    private function createElementClass(string $key, string $path = EMPTY_VALUE, string $section = EMPTY_VALUE, string $category = EMPTY_VALUE, string $type = EMPTY_VALUE, string $username = EMPTY_VALUE, string $date = EMPTY_VALUE, string $tagList = ''): Element {
        $tags = [];
        if (!empty($tagList)) {
            $tags = \explode(',', $tagList);
        }
        $obj = new Element();
        $obj->key = $key;
        $obj->path = $path;
        $obj->section = $section;
        $obj->category = $category;
        $obj->type = $type;
        $obj->username = $username;
        $obj->date = $date;
        $obj->tags = $tags;
        return $obj;
    }
}
