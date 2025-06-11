<?php

function helpDeskPermissionFunction()
{
    $helpDeskPermissions = [
        'helpdesk' => [
            'title' => [
                'Help Desk' => [
                    'model' => 'help-desk',
                    'permissions' => [
                        'view' => 'view',
                    ],
                    'children' => [
                        'Tickets' => [
                            'model' => 'help-desk-tickets',
                            'permissions' => [
                                'my-tickets' => 'my-tickets',
                                'status-tickets' => 'status-tickets',
                                'show' => 'show',
                                'add' => 'add',
                                'update-status' => 'update-status',
                                'assign' => 'assign',
                                'respond' => 'respond'
                            ],

                        ],
                    ],
                ],
            ],
        ],
    ];

    return $helpDeskPermissions;
}

function flattenHelpDeskPermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenHelpDeskPermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}