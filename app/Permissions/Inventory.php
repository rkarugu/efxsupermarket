<?php

function inventoryPermissionFunction()
{
    $inventoryMainPermissions = [
        'inventory' => [
            'title' => [
                'Inventory' => [
                    'model' => 'inventory',
                    'permissions' => [
                        'view' => 'view'
                    ],
                    'children' => [
                        'Branches' => [
                            'model' => 'maintain-items',
                            'permissions' => [
                                'view-per-branch' => 'view-per-branch'
                            ]
                        ],
                        'Maintain Item' => [
                            'model' => 'maintain-item',
                            'permissions' => [
                                'view' => 'view'
                            ],
                        ],
                        'Maintain Item' => [
                            'model' => 'maintain-item',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Manage Items' => [
                                    'model' => 'maintain-items',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        // 'delete' => 'delete',
                                        'manage-item-stock' => 'manage-item-stock',
                                        'manage-category-pricing' => 'manage-category-pricing',
                                        'item_price_pending_list' => 'item_price_pending_list',
                                        'item_price_history' => 'item_price_history',
                                        'negetive_stock_report' => 'negetive_stock_report',
                                        'inventory-location-stock-report' => 'inventory-location-stock-report',
                                        'inventory-location-as-at' => 'inventory-location-as-at',
                                        'price-update-upload' => 'price-update-upload',
                                        'approve-discount' => 'approve-discount',
                                        'manage-standard-cost' => 'manage-standard-cost',
                                        'manage-standard-cost-manual' => 'manage-standard-cost-manual',
                                        'edit-max-stock' => 'edit-max-stock',
                                        'edit-reorder-level' => 'edit-reorder-level',
                                        'edit-bin-location' => 'edit-bin-location',
                                        'price_list_cost' => 'price_list_cost',
                                        'standard_cost' => 'standard_cost',
                                        'last_grn_cost' => 'last_grn_cost',
                                        'weighted_average_cost' => 'weighted_average_cost',
                                        'clone' => 'clone',

                                    ],
                                    'children' => [
                                        'Tabs' => [
                                            'model' => 'maintain-items',
                                            'permissions' => [
                                                'view' => 'view',
                                            ],
                                            'children' => [
                                                'Stock Movements' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'view-stock-movements' => 'view-stock-movements'
                                                    ]
                                                ],
                                                'Stock Status' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'view-stock-status' => 'view-stock-status',
                                                        'view-all-stocks' => 'view-all-stocks'
                                                    ]
                                                ],
                                                'Purchase Data' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'maintain-purchasing-data' => 'maintain-purchasing-data'
                                                    ]
                                                ],
                                                'Small Packs' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'assign-inventory-items' => 'assign-inventory-items'
                                                    ]
                                                ],
                                                'Price Change History' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'price-change-history' => 'price-change-history'
                                                    ]
                                                ],
                                                'Bin Location' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'update-bin-location' => 'update-bin-location'
                                                    ]
                                                ],
                                                'Discounts' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'manage-discount' => 'manage-discount'
                                                    ]
                                                ],
                                                'Promotions' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'manage-promotions' => 'manage-promotions'
                                                    ]
                                                ],
                                                'Route Pricing' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'route-pricing' => 'route-pricing'
                                                    ]
                                                ],
                                                'Shop Pricing' => [
                                                    'model' => 'maintain-items',
                                                    'permissions' => [
                                                        'view-shop-pricing' => 'view-shop-pricing'
                                                    ]
                                                ],
                                            ]
                                        ]
                                    ]
                                ],
                                'Inventory Adjustment' => [
                                    'model' => 'inventory-item-adjustment',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add'
                                    ]
                                ],
                                'Price Change' => [
                                    'model' => 'price-change',
                                    'permissions' => [
                                        'manage-standard-cost' => 'manage-standard-cost'
                                    ],
                                    'children' => [
                                        'Price Change' => [
                                            'model' => 'price-change',
                                            'permissions' => [
                                                'manage-standard-cost' => 'manage-standard-cost'
                                            ]
                                        ],
                                        'Price Change History' => [
                                            'model' => 'price-change',
                                            'permissions' => [
                                                'manage-standard-cost' => 'manage-standard-cost'
                                            ]
                                        ],
                                        'Manual Cost Change' => [
                                            'model' => 'maintain-items',
                                            'permissions' => [
                                                'manage-standard-cost-manual' => 'manage-standard-cost-manual'
                                            ]
                                        ]
                                    ]
                                ],
                                'Stocks Break' => [
                                    'model' => 'stock-breaking',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],
                                    'children' => [
                                        'Stock Breaking' => [
                                            'model' => 'stock-breaking',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'edit' => 'edit',
                                                'summary' => 'summary',
                                            ]
                                        ],
                                        'Auto Breaks' => [
                                            'model' => 'stock-auto-breaks',
                                            'permissions' => [
                                                'view' => 'view'
                                            ]
                                        ],
                                        'Display Split Requests' => [
                                            'model' => 'display-split-requests',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'edit' => 'edit',
                                                'approve' => 'approve',
                                            ]
                                        ],
                                        'Reverse Splits' => [
                                            'model' => 'reverse-splits',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'edit' => 'edit',
                                                'approve' => 'approve',
                                                'reject' => 'reject',
                                            ]
                                        ],
                                        'Pending Dispatches' => [
                                            'model' => 'stock-auto-breaks',
                                            'permissions' => [
                                                'dispatch' => 'dispatch'
                                            ]
                                        ],
                                        'Dispatched Breaks' => [
                                            'model' => 'stock-auto-breaks',
                                            'permissions' => [
                                                'dispatch' => 'dispatch'
                                            ]
                                        ],
                                        'Completed Breaks' => [
                                            'model' => 'stock-auto-breaks',
                                            'permissions' => [
                                                'dispatch' => 'dispatch'
                                            ]
                                        ]
                                    ]
                                ],
                                'Weighted Averages' => [
                                    'model' => 'weighted-average-history',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Item Approval' => [
                                    'model' => 'maintain-items',
                                    'permissions' => [
                                        'item-approval' => 'item-approval'
                                    ],
                                    'children' => [
                                        'Pending New Approval' => [
                                            'model' => 'maintain-items',
                                            'permissions' => [
                                                'item-approval' => 'item-approval'
                                            ]
                                        ],
                                        'Pending Edit Approval' => [
                                            'model' => 'maintain-items',
                                            'permissions' => [
                                                'item-approval' => 'item-approval'
                                            ]
                                        ],
                                        'Item History' => [
                                            'model' => 'maintain-items',
                                            'permissions' => [
                                                'item-approval' => 'item-approval'
                                            ]
                                        ],
                                        'Rejected Request' => [
                                            'model' => 'maintain-items',
                                            'permissions' => [
                                                'item-approval' => 'item-approval'
                                            ]
                                        ],
                                    ]
                                ],
                            ]
                        ],
                        'Purchase Orders' => [
                            'model' => 'inventory-purchase-orders',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Receive Purchases' => [
                                    'model' => 'inventory-purchase-orders',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],
                                    'children' => [
                                        'Initiate GRN' => [
                                            'model' => 'receive-purchase-order',
                                            'permissions' => [
                                                'view' => 'view',
                                                'edit-price' => 'edit-price'
                                            ],
                                            'children' => [
                                                'Pending Return Received Order' => [
                                                    'model' => 'pending-returns-receive-purchase-order',
                                                    'permissions' => [
                                                        'view' => 'view'
                                                    ]
                                                ],
                                                'Return Accepted Received Order' => [
                                                    'model' => 'return-accepted-receive-order',
                                                    'permissions' => [
                                                        'view' => 'view'
                                                    ]
                                                ],
                                            ],
                                        ],
                                        'Approve GRN' => [
                                            'model' => 'process-receive-purchase-order',
                                            'permissions' => [
                                                'view' => 'view'
                                            ]
                                        ],
                                        'Confirm GRN' => [
                                            'model' => 'confirmed-receive-purchase-order',
                                            'permissions' => [
                                                'view' => 'view',
                                                'confirm' => 'confirm',
                                                'delete' => 'delete'
                                            ]
                                        ]
                                    ]
                                ],
                                'Completed GRN' => [
                                    'model' => 'completed-grn',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],
                                ],
                            ]
                        ],
                        'Delivery Notes' => [
                            'model' => 'delivery-notes',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                            ],
                            'children' => [
                                'Match Purchase Orders' => [
                                    'model' => 'match-purchase-orders',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add'
                                    ]
                                ],
                                'Delivery Notes Invoices' => [
                                    'model' => 'delivery-notes-invoices',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add'
                                    ],
                                ],
                                'Delivery Notes Schedules' => [
                                    'model' => 'delivery-notes-schedules',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                            ],
                        ],
                        'Goods Returns' => [
                            'model' => 'goods-returns',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Return From GRN' => [
                                    'model' => 'return-to-supplier-from-grn',
                                    'permissions' => [
                                        'view' => 'view',
                                        'create' => 'create',
                                        'change-bin' => 'change-bin',
                                        'view-pending' => 'view-pending',
                                        'approve' => 'approve',
                                        'view-approved' => 'view-approved',
                                        'print' => 'print',
                                    ]
                                ],
                                'Return From Store' => [
                                    'model' => 'return-to-supplier-from-store',
                                    'permissions' => [
                                        'view' => 'view',
                                        'create' => 'create',
                                        'change-branch' => 'change-branch',
                                        'change-store-location' => 'change-store-location',
                                        'change-bin' => 'change-bin',
                                        'view-all-pending' => 'view-all-pending',
                                        'view-pending' => 'view-pending',
                                        'view-pending-details' => 'view-pending-details',
                                        'approve' => 'approve',
                                        'view-all-approved' => 'view-all-approved',
                                        'view-approved' => 'view-approved',
                                        'print' => 'print',
                                        'view-rejected' => 'view-rejected',
                                    ]
                                ],
                                'Processed Returns' => [
                                    'model' => 'return-to-supplier-processed',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],
                                ],
                                'Pending Portal Request' => [
                                    'model' => 'pending-returns-receive-purchase-order',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],
                                ],
                                'Returned Credit Notes' => [
                                    'model' => 'return-accepted-receive-order',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],
                                ],
                            ]
                        ],
                        'Inter-branch Transfers' => [
                            'model' => 'transfers',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Initiate Transfer' => [
                                    'model' => 'transfers',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        // 'delete' => 'delete',
                                        'return-list' => 'return-list',
                                        'resign-esd' => 'resign-esd'
                                    ],

                                ],
                                'Receive Transfer' => [
                                    'model' => 'transfers',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],

                                ],
                                'Processed Transfers' => [
                                    'model' => 'transfers',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],

                                ],
                            ]
                        ],
                        'Internal Requisition' => [
                            'model' => 'internal-requisitions',
                            'permissions' => [
                                'view' => 'view',
                            ],
                            'children' => [
                                'New Stock Request' => [
                                    'model' => 'internal-requisitions',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add'
                                    ],

                                ],
                                'Authorise Requisition' => [
                                    'model' => 'n-authorise-requisitions',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],

                                ],
                                'Issue/Fullfill Requisition' => [
                                    'model' => 'issue-fullfill-requisition',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],

                                ],
                                'Processed Requisition' => [
                                    'model' => 'processed-requisition',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],

                                ],
                            ]
                        ],
                        'Stock Take' => [
                            'model' => 'stock-take',
                            'permissions' => [
                                'view' => 'view',
                                'add' => 'add',
                                'freeze' => 'freeze'
                            ],
                            'children' => [
                                'Create Stock Take Sheet' => [
                                    'model' => 'stock-take',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],

                                ],
                                'Stock Count Users' => [
                                    'model' => 'stock-take-user-assignment',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'delete' => 'delete',
                                        'upload' => 'upload',
                                        'batch-upload' => 'batch-upload',
                                        'transfer' => 'transfer',

                                    ],

                                ],
                                'Enter Stock Counts' => [
                                    'model' => 'stock-counts',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        // 'delete' => 'delete'
                                    ],

                                ],
                                'Compare Counts Vs Stock Check Data' => [
                                    'model' => 'compare-counts-vs-stock-check',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],

                                ],
                                'Stock Count Variance' => [
                                    'model' => 'stock-count-variance',
                                    'permissions' => [
                                        'view' => 'view',
                                        'detailed-view' => 'detailed-view',
                                        'summary-view' => 'summary-view'
                                    ],
                                ],
                                'Stock Debtors' => [
                                    'model' => 'stock-debtors',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        'view-details' => 'view-details',
                                        'print' => 'print',
                                        'split' => 'split',
                                        'split-non-debtor' => 'split-non-debtor',
                                    ],
                                ],
                                'Non Stock Debtors' => [
                                    'model' => 'stock-non-debtors',
                                    'permissions' => [
                                        'view' => 'view',
                                        'view-details' => 'view-details',
                                        'print' => 'print',
                                    ],
                                ],
                                
                                'Stock Processing' => [
                                    'model' => 'stock-processing',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],
                                    'children' => [
                                        'Stock Processing Sales' => [
                                            'model' => 'stock-processing-sales',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'show' => 'show',
                                                'edit' => 'edit',
                                                'print' => 'print',
                                                'resign_esd' => 'resign_esd'
                                            ],
                                        ],
                                        'Stock Processing Return' => [
                                            'model' => 'stock-processing-return',
                                            'permissions' => [
                                                'view' => 'view',
                                                'add' => 'add',
                                                'show' => 'show',
                                                'edit' => 'edit',
                                                'print' => 'print',
                                                'resign_esd' => 'resign_esd'
                                            ],
                                        ],
                                        'Stock Uncompleted Sales' => [
                                            'model' => 'stock-uncompleted-sales',
                                            'permissions' => [
                                                'view' => 'view',
                                                'show' => 'show',
                                                'print' => 'print',
                                                'process' => 'process'
                                            ],
                                        ],
                                    ],
                                ],
                                'Blocked Users' => [
                                    'model' => 'stock-count-blocked-users',
                                    'permissions' => [
                                        'view' => 'view',

                                    ],
                                ],
                                'Exemption Schedules' => [
                                    'model' => 'stock-count-blocked-users-exemption-schedules',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        // 'delete' => 'delete'

                                    ],
                                ],
                            ]
                        ],
                        'Utility' => [
                            'model' => 'utility',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Update Max Stock / Reorder Level' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'view' => 'view',
                                        'update' => 'update'
                                    ],

                                ],
                                'Retired Items' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'view' => 'view'
                                    ],

                                ],
                                'Recalculate QOH' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'recalculate_qoh' => 'recalculate_qoh'
                                    ],

                                ],
                                'Supplier User Management' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'supplier-user-management' => 'supplier-user-management'
                                    ],

                                ],
                                'Update Bin' => [
                                    'model' => 'bin-utility',
                                    'permissions' => [
                                        'view' => 'view',
                                        'update-bin' => 'update-bin',
                                        'approve-bin-allocation' => 'approve-bin-allocation',
                                    ],

                                ],
                                'Update Selling Price' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'item-selling-price' => 'item-selling-price',
                                        'download-item-selling-price' => 'download-item-selling-price',
                                        'update-item-selling-price' => 'update-item-selling-price',
                                    ],

                                ],
                                'Update Standard Cost' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'item-standard-cost' => 'item-standard-cost',
                                        'download-standard-cost' => 'download-standard-cost',
                                        'update-standard-cost' => 'update-standard-cost',
                                    ],

                                ],
                                'Update Item Margins' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'item-margins' => 'item-margins',
                                        'download-item-margins' => 'download-item-margins',
                                        'update-item-margins' => 'update-item-margins'
                                    ],

                                ],
                                'Update Branch Selling Price & Standard Cost' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'item-selling-price-and-standard-cost' => 'item-selling-price-and-standard-cost',
                                        'download-selling-price-and-standard-cost' => 'download-selling-price-and-standard-cost',
                                        'update-selling-price-and-standard-cost' => 'update-selling-price-and-standard-cost',
                                    ],

                                ],
                                'Download Branch Utilities' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'branch-utilities' => 'branch-utilities',
                                        'download-inventory-category' => 'download-inventory-category',
                                        'download-inventory-sub-category' => 'download-inventory-sub-category',
                                        'download-suppliers' => 'download-suppliers',
                                        'download-tax-category' => 'download-tax-category',
                                        'download-pack-size' => 'download-pack-size',
                                        'download-bin-locations' => 'download-bin-locations',
                                    ],

                                ],
                                'Promotion Type' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'promotion-type-create' => 'promotion-type-create',
                                        'promotion-type-view' => 'promotion-type-view',
                                        'promotion-type-update' => 'promotion-type-update',
                                    ],

                                ],
                                'Promotion Group' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'promotion-group-create' => 'promotion-group-create',
                                        'promotion-group-view' => 'promotion-group-view',
                                        'promotion-group-update' => 'promotion-group-update',
                                    ],

                                ],
                                'Active Promotions' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'active-promotions-view' => 'active-promotions-view',
                                        'active-promotions-create' => 'active-promotions-create',
                                        'active-promotions-update' => 'active-promotions-update',
                                    ],

                                ],
                                'Hampers' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'hampers-view' => 'hampers-view',
                                        'hampers-create' => 'hampers-create',
                                        'hampers-update' => 'hampers-update',
                                    ],

                                ],
                                'Upload New Items' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'upload-new-items' => 'upload-new-items'
                                    ],

                                ],
                                'Verify Stocks' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'verify-stocks' => 'verify-stocks'
                                    ],

                                ],
                                'Download Stocks' => [
                                    'model' => 'utility',
                                    'permissions' => [
                                        'download-stocks' => 'download-stocks'
                                    ],

                                ],
                                'Update Item Stock Code' => [
                                    'model' => 'update-item-code',
                                    'permissions' => [
                                        'view' => 'view',
                                        'update' => 'update',
                                    ],

                                ],
                                'Items Without Suppliers' => [
                                    'model' => 'items-without-suppliers',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],

                                ],
                                'Competing Brands' => [
                                    'model' => 'competing-brands',
                                    'permissions' => [
                                        'view' => 'view',
                                        'add' => 'add',
                                        'edit' => 'edit',
                                        // 'delete' => 'delete'
                                    ],

                                ],
                                'Update Stock QOH' => [
                                    'model' => 'update-stock-qoh',
                                    'permissions' => [
                                        'view' => 'view',
                                        'download' => 'download',
                                        'update' => 'update'
                                    ],

                                ],
                                'GRN Update' => [
                                    'model' => 'update-grn-utility',
                                    'permissions' => [
                                        'view' => 'view',
                                        'change-qty' => 'change-qty',
                                        'change-price' => 'change-price'
                                    ],

                                ],
                                'Update Item Count' => [
                                    'model' => 'item-has-count',
                                    'permissions' => [
                                        'view' => 'view',
                                    ],

                                ],
                            ]
                        ],
                        'Utility Logs' => [
                            'model' => 'inventory-utility-logs',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Logs' => [
                                    'model' => 'inventory-utility-logs',
                                    'permissions' => [
                                        'view-inventory-utility-logs' => 'view-inventory-utility-logs'
                                    ]
                                ],
                            ],
                        ],

                        'Reports' => [
                            'model' => 'inventory-reports',
                            'permissions' => [
                                'view' => 'view'
                            ],
                            'children' => [
                                'Valuation Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'inventory-valuation-report' => 'inventory-valuation-report'
                                    ]
                                ],
                                'Suggested Order Report' => [
                                    'model' => 'maintain-items',
                                    'permissions' => [
                                        'suggested_order_report' => 'suggested_order_report'
                                    ]
                                ],
                                'Inventory -Ve Stock Report' => [
                                    'model' => 'maintain-items',
                                    'permissions' => [
                                        'negetive_stock_report' => 'negetive_stock_report'
                                    ]
                                ],
                                'Average Sales Vs Max Stock Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'average-sales-report' => 'average-sales-report'
                                    ]
                                ],
                                'Max Stock Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'max-stock-report' => 'max-stock-report'
                                    ]
                                ],
                                'Reorder Items Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'reorder-items-report' => 'reorder-items-report'
                                    ]
                                ],
                                'Missing Items Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'missing-items-report' => 'missing-items-report'
                                    ]
                                ],
                                'Discount Items Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'discount-items' => 'discount-items'
                                    ]
                                ],
                                'Promotion Items Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'promotion-items' => 'promotion-items'
                                    ]
                                ],
                                'Overstock Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'overstock-report' => 'overstock-report'
                                    ]
                                ],
                                'Inactive Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'inactive-stock-report' => 'inactive-stock-report'
                                    ]
                                ],
                                'Dead Stock Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'dead-stock-report' => 'dead-stock-report'
                                    ]
                                ],
                                'Slow Moving Items Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'slow-moving-items-report' => 'slow-moving-items-report'
                                    ]
                                ],
                                'Child Vs Mother Qoh Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'child-vs-mother-qoh' => 'child-vs-mother-qoh'
                                    ]
                                ],
                                'Missing Split Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'missing-split-report' => 'missing-split-report'
                                    ]
                                ],
                                'CTN without Children Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'ctn-without-children' => 'ctn-without-children'
                                    ]
                                ],
                                'Price Timeline Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'price-timeline-report' => 'price-timeline-report'
                                    ]
                                ],
                                'Items List Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'items-list-report' => 'items-list-report'
                                    ]
                                ],
                                'Reported Issues Report' => [
                                    'model' => 'reported-shift-issues',
                                    'permissions' => [
                                        'view' => 'view'
                                    ]
                                ],
                                'Location Stock Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'inventory-location-stock-report' => 'inventory-location-stock-report'
                                    ]
                                ],
                                'Supplier Product Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'supplier-product-reports' => 'supplier-product-reports'
                                    ]
                                ],
                                'GRN Summary by Supplier Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'grn-summary-by-supplier-report' => 'grn-summary-by-supplier-report'
                                    ]
                                ],
                                'No Supplier Items Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'no-supplier-items-report' => 'no-supplier-items-report'
                                    ]
                                ],
                                'Supplier User Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'supplier-user-report' => 'supplier-user-report'
                                    ]
                                ],
                                'Sub Distributor Suppliers Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'sub-distributor-suppliers-report' => 'sub-distributor-suppliers-report'
                                    ]
                                ],
                                'Location Stock As At Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'inventory-location-as-at' => 'inventory-location-as-at'
                                    ]
                                ],
                                'Items Data Sales Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'items-data-sales' => 'items-data-sales'
                                    ]
                                ],
                                'Items Data Purchases Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'promotion-items' => 'promotion-items'
                                    ]
                                ],
                                'Transfers Inwards Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'transfer-inwards-report' => 'transfer-inwards-report'
                                    ]
                                ],
                                'Item Sales Route Performance Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'item-sales-route-performance-report' => 'item-sales-route-performance-report'
                                    ]
                                ],
                                'Missing Series Numbers' => [
                                    'model' => 'number-series-utility',
                                    'permissions' => [
                                        'missing_invoice_series_numbers' => 'missing_invoice_series_numbers'
                                    ]
                                ],
                                'Item Margins Report' => [
                                    'model' => 'sales-and-receivables-reports',
                                    'permissions' => [
                                        'item-margins-report' => 'item-margins-report'
                                    ]
                                ],
                                'Price List Cost' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'price-list-cost-report' => 'price-list-cost-report'
                                    ]
                                ],
                                'Multi Supplier Items' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'items-with-multiple-suppliers' => 'items-with-multiple-suppliers'
                                    ]
                                ],
                                'Daily Moves Report' => [
                                    'model' => 'inventory-reports',
                                    'permissions' => [
                                        'daily-moves-report' => 'daily-moves-report'
                                    ]
                                ],
                            
                            ],
                        ],
                    ]
                ]
            ]
        ]
    ];

    return $inventoryMainPermissions;
}


function flattenInventoryPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenInventoryPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}