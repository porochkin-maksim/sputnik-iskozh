<?php declare(strict_types=1);

use App\Http\Resources\Admin\Accounts\AccountResource;
use Core\Resources\Views\SectionNames;
use Core\Resources\Views\ViewNames;

/**
 * @var AccountResource $account
 */
?>

@extends(ViewNames::LAYOUTS_ADMIN)

@section(SectionNames::CONTENT)
    <account-item-view
            :model-value='@json($account)'
    />
@endsection