<?php declare(strict_types=1);
namespace html_go\model;

final class ModelFactory
{
    static function create(object $indexElement): Content {
        print_r((array)$indexElement);
    }
}
