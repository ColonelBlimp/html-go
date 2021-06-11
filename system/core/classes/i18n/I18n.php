<?php declare(strict_types=1);
namespace html_go\i18n;

final class I18n
{
    /**
     * @var array<string, string>
     */
    private array $bundle = [];

    function __construct(string $bundlePath) {
        if (!\file_exists($bundlePath)) {
            throw new \InvalidArgumentException("i18n bundle file not found [$bundlePath]");
        }
        $this->bundle = include $bundlePath;
    }

    function getText(string $key): string {
        if (\array_key_exists($key, $this->bundle)) {
            return $this->bundle[$key];
        }
        return '!'.$key.'!';
    }
}
