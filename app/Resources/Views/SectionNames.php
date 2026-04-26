<?php declare(strict_types=1);

namespace App\Resources\Views;

enum SectionNames: string
{
    public const string CONTENT = 'content';
    public const string TITLE   = 'title';
    public const string STYLES  = 'styles';
    public const string SCRIPTS = 'scripts';
    public const string METRICS = 'metrics';
    public const string META    = 'meta';
}
