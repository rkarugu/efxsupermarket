<?php

function deliveryAndLogisticsPermissionFunction()
{
    $deliveryAndLogisticsPermissions = [
        'deliveryandlogistics' => [
            'title' => [
                'Delivery And Logistics' => [
                    'model' => 'delivery_and_logistics',
                    'permissions' => [
                        'view' => 'view',
                    ],
                    'children' => [
                        'Fuel Management' => [
                            'model' => 'fuel-entries',
                            'permissions' => [
                                'view' => 'view',
                                'see-overview' => 'see-overview',
                            ],
                            'children' => [
                                'Fuel Statements' => [
                                    'model' => 'fuel-statements',
                                    'permissions' => [
                                        'view' => 'view',
                                        'upload' => 'upload',
                                    ],
                                ],
                                'Verification' => [
                                    'model' => 'fuel-verification',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Fuel Suppliers' => [
                                    'model' => 'fuel-suppliers',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Fuel Stations' => [
                                    'model' => 'fuel-stations',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                        'Small Packs' => [
                            'model' => 'small-packs',
                            'permissions' => [
                                'view' => 'view',
                                'store-loading-sheets' => 'store-loading-sheets',
                                'view-loading-sheets' => 'view-loading-sheets',
                                'process-dispatch' => 'process-dispatch',
                                'dispatched-loading-sheets' => 'dispatched-loading-sheets',
                                'dispatched-sheets-view' => 'dispatched-sheets-view'
                            ],
                        ],
                        'Device Management' => [
                            'model' => 'device-management',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Device Center' => [
                                    'model' => 'device-center',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'show' => 'show',
                                        'bulk-allocate' => 'bulk-allocate',
                                        'allocate' => 'allocate',
                                        'initiate-return' => 'initiate-return',
                                        'delete' => 'delete',
                                        'export' => 'export',
                                        'print' => 'print',
                                        'add-sim' => 'add-sim',
                                        'remove-sim' => 'remove-sim'
                                    ]
                                ],
                                'Device Type' => [
                                    'model' => 'device-type',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ]
                                ],

                                'Device Sim Card' => [
                                    'model' => 'device-sim-card',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ]
                                ],
                                'Device Repair' => [
                                    'model' => 'device-repair',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ]
                                ],
                            ]
                        ]
                    ],
                ],
            ],
        ],
    ];

    return $deliveryAndLogisticsPermissions;
}

function flattenDeliveryAndLogisticsPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenDeliveryAndLogisticsPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}