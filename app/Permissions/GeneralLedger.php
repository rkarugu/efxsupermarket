<?php

function generalLedgerPermissionFunction()
{
    $generalLedgerMainPermissions = [
        'generalledger' => [
            'title' => [
                'General Ledger' => [
                    'model' => 'genralledger',
                    'permissions' => [
                        'view' => 'view',
                    ],
                    'children' => [
                        'Maintain Wallets' => [
                            'model' => 'maintain-wallet',
                            'permissions' => [
                                'view' => 'view',
                            ],
                        ],
                        'Chart of Accounts' => [
                            'model' => 'chart-of-accounts',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete'
                            ],
                        ],
                        'Account Inquiry' => [
                            'model' => 'account-inquiry',
                            'permissions' => [
                                'view' => 'view',
                                'edit-account-transaction' => 'account-account-transaction',
                            ],
                        ],
                        'GL Journal Inquiry' => [
                            'model' => 'gl-journal-inquiry',
                            'permissions' => [
                                'view' => 'view',
                            ],
                        ],
                        'GL Transactions' => [
                            'model' => 'transfers',
                            'permissions' => [
                                'view' => 'view',
                            ],
                        ],
                        'GL Reconciliation' => [
                            'model' => 'gl_reconciliation',
                            'permissions' => [
                                'view' => 'view',
                                'overview' => 'overview',
                                'create' => 'create',
                                'edit' => 'edit',
                                'show' => 'show',
                                're-verify' => 're-verify',
                                'close-recon' => 'close-recon'
                            ],
                        ],
                        'Petty Cash' => [
                            'model' => 'petty-cash-requests',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Requests' => [
                                    'model' => 'petty-cash-requests-request',
                                    'permissions' => [
                                        'view' => 'view',
                                        'create' => 'create',
                                        'change-branch' => 'change-branch'
                                    ],
                                ],
                                'Initial Approval' => [
                                    'model' => 'petty-cash-requests-initial-approval',
                                    'permissions' => [
                                        'view' => 'view',
                                        'approve' => 'approve',
                                        'reject' => 'reject',
                                        'batch-approve' => 'batch-approve',
                                        'batch-reject' => 'batch-reject',
                                    ],
                                ],
                                'Final Approval' => [
                                    'model' => 'petty-cash-requests-final-approval',
                                    'permissions' => [
                                        'view' => 'view',
                                        'approve' => 'approve',
                                        'reject' => 'reject',
                                        'batch-approve' => 'batch-approve',
                                        'batch-reject' => 'batch-reject',
                                    ],
                                ],
                                'Processed Requests' => [
                                    'model' => 'petty-cash-requests-processed',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Failed Requests' => [
                                    'model' => 'petty-cash-requests-failed',
                                    'permissions' => [
                                        'view' => 'view',
                                        'resend' => 'resend',
                                    ],
                                ],
                                'Rejected Requests' => [
                                    'model' => 'petty-cash-requests-rejected',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Expunged Requests' => [
                                    'model' => 'petty-cash-requests-expunged',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                                'Logs' => [
                                    'model' => 'petty-cash-requests-logs',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                ],
                            ],
                        ],
                        'Expenses' => [
                            'model' => 'expenses',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'Bills' => [
                                    'model' => 'expenses',
                                    'permissions' => [
                                        'bill' => 'bill'
                                    ],
                                ],
                                'Expense' => [
                                    'model' => 'expenses',
                                    'permissions' => [
                                        'expense' => 'expense'
                                    ],
                                ],
                                'Cheque' => [
                                    'model' => 'expenses',
                                    'permissions' => [
                                        'cheque' => 'cheque'
                                    ],
                                ],
                            ],
                        ],
                        'Journal Entry' => [
                            'model' => 'journal-entries',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'edit' => 'edit',
                                'delete' => 'delete',
                            ],
                        ],
                        'Processed JV' => [
                            'model' => 'journal-entries',
                            'permissions' => [
                                'processed' => 'processed',
                            ],
                        ],
                        'View Ledger' => [
                            'model' => 'edit-ledger',
                            'permissions' => [
                                'view' => 'view',
                                'processed' => 'processed',
                            ],
                        ],
                        'Utility' => [
                            'model' => 'general-ledger-utility',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Assign Account View' => [
                                    'model' => 'assign-account-view',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit'
                                    ]
                                ],
                                'Transaction Without Branch' => [
                                    'model' => 'transaction-without-branch',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],

                                'Transaction Without Account' => [
                                    'model' => 'transaction-without-account',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],
                                'Update Customer to GL' => [
                                    'model' => 'update-customer-to-gl',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],
                            ]
                        ],
                        'Reports' => [
                            'model' => 'general-ledger-reports',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Trial Balance Report' => [
                                    'model' => 'trial-balances',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Detailed Trial Balance Report' => [
                                    'model' => 'tgeneral-ledger-reports',
                                    'permissions' => [
                                        'detailed-trial-balance' => 'detailed-trial-balance'
                                    ]
                                ],
                                'Trading Profit & Loss Report' => [
                                    'model' => 'trading-profit-and-loss',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Profit & Loss Report' => [
                                    'model' => '',
                                    'permissions' => []
                                ],
                                'Profit & Loss Details Report' => [
                                    'model' => '',
                                    'permissions' => []
                                ],
                                'Balance Sheet Report' => [
                                    'model' => '',
                                    'permissions' => []
                                ],
                                'Detailed Balance Sheet Report' => [
                                    'model' => '',
                                    'permissions' => []
                                ],
                                'Transaction Summary Report' => [
                                    'model' => 'general-ledger-reports',
                                    'permissions' => [
                                        'transaction-summary' => 'transaction-summary'
                                    ]
                                ],
                                'Detailed Transaction Summary Report' => [
                                    'model' => 'general-ledger-reports',
                                    'permissions' => [
                                        'detailed-transaction-summary' => 'detailed-transaction-summary'
                                    ]
                                ],
                                'Profit & Loss Monthly Report' => [
                                    'model' => 'general-ledger-reports',
                                    'permissions' => [
                                        'p-l-monthly-report' => 'p-l-monthly-report'
                                    ]
                                ],
                                'GL Account Update Report' => [
                                    'model' => 'general-ledger-reports',
                                    'permissions' => [
                                        'gl-account-update-report' => 'gl-account-update-report'
                                    ]
                                ],


                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    return $generalLedgerMainPermissions;
}

function flattenGeneralLedgerPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenGeneralLedgerPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}