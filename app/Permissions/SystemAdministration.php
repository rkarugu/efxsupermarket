<?php

function systemAdministrationPermissionFunction()
{
    $systemMainPermissions = [
        'systemadministration' => [
            'title' => [
                'System Administration' => [
                    'model' => 'financial-management',
                    'permissions' => [
                        'view' => 'view',
                    ],
                    'children' => [
                        'General Ledger' => [
                            'model' => 'general-ledger',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Account Groups' => [
                                    'model' => 'account-groups',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Account Sections' => [
                                    'model' => 'account-sections',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Account Sub Sections' => [
                                    'model' => 'sub-account-sections',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit'
                                    ],
                                ],
                                'Dimensions' => [
                                    'model' => 'dimensions',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                    'children' => [
                                        'Branches' => [
                                            'model' => 'branches',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ],
                                        ],
                                        'Departments' => [
                                            'model' => 'departments',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ],
                                        ],
                                        'Projects' => [
                                            'model' => 'projects',
                                            'permissions' => [
                                                'view' => 'view',
                                            ],
                                        ],
                                        'GL Tags' => [
                                            'model' => 'gl-tags',
                                            'permissions' => [
                                                'view' => 'view',
                                            ],
                                        ],
                                    ],
                                ],
                                'Wallet Matrix' => [
                                    'model' => 'wallet-matrix',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Petty Cash' => [
                                    'model' => 'petty-cash',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'print' => 'print',
                                        'pdf' => 'pdf',
                                        're-print' => 're-print',
                                        'edit' => 'edit',
                                        'destroy' => 'destroy',
                                    ],
                                ],
                                'Petty Cash Request Types' => [
                                    'model' => 'petty-cash-request-types',
                                    'permissions' => [
                                        'view' => 'view',
                                        'create' => 'create',
                                        'edit' => 'edit',
                                        'assign-users' => 'assign-users',
                                        'assign-account' => 'assign-account',
                                    ],
                                ],
                            ],
                        ],
                        'System Setup' => [
                            'model' => 'finanacial-system-setup',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Company Preferences' => [
                                    'model' => 'company-preferences',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Tax Manager' => [
                                    'model' => 'tax-manager',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Currency Managers' => [
                                    'model' => 'currency-managers',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Accounting Periods' => [
                                    'model' => 'accounting-periods',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Number Series' => [
                                    'model' => 'number-series',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Roles' => [
                                    'model' => 'roles',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Employees' => [
                                    'model' => 'employees',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                        'change_password' => 'Change Password',
                                        'view_all_branches_data' => 'view_all_branches_data',
                                        // 'chairman_dashboard' => 'chairman_dashboard',
                                        // 'procurement_dashboard' => 'procurement_dashboard',
                                    ],
                                ],
                                'Loaders' => [
                                    'model' => 'loaders',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Teams' => [
                                    'model' => 'teams',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                    ],
                                ],
                                'User Access Requests' => [
                                    'model' => 'access-denied',
                                    'permissions' => [
                                        'edit' => 'edit',
                                    ],
                                ],
                                'User Logs' => [
                                    'model' => 'user-logs',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Activity Logs' => [
                                    'model' => 'activity-logs',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                // activity-logs
                                'Return Reasons' => [
                                    'model' => 'return-reasons',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Device Management' => [
                                    'model' => 'device-manager',
                                    'permissions' => [
                                        'reset' => 'reset',
                                    ],
                                ],
                                'Banks' => [
                                    'model' => 'cheque-banks',
                                    'permissions' => [
                                        'view' => 'view',
                                        'create' => 'create',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Help Desk Setup' => [
                                    'model' => 'help-desk-setup',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                    'children' => [
                                        'Support Team' => [
                                            'model' => 'support-team',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'edit' => 'edit',
                                                'delete' => 'delete'
                                            ],
                                        ],
                                        'Ticket Category' => [
                                            'model' => 'ticket-category',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'edit' => 'edit'
                                            ],
                                        ],
                                    ]
                                ],
                                'Incentive Settings' => [
                                    'model' => 'incentive-settings',
                                    'permissions' => [
                                        'view' => 'view',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                        'Inventory' => [
                            'model' => 'finanacial-inventory',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Location and Stores' => [
                                    'model' => 'location-and-stores',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Stock Type Categories' => [
                                    'model' => 'stock-type-categories',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Stock Family Groups' => [
                                    'model' => 'stock-family-groups',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Inventory Categories' => [
                                    'model' => 'inventory-categories',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Item Sub Categories' => [
                                    'model' => 'item-sub-categories',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Priority Level' => [
                                    'model' => 'priority-level',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Bin Location' => [
                                    'model' => 'unit-of-measures',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Category Price' => [
                                    'model' => 'category-price',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Pack Size' => [
                                    'model' => 'pack-size',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                        'Route Manager' => [
                            'model' => 'route-manager',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Route Mapping' => [
                                    'model' => 'manage-routes',
                                    'permissions' => [
                                        'view' => 'view',
                                        'view-all-routes' => 'view-all-routes'
                                    ],
                                ],
                                'Route Listing' => [
                                    'model' => 'manage-routes',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Route Targets Summary' => [
                                    'model' => 'manage-routes',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Route' => [
                                    'model' => 'routes',
                                    'permissions' => [
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'remove' => 'remove',
                                    ]
                                ]
                            ],
                        ],
                        'Receivables / Payables' => [
                            'model' => 'finanacial-receivables-payables',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Payment Terms' => [
                                    'model' => 'payment-terms',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Payment Providers' => [
                                    'model' => 'payment-providers',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Payment Methods' => [
                                    'model' => 'payment-methods',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                                'Payment Modes' => [
                                    'model' => 'payment-modes',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                        'Production' => [
                            'model' => 'financial-production',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Processes' => [
                                    'model' => 'processes',
                                    'permissions' => [
                                        'view' => 'view',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                    ],
                                ],
                            ],
                        ],
                        'Alerts and Notifications' => [
                            'model' => 'alerts-and-notifications',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Alerts' => [
                                    'model' => 'alerts',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    return $systemMainPermissions;
}

function flattenSystemPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenSystemPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}