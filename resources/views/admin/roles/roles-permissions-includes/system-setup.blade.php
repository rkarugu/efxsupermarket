@php
    function displaySystemSetupPermissions($permissions, $previous_permissions, $level = 0)
    {
        echo '<ul class="permission-list level-' . $level . '">';
        foreach ($permissions as $title => $permission) {
            echo '<li>';
            echo '<div class="permission-title">';
            echo '<i class="toggle-icon fa fa-plus-square"></i> <i class="folder-icon fa fa-folder"></i> ';
            echo $title;
            echo '</div>';
            echo '<ul class="permission-children" style="display: none;">';
            foreach ($permission['permissions'] as $permissionKey => $permissionValue) {
                $modelPermissionKey = strtolower($permission['model'] . '___' . $permissionKey);
                $isChecked =
                    isset($previous_permissions[$modelPermissionKey]) &&
                    $previous_permissions[$modelPermissionKey] == $permissionKey;
                echo '<li class="permission-permission">';
                echo '<label class="checkbox-inline">';
                echo '<input type="checkbox" value="' .
                    $permissionKey .
                    '" id="' .
                    $modelPermissionKey .
                    '" name="' .
                    $modelPermissionKey .
                    '"';
                if ($isChecked) {
                    echo ' checked';
                }
                // echo '>&nbsp;&nbsp;' . ucfirst($permissionKey) . ' (' . $permission['model'] . ')';
                echo '>&nbsp;&nbsp;' . ucfirst($permissionKey);
                echo '</label>';
                if ($permissionKey == 'view') {
                    echo '<input id="hidden_' .
                        $permission['model'] .
                        '" type="hidden" name="' .
                        $modelPermissionKey .
                        '" value="';
                    if ($isChecked) {
                        echo 'view';
                    }
                    echo '" />';
                }
                echo '</li>';
            }
            if (isset($permission['children'])) {
                displaySystemSetupPermissions($permission['children'], $previous_permissions, $level + 1);
            }
            echo '</ul>';
            echo '</li>';
        }
        echo '</ul>';
    }

    $permissionsToShow = systemAdministrationPermissionFunction();
@endphp

<div class="container-fluid" style="margin-top: 10px">
    <div class="row">
        <div class="col-md-12">
            <div class="permissions-wrapper">
                @foreach ($permissionsToShow as $mainTitle => $mainPermissions)
                    @foreach ($mainPermissions['title'] as $title => $permission)
                        <ul class="permission-list level-0">
                            <li>
                                <div class="permission-title">
                                    <i class="toggle-icon fa fa-plus-square"></i> <i
                                        class="folder-icon fa fa-folder"></i>
                                    {{ $title }}
                                </div>
                                <ul class="permission-children" style="display: none;">
                                    @foreach ($permission['permissions'] as $permissionKey => $permissionValue)
                                        @php
                                            $modelPermissionKey = strtolower(
                                                $permission['model'] . '___' . $permissionKey,
                                            );
                                            $isChecked =
                                                isset($previous_permissions[$modelPermissionKey]) &&
                                                $previous_permissions[$modelPermissionKey] == 'view';
                                        @endphp
                                        <li>
                                            <label class="checkbox-inline">
                                                <input type="checkbox" value="{{ $permissionKey }}"
                                                    id="{{ $modelPermissionKey }}" name="{{ $modelPermissionKey }}"
                                                    @if ($isChecked) checked @endif>
                                                &nbsp;&nbsp;{{ ucfirst($permissionKey) }}
                                            </label>
                                            @if ($permissionKey == 'view')
                                                <input id="hidden_{{ $permission['model'] }}" type="hidden"
                                                    name="{{ $modelPermissionKey }}"
                                                    value="@if ($isChecked) view @endif" />
                                            @endif
                                        </li>
                                    @endforeach
                                    @if (isset($permission['children']))
                                        @php displaySystemSetupPermissions($permission['children'], $previous_permissions, 1) @endphp
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
