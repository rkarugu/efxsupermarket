@if ($logged_user_info->role_id == 1 || isset($my_permissions['communication-center___view']))
    <li class="treeview @if (isset($model) &&
                    ($model == 'bulk-sms-create' ||
                    $model == 'bulk-sms-test-message' ||
                        $model == 'bulk-sms-message-log')) active @endif">
        <a href="#"><i class="fa fa-solid fa-headset"></i><span>Communications Centre </span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            @if ($logged_user_info->role_id == 1 || isset($my_permissions['bulk-sms___view']))
                    <?php $active_class = isset($model) && in_array($model, [
                    'bulk-sms-create',
                    'bulk-sms-test-message',
                    'bulk-sms-message-log'
                ]) ? 'active' : ''; ?>
                <li class="treeview <?= $active_class ?>"><a href="#"><i class="fa fa-circle"></i>
                        <span>Bulk SMS</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['bulk-sms___create']))
                            <li class="@if (isset($model) && $model == 'bulk-sms-create') active @endif">
                                <a href="{!! route('bulk-sms.create') !!}">
                                    <i class="fa fa-circle"></i>
                                    Create Bulk Message
                                </a>
                            </li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['bulk-sms___test-message']))
                            <li class="@if (isset($model) && $model == 'bulk-sms-test-message') active @endif">
                                <a href="{!! route('bulk-sms.test-message') !!}">
                                    <i class="fa fa-circle"></i>
                                    Test Message
                                </a>
                            </li>
                        @endif
                        @if ($logged_user_info->role_id == 1 || isset($my_permissions['bulk-sms___message-log']))
                            <li class="@if (isset($model) && $model == 'bulk-sms-message-log') active @endif">
                                <a href="{!! route('bulk-sms.message-log') !!}">
                                    <i class="fa fa-circle"></i>
                                    Message Log
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
    </li>
@endif