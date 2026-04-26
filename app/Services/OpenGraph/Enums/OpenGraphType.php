<?php declare(strict_types=1);

namespace App\Services\OpenGraph\Enums;

/**
 * @see https://ogp.me/#types
 */
enum OpenGraphType: string
{
    case ARTICLE = 'article';
    case WEBSITE = 'website';
}
