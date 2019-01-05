<?php

return [
    'routing' => true,
    'admin_route_prefix'           => 'admin',
    'admin_route_middleware' => [
        'auth'
    ],
    'public_route_prefix'           => '',
    'public_route_middleware' => [
        'auth'
    ],
    'paginate'               => 50,
    'users_table'            => 'users',
    'users_primary_key'      => 'id',
];
