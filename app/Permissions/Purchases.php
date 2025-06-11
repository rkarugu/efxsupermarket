<?php

function purchasesPermissionFunction()
{
    $purchasesMainPermissions = [
        'purchases' => [
            'title' => [
                'Purchases' => [
                    'model' => 'purchases',
                    'permissions' => [
                        'view' => 'view'
                    ],
                    'children' => [
                        'Branch Purchase Requisitions' => [
                            'model' => 'purchase_requisitions',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Branch Requisitions' => [
                                    'model' => 'external-requisitions',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'view-all' => 'view-all'
                                    ]
                                ],
                                'Archived Branch Requisitions' => [
                                    'model' => 'external-requisitions',
                                    'permissions' => [
                                        'archived-requisition' => 'archived-requisition'
                                    ]
                                ],
                                'Approve Branch Requisitions' => [
                                    'model' => 'approve-external-requisitions',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Resolve Requisition to LPO' => [
                                    'model' => 'resolve-requisition-to-lpo',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add'
                                    ]
                                ],
                                'Suggested Orders' => [
                                    'model' => 'suggested-orders',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Status Report' => [
                                    'model' => 'external-requisitions',
                                    'permissions' => [
                                        'external-requisition-report' => 'external-requisition-report'
                                    ]
                                ],
                            ]
                        ],
                        'Purchase Orders' => [
                            'model' => 'purchase_orders_module',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'New Purchase Order' => [
                                    'model' => 'purchase-orders',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'view-all' => 'view-all',
                                        'hide' => 'hide',
                                        'send-to-supplier' => 'send-to-supplier',
                                        'print' => 'print',
                                        'export-pdf' => 'export-pdf'
                                    ]
                                ],
                                'Approve LPO' => [
                                    'model' => 'approve-lpo',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                "Archived LPOs" => [
                                    'model' => 'archived-lpo',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                "Completed LPOs" => [
                                    'model' => 'completed-lpo',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Status Report' => [
                                    'model' => 'purchase-order-status',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                            ]
                        ],
                        'Reports' => [
                            'model' => 'purchases-reports',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'LPO Status & Leadtime Report' => [
                                    'model' => 'purchases-reports',
                                    'permissions' => [
                                        'lpo-status-and-leadtime' => 'lpo-status-and-leadtime'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ]
            ]
        ]
    ];

    return $purchasesMainPermissions;
}


function flattenPurchasesPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenPurchasesPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}