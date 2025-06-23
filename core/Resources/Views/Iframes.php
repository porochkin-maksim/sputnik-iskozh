<?php declare(strict_types=1);

namespace Core\Resources\Views;

abstract class Iframes
{
    public static function map(): string
    {
        return <<<HTML
<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A604bc906b7b7a8a4780c38eb81e9d38d066280cd260fe0139c348e4c74c4602a&amp;source=constructor" width="100%" height="570" frameborder="0"></iframe>
HTML;
    }

    public static function garbagePlace(): string
    {
        return <<<HTML
<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A677d287e328f92a58bee949b9048c2fe8c3116f3b61c6a7b5679a8fa0962b18a&amp;source=constructor" width="100%" height="570" frameborder="0"></iframe>
HTML;
    }
}
