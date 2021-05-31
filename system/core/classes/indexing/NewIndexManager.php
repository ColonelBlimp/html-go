<?php declare(strict_types=1);
namespace html_go\indexing;

final class NewIndexManager
{
    private string $appRoot;
    private string $indexDir;

    function __construct(string $appRoot) {
        if (\is_dir($appRoot) === false) {
            throw new \InvalidArgumentException("The application root cannot be found [$appRoot]");
        }
        if (($real = \realpath($appRoot)) === false) {
            throw new \RuntimeException("realpath() function failed on [$appRoot]");
        }
        $this->appRoot = $real;
        $this->indexDir = $real.DS.'cache'.DS.'indexes';
        $this->initialize();
    }

    function reindex(): void {

    }
    private function initialize(): void {
        if ((\is_dir($this->indexDir)) === false) {
            $this->reindex();
        } else {

        }
    }

    private function loadIndexe(): array {
        return [];
    }
}
