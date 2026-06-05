<?php declare(strict_types=1);

/**
 * @var UidDTO $uid
 */

use Core\Domains\Infra\Uid\UidDTO;
use Core\Domains\Infra\Uid\UidTypeEnum;
use App\Resources\RouteNames;


$qrCode = QrCode::size(300)->generate(route(RouteNames::TOKEN, $uid->getToken()))?->toHtml();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>QR-код</title>
    <style>
        html, body {
            height : 100%;
            margin : 0;
        }

        body {
            display         : flex;
            justify-content : center;
            align-items     : center;
        }

        .center-box {
            text-align : center;
        }
    </style>
</head>
<body>
<div class="center-box">
    {!! $qrCode !!}
</div>
</body>
</html>