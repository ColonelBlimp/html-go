<?php declare(strict_types=1);

use html_go\model\Config;

function get_site_object(): \stdClass {
    $config = get_config();
    $site = new \stdClass();
    $site->url = $config->getString(Config::KEY_SITE_URL);
    $site->name = $config->getString(Config::KEY_SITE_NAME);
    $site->title = $config->getString(Config::KEY_SITE_TITLE);
    $site->description = $config->getString(Config::KEY_SITE_DESCRIPTION);
    $site->tagline = $config->getString(Config::KEY_SITE_TAGLINE);
    $site->copyright = $config->getString(Config::KEY_SITE_COPYRIGHT);
    $site->language = $config->getString(Config::KEY_LANG);
    $site->theme = $config->getString(Config::KEY_THEME_NAME);
    $site->tpl_engine = $config->getString(Config::KEY_TPL_ENGINE);
    return $site;
}
