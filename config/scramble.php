<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    'api_path' => [
        'include' => [
            'api',
            'citas',
            'citas/*',
            'despachadores',
            'despachadores/*',
            'entregas',
            'entregas/*',
            'eps',
            'eps/*',
            'formulas-medicas',
            'formulas-medicas/*',
            'inventario',
            'inventario/*',
            'medicamentos',
            'medicamentos/*',
            'pacientes',
            'pacientes/*',
        ],
        'exclude' => [
            'docs',
            'docs/*',
        ],
    ],

    'api_domain' => null,
    'export_path' => 'api.json',

    'info' => [
        'version' => env('API_VERSION', '0.0.1'),
        'description' => 'Documentacion de endpoints API y rutas de modulos de gestion.',
    ],

    'ui' => [
        'title' => 'Formulas Medicas API',
    ],

    'renderer' => 'elements',

    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
    ],

    'security_strategy' => null,
];
