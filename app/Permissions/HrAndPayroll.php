<?php

function hrAndPayrollPermissionFunction()
{
    $hrAndPayrollMainPermissions = [
        'hrandpayroll' => [
            'title' => [
                'HR and Payroll' => [
                    'model' => 'hr-and-payroll',
                    'permissions' => [
                        'view' => 'view'
                    ],
                    'children' => [
                        'Configurations' => [
                            'model' => 'hr-and-payroll-configurations',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'General' => [
                                    'model' => 'hr-and-payroll-configurations-general',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                    'children' => [
                                        'Gender' => [
                                            'model' => 'hr-and-payroll-configurations-gender',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Salutation' => [
                                            'model' => 'hr-and-payroll-configurations-salutation',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Marital Status' => [
                                            'model' => 'hr-and-payroll-configurations-marital-status',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Education Level' => [
                                            'model' => 'hr-and-payroll-configurations-education-level',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Nationality' => [
                                            'model' => 'hr-and-payroll-configurations-nationality',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                                'bulk-upload' => 'bulk-upload',
                                            ]
                                        ],
                                        'Relationship' => [
                                            'model' => 'hr-and-payroll-configurations-relationship',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Document Type' => [
                                            'model' => 'hr-and-payroll-configurations-document-type',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Employment Type' => [
                                            'model' => 'hr-and-payroll-configurations-employment-type',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Employment Status' => [
                                            'model' => 'hr-and-payroll-configurations-employment-status',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Job Group' => [
                                            'model' => 'hr-and-payroll-configurations-job-group',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                                'bulk-upload' => 'bulk-upload',
                                            ]
                                        ],
                                        'Job Level' => [
                                            'model' => 'hr-and-payroll-configurations-job-level',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                                'bulk-upload' => 'bulk-upload',
                                            ]
                                        ],
                                        'Job Grade' => [
                                            'model' => 'hr-and-payroll-configurations-job-grade',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                                'bulk-upload' => 'bulk-upload',
                                            ]
                                        ],
                                        'Job Title' => [
                                            'model' => 'hr-and-payroll-configurations-job-title',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                                'bulk-upload' => 'bulk-upload',
                                            ]
                                        ],
                                        'Dsicipline Category' => [
                                            'model' => 'hr-and-payroll-configurations-discipline-category',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Dsicipline Action' => [
                                            'model' => 'hr-and-payroll-configurations-discipline-action',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Termination Type' => [
                                            'model' => 'hr-and-payroll-configurations-termination-type',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                    ]
                                ],
                                'Payroll' => [
                                    'model' => 'hr-and-payroll-configurations-payroll',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                    'children' => [
                                        'Earnings' => [
                                            'model' => 'hr-and-payroll-configurations-earning',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Deductions' => [
                                            'model' => 'hr-and-payroll-configurations-deduction',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'PAYE' => [
                                            'model' => 'hr-and-payroll-configurations-paye',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'NSSF' => [
                                            'model' => 'hr-and-payroll-configurations-nssf',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'SHIF' => [
                                            'model' => 'hr-and-payroll-configurations-shif',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Housing Levy' => [
                                            'model' => 'hr-and-payroll-configurations-housing-levy',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Relief' => [
                                            'model' => 'hr-and-payroll-configurations-relief',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ]
                                        ],
                                        'Setting' => [
                                            'model' => 'hr-and-payroll-configurations-setting',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                            ]
                                        ],
                                    ]
                                ],
                                'Banking' => [
                                    'model' => 'hr-and-payroll-configurations-banking',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                    'children' => [
                                        'Bank' => [
                                            'model' => 'hr-and-payroll-configurations-bank',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                                'bulk-upload' => 'bulk-upload',
                                            ],
                                        ],
                                        'Bank Branch' => [
                                            'model' => 'hr-and-payroll-configurations-bank-branch',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ],
                                        ],
                                        'Payment Modes' => [
                                            'model' => 'hr-and-payroll-configurations-payment-modes',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'edit' => 'edit',
                                                'delete' => 'delete',
                                            ],
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'HR Management' => [
                            'model' => 'hr-and-payroll-hr-management',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Drafts' => [
                                    'model' => 'hr-management-employee-drafts',
                                    'permissions' => [
                                        'view' => 'view',
                                    ]
                                ],
                                'Employees' => [
                                    'model' => 'hr-management-employees',
                                    'permissions' => [
                                        'view' => 'view',
                                        'create' => 'create',
                                        'edit' => 'edit',
                                        'details' => 'details',
                                        'bulk-upload' => 'bulk-upload',
                                    ]
                                    ],
                                'Casuals' => [
                                    'model' => 'hr-management-casuals',
                                    'permissions' => [
                                        'view' => 'view',
                                        'create' => 'create',
                                        'edit' => 'edit',
                                        'bulk-upload' => 'bulk-upload',
                                    ]
                                ]
                            ]
                                    ],
                        'Payroll' => [
                            'model' => 'hr-and-payroll-payroll',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Payroll Months' => [
                                    'model' => 'payroll-payroll-months',
                                    'permissions' => [
                                        'view' => 'view',
                                        'create' => 'create',
                                        'details' => 'details',
                                        'close' => 'close',
                                    ],
                                    'children' => [
                                        'Payroll Month Details' => [
                                            'model' => 'payroll-payroll-month-details',
                                            'permissions' => [
                                                'view' => 'view',
                                                'edit' => 'edit',
                                                'view-payslip' => 'view-payslip',
                                                'upload-earnings-and-deductions' => 'upload-earnings-and-deductions',
                                            ]
                                        ]
                                    ]
                                ],
                                'Casuals Pay' => [
                                    'model' => 'casuals-pay',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],
                                    'children' => [
                                        'Pay Periods' => [
                                            'model' => 'casuals-pay-pay-periods',
                                            'permissions' => [
                                                'view' => 'view',
                                                'create' => 'create',
                                                'details' => 'details',
                                                'initial-approval' => 'initial-approval',
                                                'final-approval' => 'final-approval',
                                                'upload-register' => 'upload-register',
                                                'print' => 'print',
                                            ]
                                        ],
                                        'Successful Disbursements' => [
                                            'model' => 'casuals-pay-successful-disbursements',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'Failed Disbursements' => [
                                            'model' => 'casuals-pay-failed-disbursements',
                                            'permissions' => [
                                                'view' => 'view',
                                                'recheck-and-resend' => 'recheck-and-resend',
                                                'expunge' => 'expunge',
                                            ]
                                        ],
                                        'Expunged Disbursements' => [
                                            'model' => 'casuals-pay-expunged-disbursements',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                    ]
                                ],
                                'Reports' => [
                                    'model' => 'payroll-reports',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                    'children' => [
                                        'Paymaster Report' => [
                                            'model' => 'payroll-reports-paymaster',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'Payroll Summary Report' => [
                                            'model' => 'payroll-reports-payroll-summary',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'Earnings Report' => [
                                            'model' => 'payroll-reports-earnings',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'Deductions Report' => [
                                            'model' => 'payroll-reports-deductions',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'Consolidated Payroll Report' => [
                                            'model' => 'payroll-reports-consolidated-payroll',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'PAYE Deductions Report' => [
                                            'model' => 'payroll-reports-paye-deductions',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'NSSF Deductions Report' => [
                                            'model' => 'payroll-reports-nssf-deductions',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'SHIF Deductions Report' => [
                                            'model' => 'payroll-reports-shif-deductions',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'Housing Levy Deductions Report' => [
                                            'model' => 'payroll-reports-housing-levy-deductions',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                        'Other Deductions Report' => [
                                            'model' => 'payroll-reports-other-deductions',
                                            'permissions' => [
                                                'view' => 'view',
                                            ]
                                        ],
                                    ]
                                ],
                            ]
                        ]
                    ],
                ]
            ]
        ]
    ];

    return $hrAndPayrollMainPermissions;
}


function flattenHRAndPayrollPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenHRAndPayrollPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}
