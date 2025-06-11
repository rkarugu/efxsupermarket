<?php

function communicationsCentrePermissionFunction()
{
    $communicationsCentre = [
        'communicationsCentre' => [
            'title' => [
                'Communications Centre' => [
                    'model' => 'communication-center',
                    'permissions' => [
                        'view' => 'view',
                    ],
                    'children' => [
                        'Bulk SMS' => [
                            'model' => 'bulk-sms',
                            'permissions' => [
                                'view' => 'view',
                                'create' => 'create',
                                'test-message' => 'test-message',
                                'message-log' => 'message-log'
                            ],
                        ]
                    ]
                ],
            ],
        ],
    ];

    return $communicationsCentre;
}

function flattenCommunicationsCentrePermissions($permissions, &$flattenedPermissions = [], $parentModel = '')
{
    foreach ($permissions as $key => $permission) {
        if (isset($permission['permissions'])) {
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower(($parentModel ? $parentModel . '___' : '') . $permission['model'] . '___' . $permissionKey);
                $flattenedPermissions[$modelPermissionKey] = $permissionValue;
            }
        }
        if (isset($permission['children'])) {
            flattenCommunicationsCentrePermissions($permission['children'], $flattenedPermissions, $permission['model']);
        }
    }
}
