<?php declare(strict_types=1);

use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use Core\Resources\RouteNames;

/**
 * @var TicketCategoryCollection $categories
 * @var null|TicketCategoryDTO   $category
 */

$category = $category ?? null;

$categories = $categories->sort(function (TicketCategoryDTO $a, TicketCategoryDTO $b) use ($category) {
    if ($category && $category->getId() === $a->getId()) {
        return -1; // a должна быть перед b
    }
    if ($category && $category->getId() === $b->getId()) {
        return 1; // b должна быть перед a, значит a после b
    }

    // Сортировка по возрастанию sort_order
    return $a->getSortOrder() <=> $b->getSortOrder();
})
?>

<div class="help-desk">
    @foreach($categories as $categoryItem)
        @if($categoryItem->getServices(true)->getActive()->isNotEmpty())
            <div class="">
                <h3 class="my-3">
                    @if(!$category || $category->getId() === $categoryItem->getId())
                        <a href="{{ route(RouteNames::HELP_DESK_CATEGORY, [$type->code(), $categoryItem->getCode()]) }}"
                           class="text-decoration-none text-success">
                            @if ($category)
                                <i class="fa fa-folder-open me-2"></i> {{ $categoryItem->getName() }}
                            @else
                                <i class="fa fa-folder me-2"></i> {{ $categoryItem->getName() }}
                            @endif
                        </a>
                    @else
                        <a href="{{ route(RouteNames::HELP_DESK_CATEGORY, [$type->code(), $categoryItem->getCode()]) }}"
                           class="text-decoration-none text-secondary">
                            <i class="fa fa-folder-o me-2"></i> {{ $categoryItem->getName() }}
                        </a>

                    @endif
                </h3>
                @if(!$category || $category->getId() === $categoryItem->getId())
                    <div>
                        @if($categoryItem->getServices()->getActive()->isNotEmpty())
                            <div class="row g-3">
                                @foreach($categoryItem->getServices()->getActive() as $service)
                                    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                                        <a href="{{ route(RouteNames::HELP_DESK_SERVICE, [$type->code(), $categoryItem->getCode(), $service->getCode()]) }}"
                                           class="text-decoration-none">
                                            <div class="service-item p-3 border rounded-3 bg-light h-100 transition-hover">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-cog text-secondary me-2 fa-fw"></i>
                                                    <span class="fw-semibold">{{ $service->getName() }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0 py-2">Нет доступных услуг</p>
                        @endif
                    </div>
                @endif
                @unless($loop->last)
                    <hr>
                @endunless
            </div>
        @endif
    @endforeach
</div>

