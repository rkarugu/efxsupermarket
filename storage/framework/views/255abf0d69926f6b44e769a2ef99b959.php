
      <ol class="breadcrumb">
        <li><a href="<?php echo route('admin.dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <?php if(isset($breadcum) && is_array($breadcum) && count($breadcum)>0): ?>
        <?php  $br=1; $total_br=count($breadcum); ?>
       		<?php $__currentLoopData = $breadcum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcum_key=>$breadcum_url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
       			<?php if($total_br == $br): ?>
       			 <li class="active"> <?php echo ucfirst($breadcum_key); ?> </li>
       			 <?php else: ?>
       			 <li><a href="<?php echo $breadcum_url; ?>"> <?php echo ucfirst($breadcum_key); ?></a></li>
       			<?php endif; ?>
       			<?php $br++; ?>
        	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
      </ol><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/breadcum.blade.php ENDPATH**/ ?>