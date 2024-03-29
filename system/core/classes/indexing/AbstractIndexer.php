<?php declare(strict_types=1);
namespace html_go\indexing;

use InvalidArgumentException;
use html_go\exceptions\InternalException;

abstract class AbstractIndexer
{
    protected string $parentDir;
    protected string $commonDir;
    protected string $userDataDir;

    protected string $pageInxFile;
    protected string $catInxFile;
    protected string $postInxFile;
    protected string $tagInxFile;
    protected string $tag2postInxFile;
    protected string $cat2postInxFile;
    protected string $menuInxFile;

    public function __construct(string $parentDir) {
        if (($path = \realpath($parentDir)) === false) {
            throw new InternalException("realpath() function failed on [$parentDir]"); // @codeCoverageIgnore
        }
        $this->parentDir = $path;

        $this->commonDir = $path.DS.'content'.DS.'common';
        if (\is_dir($this->commonDir) === false) {
            throw new InvalidArgumentException("The content/common directory cannot be found [$this->commonDir]");
        }

        $this->userDataDir = $path.DS.'content'.DS.'user-data';
        if (\is_dir($this->userDataDir) === false) {
            throw new InvalidArgumentException("The content/user-data directory cannot be found [$this->userDataDir]");
        }
        $indexDir = $path.DS.'cache'.DS.'indexes';
        $this->pageInxFile = $indexDir.DS.'page.inx';
        $this->catInxFile = $indexDir.DS.'category.inx';
        $this->postInxFile = $indexDir.DS.'post.inx';
        $this->tagInxFile = $indexDir.DS.'tag.inx';
        $this->tag2postInxFile = $indexDir.DS.'tag2post.inx';
        $this->cat2postInxFile = $indexDir.DS.'cat2post.inx';
        $this->menuInxFile = $indexDir.DS.'menu.inx';
    }

    /**
     * Load the given index file.
     * @param string $filename
     * @throws InternalException
     * @throws InvalidArgumentException
     * @return array<mixed>
     */
    protected function loadIndex(string $filename): array {
        if (\file_exists($filename) === false) {
            throw new InvalidArgumentException("Index file does not exist [$filename]. Call 'redindex()'"); // @codeCoverageIgnore
        }
        if (($data = \file_get_contents($filename)) === false) {
            throw new InternalException("file_get_contents() failed [$filename]"); // @codeCoverageIgnore
        }
        if (($data = \unserialize($data)) === false) {
            throw new InternalException("unserialize() failed [$filename]"); // @codeCoverageIgnore
        }
        return $data;
    }

    /**
     * Recursively scans a folder heirarchy returning the all the files and folders
     * in an array.
     * @return array<int, string>
     * @throws InternalException
     */
    protected function scanDirectory(string $rootDir): array {
        static $files = [];
        if (($handle = \opendir($rootDir)) === false) {
            throw new InternalException("opendir() failed [$rootDir]"); // @codeCoverageIgnore
        }
        while (($entry = \readdir($handle)) !== false) {
            $path = $rootDir.DS.$entry;
            if (\is_dir($path)) {
                if ($entry === '.' || $entry === '..') {
                    continue;
                }
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
     * @throws InternalException
     */
    protected function parseDirectory(string $pattern): array {
        if (($files = \glob($pattern, GLOB_NOSORT)) === false) {
            throw new InternalException("glob() failed [$pattern]"); // @codeCoverageIgnore
        }
        return $files;
    }

    /**
     * Writes data to an index file, creating the file if necessary.
     * @param string $filepath
     * @param array<mixed> $index
     * @throws InternalException
     */
    protected function writeIndex(string $filepath, array $index): void {
        $index = \serialize($index);
        if (\file_put_contents($filepath, print_r($index, true)) === false) {
            throw new InternalException("file_put_contents() failed [$filepath]"); // @codeCoverageIgnore
        }
    }

    /**
     * Creates and populates an index Element class.
     * @param string $key The index key
     * @param string $path The filepath
     * @param string $section 'pages', 'posts', 'categories' or 'tags'
     * @param string $optional When populating with variable arguments, use the
     * following <b>named parameters<b>:
     * <ul>
     *   <li>type:</li>
     *   <li>category:</li>
     *   <li>username:</li>
     *   <li>date:</li>
     *   <li>tags:</li>
     * </ul>
     * @return \stdClass
     */
    protected function createElementClass(string $key, string $path, string $section, string ...$optional): \stdClass {
        if (\in_array($section, [CATEGORY_SECTION, TAG_SECTION, PAGE_SECTION, POST_SECTION]) === false) {
            throw new \InvalidArgumentException("Unknown section [$section]");
        }
        $obj = new \stdClass();
        $obj->key = $key;
        $obj->path = $path;
        $obj->section = $section;
        $obj->type = $this->checkSetOrDefault($optional, 'type', EMPTY_VALUE);
        $obj->category = $this->checkSetOrDefault($optional, 'category', EMPTY_VALUE);
        $obj->username = $this->checkSetOrDefault($optional, 'username', EMPTY_VALUE);
        $obj->timestamp = $this->checkSetOrDefault($optional, 'timestamp', EMPTY_VALUE);

        $tags = [];
        if (!empty($optional['tags'])) {
            $tags = \explode(',', $optional['tags']);
        }

        $obj->tags = $tags;
        return $obj;
    }

    /**
     * Create an Element object. The type of element and what properties are poplutated and
     * persisted to the index is determined by the <code>section</code>.
     * @param string $key
     * @param string $filepath
     * @param string $section
     * @throws InternalException
     * @throws InvalidArgumentException
     * @return \stdClass
     */
    protected function createElement(string $key, string $filepath, string $section): \stdClass {
        if (empty($key) || empty($section)) {
            throw new \InvalidArgumentException("A parameter is empty for [$key][$filepath][$section]"); // @codeCoverageIgnore
        }

        if( $section === POST_SECTION) {
                $uriDateStringTagList = $this->getPostUriDateStringAndTagListFromIndexKey($key);
                $typeCatUsername = $this->getTypeCategoryUsernameFromFilepath($filepath);
                return $this->createElementClass($uriDateStringTagList[0], $filepath, POST_SECTION, type: $typeCatUsername[0], category: $typeCatUsername[1], username: $typeCatUsername[2], timestamp: $uriDateStringTagList[1], tags: $uriDateStringTagList[2]);
        }
        return $this->createElementClass($key, $filepath, $section);
    }

    /**
     * Checks if the given key is set in the given array. If so, returns the value,
     * otherwise returns the default value.
     * @param array<mixed> $ar
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function checkSetOrDefault(array $ar, string $key, mixed $default): mixed {
        if (isset($ar[$key])) {
            return $ar[$key];
        }
        return $default;
    }

    /**
     *
     * @param string $key
     * @throws InvalidArgumentException
     * @return array<string>
     */
    private function getPostUriDateStringAndTagListFromIndexKey(string $key): array {
        if (\strlen($key) < 17) {
            throw new InvalidArgumentException("Post content filename is too short [$key]"); // @codeCoverageIgnore
        }
        $dateString = \substr($key, 0, TIMESTAMP_LEN);
        $start = 15;
        if (($end = \strpos($key, '_', $start)) === false) {
            throw new InvalidArgumentException("Post content filename syntax error [$key]"); // @codeCoverageIgnore
        }
        $tagList = \substr($key, $start, $end - $start);
        $title = \substr($key, $end + 1);
        $year = \substr($dateString, 0, 4);
        $month = \substr($dateString, 4, 2);
        $uri = $year.FWD_SLASH.$month.FWD_SLASH.$title;
        return [$uri, $dateString, $tagList];
    }

    /**
     * Extract the post's type, category and username from its filepath.
     * @param string $filepath
     * @return array<string> value index: [0] = type, [1] = category, [2] = username
     */
    private function getTypeCategoryUsernameFromFilepath(string $filepath): array {
        $pathinfo = \pathinfo($filepath);
        $parts = \explode(DS, $pathinfo['dirname']);
        $cnt = \count($parts);
        // type,  category, username
        return [$parts[$cnt - 1], CATEGORY_SECTION.FWD_SLASH.$parts[$cnt - 2], $parts[$cnt - 4]];
    }

    /**
     * Reindex the whole system. Generally called when new content has been added.
     */
    public abstract function reindex(): void;
}
