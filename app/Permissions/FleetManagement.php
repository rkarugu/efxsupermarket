<?php

function fleetManagementPermissionFunction()
{
    $fleetManagementPermissions = [
        'fleetmanagement' => [
            'title' => [
                'Fleet Management' => [
                    'model' => 'fleet-management-module',
                    'permissions' => [
                        'view' => 'view',
                    ],
                    'children' => [
                        'Setup' => [
                            'model' => 'vehicle-suppliers',
                            'permissions' => ['view' => 'view'],
                            'children' => [
                                'Vehicle Suppliers' => [
                                    'model' => 'vehicle-suppliers',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                    ],
                                ],
                                'Vehicle Models' => [
                                    'model' => 'vehicle-models',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                    ],
                                ],
                            ],
                        ],
                        'My Fleet' => [
                            'model' => 'vehicle-suppliers',
                            'permissions' => ['view' => 'view'],
                            'children' => [
                                'Overview' => [
                                    'model' => 'vehicles-overview',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'listing' => 'listing',
                                    ],
                                ],
                                'Vehicles' => [
                                    'model' => 'vehicles-overview',
                                    'permissions' => [
                                        'overview' => 'overview',
                                        'view-history' => 'view-history',
                                        'switch-off' => 'switch-off',

                                    ],
                                ],
                                'Vehicle Control Centre' => [
                                    'model' => 'vehicle-command-center',
                                    'permissions' => [
                                        'view' => 'view',
                                        'exemption-schedules' => 'exemption-schedules',
                                        'add-vehicles-to-exemption-schedules' => 'add-vehicles-to-exemption-schedules',
                                        'custom-schedules' => 'custom-schedules',
                                    ],
                                ],
                                'Vehicle Custom Schedules' => [
                                    'model' => 'vehicle-command-center-custom-schedules',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                        // 'Fuel History' => [
                        //     'model' => 'fuel-history',
                        //     'permissions' => [
                        //         'fuelentry' => 'fuelentry',
                        //     ],
                        // ],
                        // 'Fuel Suppliers' => [
                        //     'model' => 'fuel-suppliers',
                        //     'permissions' => [
                        //         'view' => 'view',
                        //         'add' => 'add',
                        //         'edit' => 'edit',
                        //         'delete' => 'delete',
                        //     ],
                        // ],
                        // 'Fuel Stations' => [
                        //     'model' => 'fuel-stations',
                        //     'permissions' => [
                        //         'view' => 'view',
                        //         'add' => 'add',
                        //         'edit' => 'edit',
                        //         'delete' => 'delete',
                        //     ],
                        // ],
                        // 'Fuel LPOs' => [
                        //     'model' => 'fuel-lpos',
                        //     'permissions' => [
                        //         'view' => 'view',
                        //         'add' => 'add',
                        //         'archive' => 'archive'
                        //     ],
                        // ],
                        // 'Pending Fuel LPOs' => [
                        //     'model' => 'pending-fuel-lpos',
                        //     'permissions' => [
                        //         'view' => 'view',
                        //         'add' => 'add',
                        //         'approve' => 'approve',
                        //         'archive' => 'archive'
                        //     ],
                        // ],
                        // 'Confirmed Fuel LPOs' => [
                        //     'model' => 'confirmed-fuel-lpos',
                        //     'permissions' => [
                        //         'view' => 'view',
                        //         'add' => 'add',
                        //         'approve' => 'approve',
                        //         'archive' => 'archive'
                        //     ],
                        // ],
                        // 'Expired Fuel LPOs' => [
                        //     'model' => 'expired-fuel-lpos',
                        //     'permissions' => [
                        //         'view' => 'view',
                        //         'add' => 'add',
                        //         'reactivate' => 'reactivate',
                        //         'approve' => 'approve',
                        //         'archive' => 'archive'
                        //     ],
                        // ],
                    ],
                ],
            ],
        ],
    ];

    return $fleetManagementPermissions;
}

function flattenFleetManagementPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
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