<?php declare(strict_types=1);

namespace App\Services\Files\Services;

readonly class FolderBreadcrumbRenderer
{
    /**
     * @param array<int, array{title: string, url: string}> $breadcrumbs
     */
    public function render(array $breadcrumbs): string
    {
        $navHtmlList = '';

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $isActive    = $index === array_key_last($breadcrumbs) ? ' active' : '';
            $navHtmlList .= <<<HTML
                <li class="breadcrumb-item{$isActive}">
                    <a href="{$breadcrumb['url']}">{$breadcrumb['title']}</a>
                </li>
                HTML;
        }

        return $navHtmlList;
    }
}
