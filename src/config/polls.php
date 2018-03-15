<?php

return [
    'route_prefix'           => '',
    'admin_route_middleware' => [
        'auth'
    ],
    'paginate'               => 50,
    'users_table'            => 'users',
    'users_primary_key'      => 'id',
];
