<div>
    <form id="price_locations_form">

        <?php
            $authuser = Auth::user();
            $authuserlocation = $authuser->wa_location_and_store_id;
            $isAdmin = $authuser->role_id == 1;
            $hasPermission = isset($permission['maintain-items___view-all-stocks']);
        ?>

        <div class="modal-body">
            <table class="table" style="width: 100%; margin-top: 10px">
                <tbody>
                    <tr>
                        <th>Standard Cost</th>
                        <td id="std_cost"><?php echo e($item->standard_cost); ?></td>
                        <th>Selling Price Inc Vat</th>
                        <td id="selling_pr"><?php echo e($item->selling_price); ?></td>
                    </tr>
                </tbody>
            </table>

            <div id="validation_errors" class="text-danger mb-3">

            </div>
            <table class="table" style="margin-top: 10px">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Selling Price</th>
                        <th>Is Flash</th>
                    </tr>
                </thead>
                <tbody id="price_locations_table_body">
                    <?php $__currentLoopData = $item->locationPrices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(ucfirst($price->location?->location_name ?? 'Unknown Location')); ?></td>
                            
                            <td>
                                <input type="number" 
                                    name="selling_price_<?php echo e($price->location?->id ?? 'unknown'); ?>"
                                    class="form-control selling_price" 
                                    value="<?php echo e($price->selling_price); ?>"
                                    <?php if(!$isAdmin && !$hasPermission && ($price->location?->id ?? 0) != $authuserlocation): ?> disabled <?php endif; ?>>
                            </td>
                            <td>
                                <input type="checkbox" 
                                    name="is_flash_<?php echo e($price->location?->id ?? 'unknown'); ?>"
                                    class="form-check-input is_flash" 
                                    <?php if($price->is_flash): ?> checked <?php endif; ?>
                                    <?php if(!$isAdmin && !$hasPermission && ($price->location?->id ?? 0) != $authuserlocation): ?> disabled <?php endif; ?>>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="save_price_locations">Save changes</button>
            <!-- You can add additional buttons or actions here if needed -->
        </div>
    </form>
</div>

<?php $__env->startPush('scripts'); ?>
    <script src="<?php echo e(asset('assets/admin/dist/bootstrap-datepicker.js')); ?>"></script>
    <script src="<?php echo e(asset('js/sweetalert.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/form.js')); ?>"></script>
    
    <script>
        var VForm = new Form();
        $(document).ready(function() {
            function captureInputData() {
                var inputData = [];

                $('#price_locations_table_body tr').each(function() {
                    var storeId = $(this).find('input[type="number"]').attr('name').split('_')[2];
                    var sellingPrice = $(this).find('input[type="number"]').val();
                    var isFlash = $(this).find('input[type="checkbox"]').prop('checked');

                    inputData.push({
                        store_id: storeId,
                        selling_price: sellingPrice,
                        is_flash: isFlash
                    });
                });

                return {
                    price_data: inputData
                };
            }
            $('#save_price_locations').click(function() {
                var inputData = captureInputData();
                generateAjaxRequest(inputData);
            });

            function generateAjaxRequest(data) {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: '<?php echo e(route('maintain-items.update-price-per-location', $itemId)); ?>',
                    method: 'POST',
                    data: JSON.stringify(data), // Convert data to JSON format
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Include CSRF token in the headers
                    },
                    success: function(response) {
                        console.log('sucsess')
                        VForm.successMessage('Prices Updated successful');
                        $('#validation_errors').html('');
                    },
                    error: function(xhr, status, error) {
                        var errors = xhr.responseJSON.errors;
                        if (errors) {

                            var errorMessages = Object.values(errors).flat().join('<br>');
                            $('#validation_errors').html(errorMessages);

                        } else {
                            // Handle other types of errors
                            console.error(error);
                        }
                    }
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/components/item-centre/shop-prices.blade.php ENDPATH**/ ?>