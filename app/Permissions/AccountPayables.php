<?php

function accountPayablesPermissionFunction()
{
    $accountPayablesMainPermissions = [
        'accountpayables' => [
            'title' => [
                'Account Payables' => [
                    'model' => 'account-payables',
                    'permissions' => [
                        'view' => 'view'
                    ],
                    'children' => [
                        'Maintain Suppliers' => [
                            'model' => 'account-payables',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Dashboard' => [
                                    'model' => 'suppliers-overview',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Suppliers' => [
                                    'model' => 'maintain-suppliers',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Trade Agreements' => [
                                    'model' => 'trade-agreement',
                                    'permissions' => [
                                        'trade-agreement-view' => 'trade-agreement-view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                        'pdf' => 'pdf',
                                        'excel' => 'excel',
                                        'email' => 'email'
                                    ]
                                ],
                                'Trade Agreements (@Max)' => [
                                    'model' => 'trade-agreement',
                                    'permissions' => [
                                        'view' => 'view',
                                        'lock' => 'lock',
                                        'view-all' => 'view-all'
                                    ]
                                ],
                            ]
                        ],
                        'Pending GRNs' => [
                            'model' => 'pending-grns',
                            'permissions' => [
                                'view' => 'view',
                                'process' => 'process',
                            ]
                        ],
                        'Processed Invoices' => [
                            'model' => 'suppliers-invoice',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'reverse' => 'reverse'
                            ]
                        ],
                        'Advance Payments' => [
                            'model' => 'advance-payments',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'delete' => 'delete',
                            ]
                        ],
                        'Payment Vouchers' => [
                            'model' => 'payment-vouchers',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'confirm-details' => 'confirm-details',
                                'approve-confirmation' => 'approve-confirmation',
                                'approve-voucher' => 'approve-voucher',
                            ]
                        ],
                        'Generate Bank File' => [
                            'model' => 'bank-files',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit'
                            ]
                        ],
                        'Process Withholding Tax' => [
                            'model' => 'withholding-files',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete',
                            ]
                        ],
                        'Withholding Tax Payments' => [
                            'model' => 'withholding-tax-payments',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'approve' => 'approve',
                                'delete' => 'delete',
                            ]
                        ],
                        'Credit/Debit Notes' => [
                            'model' => 'credit-debit-notes',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete',
                            ]
                        ],
                        'Supplier Bills' => [
                            'model' => 'supplier-bills',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete',
                            ]
                        ],
                        'Approve Supplier' => [
                            'model' => 'maintain-suppliers',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'New Requests' => [
                                    'model' => 'maintain-suppliers',
                                    'permissions' => [
                                        'approve-new-supplier' => 'approve-new-supplier'
                                    ]
                                ],
                                'Edit Requests' => [
                                    'model' => 'maintain-suppliers',
                                    'permissions' => [
                                        'approve-edits-supplier' => 'approve-edits-supplier'
                                    ]
                                ]
                            ]
                        ],
                        'Approve Price Change' => [
                            'model' => 'maintain-suppliers',
                            'permissions' => [
                                'trade-agreement-change-request-list' => 'trade-agreement-change-request-list'
                            ],
                            'children' => [
                                'Pending Requests' => [
                                    'model' => 'maintain-suppliers',
                                    'permissions' => [
                                        'trade-agreement-change-request-list' => 'trade-agreement-change-request-list'
                                    ]
                                ]
                            ]
                        ],
                        'Price Demands' => [
                            'model' => 'item-demands',
                            'permissions' => [
                                'view' => 'view',
                                'edit-demand-quantity' => 'edit-demand-quantity',
                                'edit' => 'edit',
                                'approve' => 'approve',
                                'approve-edited' => 'approve-edited',
                                'approve-edited' => 'approve-edited',
                                'convert' => 'convert',
                            ]
                        ],
                        'Return Demands' => [
                            'model' => 'return-demands',
                            'permissions' => [
                                'view' => 'view',
                                'edit' => 'edit',
                                'approve' => 'approve',
                                'approve-edited' => 'approve-edited',
                                'convert' => 'convert',
                            ]
                        ],
                        'Trade Discounts' => [
                            'model' => 'trade-discounts',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete',
                                'approve' => 'approve',
                            ]
                        ],
                        'Trade Discount Demands' => [
                            'model' => 'trade-discounts-demands',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete',
                                'convert' => 'convert',
                            ]
                        ],
                        'Reports' => [
                            'model' => 'account-payables-reports',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Supplier Ageing Analysis Report' => [
                                    'model' => 'sales-and-receivables-reports',
                                    'permissions' => [
                                        'customer-aging-analysis' => 'customer-aging-analysis'
                                    ]
                                ],
                                'Vat Report' => [
                                    'model' => 'vat-report',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Supplier Listing Report' => [
                                    'model' => 'supplier-listing',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Supplier Bank Listing Report' => [
                                    'model' => 'supplier-bank-listing',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Supplier Statement Report' => [
                                    'model' => 'supplier-statement',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Supplier Ledger Report' => [
                                    'model' => 'supplier-ledger-report',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Payment Vouchers Report' => [
                                    'model' => 'payment-vouchers-report',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Bank Payments Report' => [
                                    'model' => 'bank-payments-report',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Withholding Tax Payments Report' => [
                                    'model' => 'withholding-tax-payments-report',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'GRNs Against Invoices Report' => [
                                    'model' => 'grns-against-invoices',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Trade Discounts Report' => [
                                    'model' => 'trade-discounts-report',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Trade Discount Demands Report' => [
                                    'model' => 'trade-discount-demands-report',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                            ],
                        ],
                        'Maintain Suppliers Data' => [
                            'model' => 'maintain-suppliers',
                            'permissions' => [
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete',
                                'supplier-portail-joining-email' => 'supplier-portail-joining-email',
                                'remittance-advice' => 'remittance-advice',
                                'enter-supplier-payment' => 'enter-supplier-payment',
                                'vendor-centre' => 'vendor-centre',
                                'can-view-all-suppliers' => 'can-view-all-suppliers'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ];

    return $accountPayablesMainPermissions;
}

function flattenAccountPayablesPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenAccountPayablesPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}
