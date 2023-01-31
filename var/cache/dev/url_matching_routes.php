<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api' => [[['_route' => 'apiapp_d_s_l_r', '_controller' => 'App\\Controller\\DSLRController::index'], null, null, null, true, false, null]],
        '/api/shooting' => [[['_route' => 'apishooting', '_controller' => 'App\\Controller\\DSLRController::shooting'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/(?'
                    .'|download/([^/]++)(*:67)'
                    .'|photos/([^/]++)(*:89)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        67 => [[['_route' => 'apidownload_filename', '_controller' => 'App\\Controller\\DSLRController::download'], ['filename'], null, null, false, true, null]],
        89 => [
            [['_route' => 'apilink_photo', '_controller' => 'App\\Controller\\DSLRController::link_photo'], ['code'], null, null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
