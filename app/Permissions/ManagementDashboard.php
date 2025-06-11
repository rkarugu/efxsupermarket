<?php

function managementDashboardPermissionFunction()
{
    $managementDashboardMainPermissions = [
        'dashboard' => [
            'title' => [
                'Management Dashboard' => [
                    'model' => 'management-dashboard',
                    'permissions' => [
                        'view' => 'view'
                    ],
                    'children' => [
                        'Chairman\'s Dashboard' => [
                            'model' => 'chairmans-dashboard',
                            'permissions' => [
                                'view' => 'view'
                            ],
                        ],
                        'HQ Dashboard' => [
                            'model' => 'hq-dashboard',
                            'permissions' => [
                                'view' => 'view'
                            ],
                        ],
                        'Procurement Dashboard' => [
                            'model' => 'procurement-dashboard',
                            'permissions' => [
                                'view' => 'view'
                            ],
                        ],
                        'Petty Cash Dashboard' => [
                            'model' => 'petty-cash-dashboard',
                            'permissions' => [
                                'view' => 'view'
                            ],
                        ],
                        'Payments Dashboard' => [
                            'model' => 'payments-dashboard',
                            'permissions' => [
                                'view' => 'view'
                            ],
                        ],
                        'profitability Dashboard' => [
                            'model' => 'profitability-dashboard',
                            'permissions' => [
                                'view' => 'view'
                            ],
                        ],
                    ],
                ]
            ]
        ]
    ];

    return $managementDashboardMainPermissions;
}


function flattenManagementDashboardPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenManagementDashboardPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}
