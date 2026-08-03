<?php

return [
    'modules' => [
        Vanilo\Foundation\Providers\ModuleServiceProvider::class,
        Konekt\AppShell\Providers\ModuleServiceProvider::class => [
            'ui' => [
                'name'     => 'Vanilo & Mercado Libre',
                'url'      => '/admin/mercadolibre',
                'logo_uri' => '/assets/logo.svg',
            ],
        ],
        Vanilo\Admin\Providers\ModuleServiceProvider::class,
    ],
    'register_route_models' => true
];
