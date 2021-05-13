<?php

namespace RebelCode\Wpra\Core\Query;

class DbQuery
{
    public $args;

    public function __construct($args = [])
    {
        $this->args = array_merge(self::defaults(), $args);
    }

    public static function create(array $args = [])
    {
        return new self($args);
    }

    public static function defaults()
    {
        return [
            'post_type' => 'any',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'suppress_filters' => true,
            'cache_results' => false,
            'no_found_rows' => true,
            'ignore_sticky_posts' => true,
        ];
    }
}
