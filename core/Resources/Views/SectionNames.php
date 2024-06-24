<?php

namespace Core\Resources\Views;

enum SectionNames: string
{
    public const CONTENT = 'content';
    public const TITLE   = 'title';
    public const STYLES  = 'styles';
    public const SCRIPTS = 'scripts';
    public const METRICS = 'metrics';
    public const META    = 'meta';

    public const SUB  = 'sub';
    public const MAIN = 'main';
}
