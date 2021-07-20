<?php
namespace html_go\model;

use InvalidArgumentException;
use html_go\exceptions\InternalException;

abstract class AdminConfig
{
    public const KEY_ADMIN_CONTEXT = 'admin.context';
    public const KEY_ADMIN_THEME_NAME = 'admin.theme.name';
    public const KEY_ADMIN_ITEMS_PER_PAGE = 'admin.items.per_page';

    /**
     * @var array<string, string>
     */
    protected array $config;

    public function __construct(string $configRoot) {
        $configFile = $configRoot.DS.'config.ini';
        if (!\is_file($configFile)) {
            throw new InvalidArgumentException("Configuration INI file not found [$configFile]");
        }
        if (($config = \parse_ini_file($configFile, false, INI_SCANNER_TYPED)) === false) {
            throw new InternalException("parse_ini_file() failed [$configFile]"); // @codeCoverageIgnore
        }
        $this->config = $this->validateAdminConfig($config);
    }

    protected function checkAndGet(string $key): mixed {
        if (empty($this->config[$key])) {
            return null;
        }
        return $this->config[$key];
    }

    /**
     * @param array<mixed> $config
     * @param string $key
     * @param mixed $default
     * @return array<mixed>
     */
    protected function checkSetOrDefault(array $config, string $key, mixed $default): array {
        if (empty($config[$key]) === false) {
            return $config;
        }
        $config[$key] = $default;
        return $config;
    }

    /**
     * Check required options are set and set defaults.
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     * @throws InvalidArgumentException
     */
    protected function validateAdminConfig(array $config): array {
        $config = $this->checkSetOrDefault($config, self::KEY_ADMIN_CONTEXT, 'admin');
        $config = $this->checkSetOrDefault($config, self::KEY_ADMIN_THEME_NAME, 'default');
        $config = $this->checkSetOrDefault($config, self::KEY_ADMIN_ITEMS_PER_PAGE, 5);
        return $config;
    }
}
