<?php

function supplierPortalPermissionFunction()
{
    $supplierPortalsMainPermissions = [
        'supplierportal' => [
            'title' => [
                'Supplier Portal' => [
                    'model' => 'supplier-portal',
                    'permissions' => [
                        'view' => 'view',                        
                    ],
                    'children' => [
                        "Maintain Suppliers" => [
                            'model' => 'supplier-maintain-suppliers',
                            'permissions' => [
                                'view' => 'view',
                                'staff' => 'staff',
                                'suspend' => 'suspend',
                                'impersonate' => 'impersonate',
                            ]
                        ],
                        "Pending Invites" => [
                            'model' => 'pending-suppliers',
                            'permissions' => [
                                'view' => 'view',
                                'invite' => 'invite'
                            ]
                        ],
                        "LPO Delivery Slots" => [
                            'model' => 'order-delivery-slots',
                            'permissions' => [
                                'view' => 'view',
                                'show' => 'show',
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete'
                            ]
                        ],
                        "LPO Booked Slots" => [
                            'model' => 'order-delivery-slots',
                            'permissions' => [
                                'view' => 'view'
                            ]
                        ],
                        "Approve LPO Changes" => [
                            'model' => 'lpo-portal-req-approval',
                            'permissions' => [
                                'view' => 'view'
                            ]
                        ],
                        'Aprrove Price List Changes' => [
                            'model' => 'price-list-cost-change',
                            'permissions' => [
                                'view' => 'view'
                            ]
                        ],
                        "Supplier Portal Logs" => [
                            'model' => 'supplier-portal',
                            'permissions' => [
                                'logs' => 'logs'
                            ]
                        ],
                        "Suggested Order" => [
                            'model' => 'suggested-order',
                            'permissions' => [
                                'view' => 'view'
                            ]
                        ],
                        "API call Logs" => [
                            'model' => 'api-call-logs',
                            'permissions' => [
                                'view' => 'view'
                            ]
                        ],
                        "Email Templates" => [
                            'model' => 'email-templates',
                            'permissions' => [
                                'view' => 'view'
                            ]
                        ],
                        "Billing Description" => [
                            'model' => 'billing-description',
                            'permissions' => [
                                'view' => 'view'
                            ]
                        ],
                        "Request new SKU" => [
                            'model' => 'request-new-sku',
                            'permissions' => [
                                'view' => 'view'
                            ]
                        ],
                        "Approve Bank Desposits" => [
                            'model' => 'approve-bank-deposits',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                "Initial Approval" => [
                                    'model' => 'supplier-bank-deposits-initial-approval',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],
                                "Final Approval" => [
                                    'model' => 'supplier-bank-deposits-final-approval',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],
                            ]
                        ],
                        "Billing" => [
                            'model' => 'supplier-billing',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                "Initial Approval Billings" => [
                                    'model' => 'billing-submitted',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],
                                "Final Approval Billings" => [
                                    'model' => 'billing-submitted-final',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],
                                "Billing Settings" => [
                                    'model' => 'billing-settings',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],
                            ]
                        ],
                    ]
                ],
            ]
        ]
    ];

    return $supplierPortalsMainPermissions;
}


function flattenSupplierPortalPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenSupplierPortalPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}
