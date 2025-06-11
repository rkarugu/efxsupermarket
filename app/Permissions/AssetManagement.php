<?php

function assetManagementPermissionFunction()
{
    $assetManagementPermissions = [
        'assetmanagement' => [
            'title' => [
                'Fixed Assets' => [
                    'model' => 'assets',
                    'permissions' => [
                        'view' => 'view',
                    ],
                    // 'children' => [
                    //     'Setup' => [
                    //         'model' => 'vehicle-suppliers',
                    //         'permissions' => ['view' => 'view'],
                    //         'children' => [
                    //             'Vehicle Suppliers' => [
                    //                 'model' => 'vehicle-suppliers',
                    //                 'permissions' => [
                    //                     'view' => 'view',
                    //                     'add' => 'add',
                    //                 ],
                    //             ],
                    //             'Vehicle Models' => [
                    //                 'model' => 'vehicle-models',
                    //                 'permissions' => [
                    //                     'view' => 'view',
                    //                     'add' => 'add',
                    //                 ],
                    //             ],
                    //         ],
                    //     ],
                    //     'My Fleet' => [
                    //         'model' => 'vehicle-suppliers',
                    //         'permissions' => ['view' => 'view'],
                    //         'children' => [
                    //             'Overview' => [
                    //                 'model' => 'vehicles-overview',
                    //                 'permissions' => [
                    //                     'view' => 'view',
                    //                     'add' => 'add',
                    //                 ],
                    //             ],
                    //             'Vehicles' => [
                    //                 'model' => 'vehicles-overview',
                    //                 'permissions' => [
                    //                     'overview' => 'overview',

                    //                 ],
                    //             ],
                    //         ],
                    //     ],
                    //     'Fuel History' => [
                    //         'model' => 'fuel-history',
                    //         'permissions' => [
                    //             'fuelentry' => 'fuelentry',
                    //         ],
                    //     ],
                    //     'Fuel Stations' => [
                    //         'model' => 'fuel-stations',
                    //         'permissions' => [
                    //             'view' => 'view',
                    //             'add' => 'add',
                    //             'edit' => 'edit',
                    //             'delete' => 'delete',
                    //         ],
                    //     ],
                    //     'Fuel LPOs' => [
                    //         'model' => 'fuel-lpos',
                    //         'permissions' => [
                    //             'view' => 'view',
                    //             'add' => 'add',
                    //             'archive' => 'archive'
                    //         ],
                    //     ],
                    // ],
                ],
            ],
        ],
    ];

    return $assetManagementPermissions;
}

function flattenAssetManagementPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenFleetManagementPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}