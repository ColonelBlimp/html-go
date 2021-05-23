<?php declare(strict_types=1);
namespace html_go\indexing;

if (!\defined('MODE')) {
    \define('MODE', 0777);
}
if (!\defined('EMPTY_VALUE')) {
    \define('EMPTY_VALUE', '<empty>');
}
//TODO: Replace this with ENUMS from php 8.1
\define('ENUM_PAGE', 'pages');
\define('ENUM_CATEGORY', 'categories');
\define('ENUM_POST', 'posts');
\define('ENUM_TAG', 'tags');

final class IndexManager
{
    private const CATEGORIES_DIR = 'content'.DS.'common'.DS.'categories';
    private const PAGES_DIR = 'content'.DS.'common'.DS.'pages';
    private const INDEX_DIR = 'cache'.DS.'indexes';
    private const USER_DATA_DIR = 'content'.DS.'user-data';

    private string $root;

    /**
     * IndexManager constructor.
     * @param string $root
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    function __construct(string $root) {
        if (($tmp = \realpath($root)) === false) {
            throw new \InvalidArgumentException("Unable to validate the location of the 'content' directory [$root]");
        }
        $this->root = $tmp;
        if (!\is_dir($this->root.DS.self::CATEGORIES_DIR)) {
            $tmp = $this->root.DS.self::CATEGORIES_DIR;
            throw new \RuntimeException(
                "Content directory format is invalid. Directory does not exist [$tmp]");
        }
        if (!\is_dir($this->root.DS.self::PAGES_DIR)) {
            $tmp = $this->root.DS.self::PAGES_DIR; // @codeCoverageIgnore
            throw new \RuntimeException("Content directory format is invalid. Directory does not exist [$tmp]"); // @codeCoverageIgnore
        }
        if (!\is_dir($this->root.DS.self::USER_DATA_DIR)) {
            $tmp = $this->root.DS.self::USER_DATA_DIR; // @codeCoverageIgnore
            throw new \RuntimeException("Content directory format is invalid. Directory does not exist [$tmp]"); // @codeCoverageIgnore
        }
        if (!\file_exists($this->root.DS.self::INDEX_DIR)) {
            if (\mkdir($this->root.DS.self::INDEX_DIR, MODE, true) === false) {
                $tmp = $this->root.DS.self::INDEX_DIR;
                throw new \RuntimeException("Unable to create directory [$tmp]");
            }
        }
        $this->Initialize();
    }

    /**
     * Initialize the indexing system and create the indexes if needed.
     */
    private function Initialize(): void {
        if (\file_exists($this->root.DS.self::INDEX_DIR.DS.'slugindex.inx') === false) {
            $this->buildCategoryIndex();
            $this->buildPageIndex();
            $this->buildPostsIndex();
        }
    }

    /**
     * Scans the <i>content/common/categories</i> folder creating and indexing all the files.
     */
    private function buildCategoryIndex(): void {
        $index = [];
        foreach ($this->parseDirectory($this->root.DS.self::CATEGORIES_DIR.DS.'*'.CONTENT_FILE_EXT) as $filepath) {
            $key = 'category'.FWD_SLASH.\pathinfo($filepath, PATHINFO_FILENAME);
            $index[] = $this->createElement($filepath, $key);
        }
        $this->writeIndex($this->root.DS.self::INDEX_DIR.DS.'categories.inx', $index);
    }

    /**
     * Scans the <i>content/pages</i> folder creating and indexing all the files and folders.
     */
    private function buildPageIndex(): void {
        $index = [];
        $pagesRoot = $this->root.DS.self::PAGES_DIR;
        $len = \strlen($pagesRoot) + 1;
        $pages = $this->scanDirectory($pagesRoot);
        \sort($pages);
        foreach ($pages as $filepath) {
            $key = \substr(\substr($filepath, $len), 0, -3);
            $index[] = $this->createElement($filepath, $key);
        }
        $this->writeIndex($this->root.DS.self::INDEX_DIR.DS.'pages.inx', $index);
    }

    /**
     * Scans the <i>content/user-data/[username]/posts</i> folder creating and indexing all files.
     */
    private function buildPostsIndex(): void {
        $index = [];
        foreach ($this->parseDirectory($this->root.DS.self::USER_DATA_DIR.DS.'*'.DS.'posts'.DS.'*'.DS.'*'.DS.'*'.CONTENT_FILE_EXT) as $filepath) {
            echo $filepath . PHP_EOL;
        }
    }

    /**
     * Recursively scans a folder heirarchy.
     * @return array<int, string>
     */
    private function scanDirectory(string $rootDir): array {
        static $files = [];
        if (($handle = \opendir($rootDir)) === false) {
            throw new \ErrorException("opendir() failed [$rootDir]"); // @codeCoverageIgnore
        }
        while (($entry = \readdir($handle)) !== false) {
            $path = $rootDir.DS.$entry;
            if (\is_dir($path)) {
                if ($entry === '.' || $entry === '..') continue;
                $this->scanDirectory($path);
                continue;
            }
            $files[] = $path;
        }
        \closedir($handle);
        return $files;
    }

    /**
     * @return array<int, string>
     */
    private function parseDirectory(string $pattern): array {
        if (($files = \glob($pattern, GLOB_NOSORT)) === false) {
            throw new \RuntimeException("glob() failed [$pattern]"); // @codeCoverageIgnore
        }
        return $files;
    }

    /**
     * Parses the given filepath parameter and creates a <code>stdClass</code> object to represent
     * an index element.
     * @param string $filepath
     * @param string $key
     * @throws \InvalidArgumentException
     * @return object
     */
    private function createElement(string $filepath, string $key): object {
        $pathinfo = \pathinfo($filepath);
        if ($key === null) {
            $key = $pathinfo['filename'];
        }
        if (\strpos($filepath, self::CATEGORIES_DIR) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_CATEGORY);
        }
        if (\strpos($filepath, self::PAGES_DIR) !== false) {
            return $this->createElementClass($key, $filepath, ENUM_PAGE);
        }
        if (\strlen($key) < 17) {
            throw new \InvalidArgumentException("Content filename is too short [$key]");
        }
        $dateString = \substr($key, 0, 14);
        $start = 15;
        if (($end = \strpos($key, '_', $start)) === false) {
            throw new \InvalidArgumentException("Content filename syntax error [$key]");
        }
        $tagList = \substr($key, $start, $end-$start);
        $key = \substr($key, $end + 1);
        $parts = \explode(DS, $pathinfo['dirname']);
        $cnt = \count($parts);
        return $this->createElementClass($key, $filepath, ENUM_POST, $parts[$cnt - 2], $parts[$cnt - 1], $parts[$cnt - 4], $dateString, $tagList);
    }

    /**
     * Creates and populates a stdClass for an index element.
     * @param string $key
     * @param string $path
     * @param string $section
     * @param string $category
     * @param string $type
     * @param string $username
     * @param string $date
     * @param string $tagList
     * @return object stdClass
     */
    private function createElementClass(string $key, string $path, string $section, string $category = EMPTY_VALUE, string $type = EMPTY_VALUE, string $username = EMPTY_VALUE, string $date = EMPTY_VALUE, string $tagList = ''): object {
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
        return $obj;
    }

    /**
     * Writes data to an index file, creating the file if necessary.
     * @param array<mixed> $index
     */
    private function writeIndex(string $filepath, array $index): void {
        $index = \serialize($index);
        if (\file_put_contents($filepath, print_r($index, true)) === false) {
            throw new \RuntimeException("file_put_contents() failed [$filepath]"); // @codeCoverageIgnore
        }
    }
}
