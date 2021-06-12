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

    function __construct(string $parentDir) {
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

        if ((\is_dir($this->parentDir.DS.'cache'.DS.'indexes')) === false) {
            $dir = $this->parentDir.DS.'cache'.DS.'indexes';
            if (\mkdir($dir, MODE, true) === false) {
                throw new InternalException("Unable to create cache/indexes directory [$dir]"); // @codeCoverageIgnore
            }
            $this->reindex();
        }
    }

    /**
     * Load the given index file.
     * @param string $filename
     * @throws InternalException
     * @throws InvalidArgumentException
     * @return array<string, Element>
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

    abstract function reindex(): void;
}
