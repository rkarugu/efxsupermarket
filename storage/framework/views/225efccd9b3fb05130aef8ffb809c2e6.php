<?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk___view'])): ?>
    <li class="treeview <?php if(isset($model) &&
                    ($model == 'open-tickets' ||
                    $model == 'development-tickets' ||
                    $model == 'completed-tickets' ||
                    $model == 'closed-tickets' ||
                    $model == 'new-tickets' ||
                    $model == 'my-tickets' ||
                    $model == 'tickets')): ?> active <?php endif; ?>">
        <a href="#">
            <svg height="14px" style="margin-right: 5px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="#b8c7ce">
                <path d="M48 224C56.88 224 64 216.9 64 208V192c0-88.25 71.75-160 160-160s160 71.75 160 160v16C384 252.1 348.1 288 304 288h-32c0-17.62-14.38-32-32-32h-32c-17.62 0-32 14.38-32 32s14.38 32 32 32h96c61.88-.125 111.9-50.13 112-112V192c0-105.9-86.13-192-192-192S32 86.13 32 192v16C32 216.9 39.13 224 48 224zM208 224h32c22.88 0 43.98 12.2 55.36 31.95L304 256c26.5 0 48-21.5 48-47.1V192c0-70.75-57.25-128-128-128s-128 57.25-128 128c0 40.38 19.12 75.99 48.37 99.49C144.2 290.2 144 289.3 144 288C144 252.6 172.6 224 208 224zM314.7 352H133.3C59.7 352 0 411.7 0 485.3C0 500.1 11.94 512 26.66 512H421.3C436.1 512 448 500.1 448 485.3C448 411.7 388.3 352 314.7 352z"/>
            </svg><span>Help Desk</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>

        <ul class="treeview-menu">
            <?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk___view'])): ?>
                    <?php $active_class = isset($model) && in_array($model, [
                    'tickets',
                    'bulk-sms-test-message',
                    'bulk-sms-message-log'
                ]) ? 'active' : ''; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk-tickets___add'])): ?>
                    <li class="<?php if(isset($model) && $model == 'new-tickets'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('help-desk.tickets.create'); ?>">
                            <i class="fa fa-circle"></i>
                            New Tickets
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk-tickets___my-tickets'])): ?>
                    <li class="<?php if(isset($model) && $model == 'my-tickets'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('help-desk.my.tickets'); ?>">
                            <i class="fa fa-circle"></i>
                            My Tickets
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk-tickets___status-tickets'])): ?>
                    <li class="<?php if(isset($model) && $model == 'open-tickets'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('help-desk.tickets.index',['status'=>'Open']); ?>">
                            <i class="fa fa-circle"></i>
                            Open Tickets
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk-tickets___status-tickets'])): ?>
                    <li class="<?php if(isset($model) && $model == 'development-tickets'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('help-desk.tickets.index',['status'=>'Development']); ?>">
                            <i class="fa fa-circle"></i>
                            Development Tickets
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk-tickets___status-tickets'])): ?>
                    <li class="<?php if(isset($model) && $model == 'completed-tickets'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('help-desk.tickets.index',['status'=>'Completed']); ?>">
                            <i class="fa fa-circle"></i>
                            Completed Tickets
                        </a>
                    </li>
                <?php endif; ?>
                <?php if($logged_user_info->role_id == 1 || isset($my_permissions['help-desk-tickets___status-tickets'])): ?>
                    <li class="<?php if(isset($model) && $model == 'closed-tickets'): ?> active <?php endif; ?>">
                        <a href="<?php echo route('help-desk.tickets.index',['status'=>'Closed']); ?>">
                            <i class="fa fa-circle"></i>
                            Closed Tickets
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </li>
<?php endif; ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/sidebar_includes/help_desk.blade.php ENDPATH**/ ?>