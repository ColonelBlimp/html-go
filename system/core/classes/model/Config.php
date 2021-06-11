<?php declare(strict_types=1);
namespace html_go\model;

use InvalidArgumentException;
use html_go\exceptions\InternalException;

final class Config
{
    const KEY_SITE_URL = 'site.url';
    const KEY_SITE_NAME = 'site.name';
    const KEY_SITE_TITLE = 'site.title';
    const KEY_SITE_DESCRIPTION = 'site.description';
    const KEY_SITE_TAGLINE = 'site.tagline';
    const KEY_SITE_COPYRIGHT = 'site.copyright';
    const KEY_LANG = 'site.language';
    const KEY_TPL_ENGINE = 'template.engine';
    const KEY_TPL_CACHING = 'template.engine.caching';
    const KEY_TPL_FILE_EXT = 'template.engine.file.ext';
    const KEY_TPL_STRICT_VARS_TWIG = 'template.engine.twig.strict_variables';
    const KEY_STATIC_INDEX = 'static.index';
    const KEY_THEME_NAME = 'theme.name';
    const KEY_POSTS_PERPAGE = 'blog.posts_per_page';
    const KEY_POST_DATE_FMT = 'blog.post_date_format';

    /**
     * @var array<string, string>
     */
    private array $config;

    /**
     * Config constructor.
     * @param string $configRoot The root directory containing the 'config.ini' file.
     * @throws InvalidArgumentException
     * @throws InternalException
     */
    function __construct(string $configRoot) {
        $configFile = $configRoot.DS.'config.ini';
        if (!\is_file($configFile)) {
            throw new InvalidArgumentException("Configuration INI file not found [$configFile]");
        }
        if (($config = \parse_ini_file($configFile, false, INI_SCANNER_TYPED)) === false) {
            throw new InternalException("parse_ini_file() failed [$configFile]"); // @codeCoverageIgnore
        }
        $this->config = $this->validateConfig($config);
    }

    function getString(string $key, string $default = ''): string {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_string($var) === false) {
            return $default;
        }
        return $var;
    }

    function getInt(string $key, int $default = -1): int {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_int($var) === false) {
            return $default;
        }
        return $var;
    }

    function getBool(string $key, bool $default = false): bool {
        $var = $this->checkAndGet($key);
        if (empty($var) || \is_bool($var) === false) {
            return $default;
        }
        return $var;
    }

    private function checkAndGet(string $key): mixed {
        if (isset($this->config[$key]) === false) {
            return null;
        }
        return $this->config[$key];
    }

    /**
     * Check required options are set and set defaults.
     * @param array<string, string> $config
     * @return array<string, mixed>
     * @throws InvalidArgumentException
     */
    private function validateConfig(array $config): array {
        if (isset($config[self::KEY_SITE_URL]) === false) {
            throw new InvalidArgumentException("Configuration option 'site.url' not set.");
        }
        if (isset($config[self::KEY_SITE_TITLE]) === false) {
            $config[self::KEY_SITE_TITLE] = ' | HTML-go';
        }
        if (isset($config[self::KEY_SITE_NAME]) === false) {
            $config[self::KEY_SITE_NAME] = 'Another HTML-go Site';
        }
        if (isset($config[self::KEY_SITE_DESCRIPTION]) === false) {
            $config[self::KEY_SITE_DESCRIPTION] = 'Powered by HTML-go, a databaseless, flat-file blogging platform';
        }
        if (isset($config[self::KEY_SITE_TAGLINE]) === false) {
            $config[self::KEY_SITE_TAGLINE] = 'Another HTML-go website';
        }
        if (isset($config[self::KEY_SITE_COPYRIGHT]) === false) {
            $config[self::KEY_SITE_COPYRIGHT] = '(c) Copyright, Your Name';
        }
        if (isset($config[self::KEY_LANG]) === false) {
            $config[self::KEY_LANG] = 'en';
        }
        if (isset($config[self::KEY_TPL_ENGINE]) === false) {
            $config[self::KEY_TPL_ENGINE] = 'twig';
        }
        if (isset($config[self::KEY_TPL_CACHING]) === false) {
            $config[self::KEY_TPL_CACHING] = false;
        }
        if (isset($config[self::KEY_TPL_FILE_EXT]) === false) {
            $config[self::KEY_TPL_FILE_EXT] = 'twig';
        }
        if (isset($config[self::KEY_TPL_STRICT_VARS_TWIG]) === false) {
            $config[self::KEY_TPL_STRICT_VARS_TWIG] = true;
        }
        if (isset($config[self::KEY_THEME_NAME]) === false) {
            $config[self::KEY_THEME_NAME] = 'default';
        }
        if (isset($config[self::KEY_STATIC_INDEX]) === false) {
            $config[self::KEY_STATIC_INDEX] = true;
        }
        if (isset($config[self::KEY_POSTS_PERPAGE]) === false) {
            $config[self::KEY_POSTS_PERPAGE] = 5;
        }
        if (isset($config[self::KEY_POST_DATE_FMT]) === false) {
            $config[self::KEY_POST_DATE_FMT] = 'jS M Y';
        }
        return $config;
    }
}
