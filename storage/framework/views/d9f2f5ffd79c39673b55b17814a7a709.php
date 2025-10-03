<?php if($errors->any()): ?>
    <div class="alert absolte_alert alert-danger alert-dismissible" onClick="this.remove();">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i> Error!</h4>
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<?php if(session()->has('success') or session()->has('warning') or session()->has('info') or session()->has('danger')): ?>
    <div class="flash-message" onClick="this.remove();">
        <?php $__currentLoopData = ['danger', 'warning', 'success', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(session()->has($msg)): ?>
                <p class="alert absolte_alert  alert-<?php echo e($msg); ?>"><?php echo e(session()->get($msg)); ?> <a href="javascript:void(0)" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/message.blade.php ENDPATH**/ ?>