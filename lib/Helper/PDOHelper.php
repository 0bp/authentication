<?php 

declare(strict_types=1);

namespace bearonahill\Helper;

class PDOHelper
{
    public static function prependPlaceholder(array $items)
    {
        return array_map(function($item) { return ':'.$item; }, $items);
    }

    public static function backtickItems(array $items)
    {
        return array_map(function($item) { return '`'.$item.'`'; }, $items);
    }
}