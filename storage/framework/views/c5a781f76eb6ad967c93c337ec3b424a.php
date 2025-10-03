<div style="padding: 10px">
    <form action="<?php echo e(route('maintain-items.postassignInventoryItems', $item->id)); ?>" method="POST" class="submitMe">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo e($item->id); ?>">

        <h3 class="box-title">
            <button type="button" class="btn btn-danger btn-sm addNewrow">
                <i class="fa fa-plus" aria-hidden="true"></i></button>
            Assign Small Packs
        </h3>
        <div>
            <span class="destination_item"></span>
        </div>
        <table class="table table-bordered table-hover assigneditems">
            <thead>
                <tr>
                    <th>
                        Destination Item
                    </th>
                    <th>
                        Conversion factor
                    </th>
                    <th>
                        ##
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if($item->destinated_items->isNotEmpty()): ?>
                    <?php $__currentLoopData = $item->destinated_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <select name="destination_item[<?php echo e($key); ?>]"
                                    class="form-control destination_item destination_items">
                                    <?php if($item->destinated_item): ?>
                                        <option value="<?php echo e($item->destinated_item->id); ?>">
                                            <?php echo e($item->destinated_item->title); ?></option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="conversion_factor[<?php echo e($key); ?>]"
                                    class="form-control conversion_factor" value="<?php echo e($item->conversion_factor); ?>">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger deleteMe">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <tr>
                        <td>
                            <select name="destination_item[0]" class="form-control destination_item destination_items">

                            </select>
                        </td>
                        <td>
                            <input type="text" name="conversion_factor[0]" class="form-control conversion_factor"
                                value="">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger deleteMe"><i class="fa fa-trash"
                                    aria-hidden="true"></i></button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <br>
        <button type="submit" class="btn btn-danger">Assign</button>
        <button type="button" onclick="location.href='<?php echo e(route('maintain-items.index')); ?>'"
            class="btn btn-danger">Cancel</button>
    </form>
</div>
<?php $__env->startPush('scripts'); ?>
    <script>
        $(function() {
            $(document).on('click', '.deleteMe', function() {
                $(this).parents('tr').remove();
                return false;
            });

            var item = '<tr>' +
                '<td>' +
                '<select name="destination_item[0]" class="form-control destination_item destination_items"></select>' +
                '</td>' +
                '<td>' +
                '<input type="text" name="conversion_factor[0]" class="form-control conversion_factor">' +
                '</td>' +
                '<td>' +
                '<button type="button" class="btn btn-danger deleteMe"><i class="fa fa-trash" aria-hidden="true"></i></button>' +
                '</td>' +
                '</tr>';

            $(document).on('click', '.addNewrow', function() {
                // Use safe Select2 destroy method
                if (typeof $.fn.safeSelect2Destroy === 'function') {
                    $(".destination_items").safeSelect2Destroy();
                } else {
                    // Fallback to manual safe destroy
                    $(".destination_items").each(function() {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            try {
                                $(this).select2('destroy');
                            } catch (e) {
                                console.warn('Select2 destroy failed:', e);
                            }
                        }
                    });
                }
                
                $('.assigneditems tbody').append(item);
                var assigneditems = $('.assigneditems tbody tr');
                $.each(assigneditems, function(indexInArray, valueOfElement) {
                    $(this).find('.destination_item').attr('name', 'destination_item[' +
                        indexInArray + ']');
                    $(this).find('.conversion_factor').attr('name', 'conversion_factor[' +
                        indexInArray + ']');
                });
                destinated_item();
            });

            destinated_item();
        })

        function destinated_item() {
            var select2Options = {
                ajax: {
                    url: "<?php echo e(route('maintain-items.inventoryDropdown', ['id' => $item->id])); ?>",
                    dataType: 'json',
                    type: "GET",
                    data: function(term) {
                        return {
                            q: term.term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            };

            // Use safe Select2 initialization
            if (typeof $.fn.safeSelect2 === 'function') {
                $(".destination_items").safeSelect2(select2Options);
            } else {
                // Fallback to manual safe initialization
                $(".destination_items").each(function() {
                    var $this = $(this);
                    try {
                        if ($this.hasClass('select2-hidden-accessible')) {
                            $this.select2('destroy');
                        }
                        $this.select2(select2Options);
                    } catch (e) {
                        console.warn('Select2 initialization failed:', e);
                    }
                });
            }
        }
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/components/item-centre/inventory-items.blade.php ENDPATH**/ ?>