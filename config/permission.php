<?php

return [
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        'role_name' => 'name',
        'permission_name' => 'name',
        'model_type' => 'model_type',
        'model_id' => 'model_id',
        'model_morph_key' => 'model_id',
    ],

    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role' => Spatie\Permission\Models\Role::class,
    ],

    'register_permission' => true,
    'register_role' => true,
    'models_discover' => false,
];
