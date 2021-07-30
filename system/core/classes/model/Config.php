<?php declare(strict_types=1);
namespace html_go\model;

use InvalidArgumentException;
use html_go\exceptions\InternalException;
use phpDocumentor\Reflection\Types\This;

final class Config extends AdminConfig
{
    public const KEY_SITE_URL = 'site.url';
    public const KEY_SITE_NAME = 'site.name';
    public const KEY_SITE_TITLE = 'site.title';
    public const KEY_SITE_DESCRIPTION = 'site.description';
    public const KEY_SITE_TAGLINE = 'site.tagline';
    public const KEY_SITE_COPYRIGHT = 'site.copyright';
    public const KEY_LANG = 'site.language';
    public const KEY_TPL_ENGINE = 'template.engine';
    public const KEY_TPL_CACHING = 'template.engine.caching';
    public const KEY_TPL_FILE_EXT = 'template.engine.file.ext';
    public const KEY_TPL_STRICT_VARS_TWIG = 'template.engine.twig.strict_variables';
    public const KEY_STATIC_INDEX = 'static.index';
    public const KEY_THEME_NAME = 'theme.name';
    public const KEY_POSTS_PERPAGE = 'blog.posts_per_page';
    public const KEY_POST_DATE_FMT = 'blog.post_date_format';
    public const KEY_DESCRIPTION_LEN = 'description.length';

    /**
     * Config constructor.
     * @param string $configRoot The root directory containing the 'config.ini' file.
     * @throws InvalidArgumentException
     * @throws InternalException
     */
    public function __construct(string $configRoot) {
        parent::__construct($configRoot);
        $this->config = $this->validateConfig($this->config);
    }

    public function getString(string $key, string $default = ''): string {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_string($var) === false) {
            return $default;
        }
        return $var;
    }

    public function getInt(string $key, int $default = -1): int {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_int($var) === false) {
            return $default;
        }
        return $var;
    }

    public function getBool(string $key, bool $default = false): bool {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_bool($var) === false) {
            return $default;
        }
        return $var;
    }

    /**
     * Check required options are set and set defaults.
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     * @throws InvalidArgumentException
     */
    private function validateConfig(array $config): array {
        if (isset($config[self::KEY_SITE_URL]) === false) {
            throw new InvalidArgumentException("Configuration option 'site.url' not set.");
        }
        $config = $this->checkSetOrDefault($config, self::KEY_SITE_TITLE, ' | HTML-go');
        $config = $this->checkSetOrDefault($config, self::KEY_SITE_NAME, 'HTML-go');
        $config = $this->checkSetOrDefault($config, self::KEY_SITE_DESCRIPTION, 'Powered by HTML-go, a databaseless, flat-file blogging platform');
        $config = $this->checkSetOrDefault($config, self::KEY_SITE_TAGLINE, 'Another HTML-go website');
        $config = $this->checkSetOrDefault($config, self::KEY_SITE_COPYRIGHT, '(c) Copyright, Your Name');
        $config = $this->checkSetOrDefault($config, self::KEY_LANG, 'en');
        $config = $this->checkSetOrDefault($config, self::KEY_TPL_ENGINE, 'twig');
        $config = $this->checkSetOrDefault($config, self::KEY_TPL_CACHING, false);
        $config = $this->checkSetOrDefault($config, self::KEY_TPL_FILE_EXT, 'twig');
        $config = $this->checkSetOrDefault($config, self::KEY_TPL_STRICT_VARS_TWIG, true);
        $config = $this->checkSetOrDefault($config, self::KEY_THEME_NAME, 'default');
        $config = $this->checkSetOrDefault($config, self::KEY_STATIC_INDEX, true);
        $config = $this->checkSetOrDefault($config, self::KEY_POSTS_PERPAGE, 5);
        $config = $this->checkSetOrDefault($config, self::KEY_POST_DATE_FMT, 'F d, Y');
        $config = $this->checkSetOrDefault($config, self::KEY_DESCRIPTION_LEN, 150);

        return $config;
    }
}
