<?php declare(strict_types=1);

return [
    'api' => [
        'providers' => [
            'VTB' => [
                'address'  => env('API_VTB_ADDRESS'),
                'username' => env('API_VTB_USERNAME'),
                'password' => env('API_VTB_PASSWORD'),
                'token'    => env('API_VTB_TOKEN'),
            ],
        ],
    ],
];
