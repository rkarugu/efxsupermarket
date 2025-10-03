<?php
    $authuser = Auth::user();
    $authuserlocation = $authuser->wa_location_and_store_id;
    $isAdmin = $authuser->role_id == 1;
    $hasPermission = isset($permission['maintain-items___view-all-stocks']);
?>

<?php $__env->startSection('content'); ?>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="box-header-flex">
                    <h3 class="box-title"><?php echo e($item->title); ?> - (<?php echo e($item->stock_id_code); ?>)</h3>
                    <div>
                        <a href="<?php echo e(route('maintain-items.index')); ?>" class="btn btn-success">Back</a>

                        <?php if(can('edit', 'maintain-items')): ?>
                            <a href = "<?php echo route('maintain-items.edit', $item->slug); ?>" class = "btn btn-success">
                                <i class="fa fa-edit"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(can('manage-item-stock', 'maintain-items')): ?>
                            <a href ="#" data-toggle="modal" data-target="#manage-stock-model"
                                class = "btn btn-success">
                                <i class="fa fa-bolt"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(can('manage-category-pricing', 'maintain-items')): ?>
                            <a href="#" data-toggle="modal" data-target="#manage-category-model"
                                class = "btn btn-success">
                                <i class="fa fa-money"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-2">
                        <label for="">Pack Size</label>
                        <p>
                            <?php if($item->pack_size): ?>
                                <?php echo e($item->pack_size->title); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-2">
                        <label for="">Price List Cost</label>
                        <p><?php echo e(manageAmountFormat($item->price_list_cost)); ?></p>
                    </div>
                    <div class="col-md-2">
                        <label for="">Standard Cost</label>
                        <p><?php echo e(manageAmountFormat($item->standard_cost)); ?></p>
                    </div>
                    <div class="col-md-2">
                        <label for="">Last GRN Cost</label>
                        <p><?php echo e(manageAmountFormat($item->last_grn_cost)); ?></p>
                    </div>
                    <div class="col-md-2">
                        <label for="">Weighted Average Cost</label>
                        <p><?php echo e(manageAmountFormat($item->weighted_average_cost)); ?></p>
                    </div>
                    <div class="col-md-2">
                        <label for="">Selling Price</label>
                        <p><?php echo e(manageAmountFormat($item->selling_price)); ?></p>
                    </div>
                    <div class="col-md-2">
                        <label for="">Margin</label>
                        <?php if($item->margin_type == 1): ?>
                            <p><?php echo e($item->percentage_margin); ?> %</p>
                        <?php else: ?>
                            <p>Kes <?php echo e($item->percentage_margin); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-2">
                        <label for="">Tax</label>
                        <p>
                            <?php if($item->getTaxesOfItem): ?>
                                <?php echo e($item->getTaxesOfItem->title); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Supplier(s)</label>
                        <p>
                            <?php echo e(implode(',', $item->suppliers->pluck('name')->toArray())); ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <ul class="nav nav-tabs">
                <?php if(can('view-stock-movements', 'maintain-items')): ?>
                    <li class="active"><a href="#stock_movements" data-toggle="tab">Stock Movements</a></li>
                <?php endif; ?>
                <?php if(can('view-stock-status', 'maintain-items')): ?>
                    <li><a href="#stock_status" data-toggle="tab">Stock Status</a></li>
                <?php endif; ?>
                <?php if(can('maintain-purchasing-data', 'maintain-items')): ?>
                    <li><a href="#purchase_data" data-toggle="tab">Purchase Data</a></li>
                <?php endif; ?>
                <?php if(can('assign-inventory-items', 'maintain-items')): ?>
                    <li><a href="#inventory_items" data-toggle="tab">Small Packs</a></li>
                <?php endif; ?>
                <?php if(can('price-change-history', 'maintain-items')): ?>
                    <li><a href="#price_change_history" data-toggle="tab">Price Change History</a></li>
                <?php endif; ?>
                <?php if(can('update-bin-location', 'maintain-items')): ?>
                    <li><a href="#bin_location" data-toggle="tab">Bin Location</a></li>
                <?php endif; ?>
                <?php if(can('manage-discount', 'maintain-items')): ?>
                    <li><a href="#discounts" data-toggle="tab">Discounts</a></li>
                <?php endif; ?>
                <?php if(can('manage-promotions', 'maintain-items')): ?>
                    <li><a href="#promotions" data-toggle="tab">Promotions</a></li>
                <?php endif; ?>
                <?php if(can('route-pricing', 'maintain-items')): ?>
                    <li><a href="#route_pricing" data-toggle="tab">Route Pricing</a></li>
                <?php endif; ?>
                <?php if(can('view-shop-pricing', 'maintain-items')): ?>
                    <li><a href="#location_prices" data-toggle="tab">Branch Pricing</a></li>
                <?php endif; ?>
            </ul>

            <div class="tab-content">
                <?php if(can('view-stock-movements', 'maintain-items')): ?>
                    <div class="tab-pane active" id="stock_movements">
                        <?php echo $__env->make('admin.item_centre.partials.stock_movements', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endif; ?>
                <?php if(can('view-shop-pricing', 'maintain-items')): ?>
                    <div class="tab-pane" id="location_prices">
                        <?php if (isset($component)) { $__componentOriginalb87aea92886c70e75a59eddc9b2f746d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb87aea92886c70e75a59eddc9b2f746d = $attributes; } ?>
<?php $component = App\View\Components\ItemCentre\ShopPrices::resolve(['itemId' => ''.e($item->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('item-centre.shop-prices'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ItemCentre\ShopPrices::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb87aea92886c70e75a59eddc9b2f746d)): ?>
<?php $attributes = $__attributesOriginalb87aea92886c70e75a59eddc9b2f746d; ?>
<?php unset($__attributesOriginalb87aea92886c70e75a59eddc9b2f746d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb87aea92886c70e75a59eddc9b2f746d)): ?>
<?php $component = $__componentOriginalb87aea92886c70e75a59eddc9b2f746d; ?>
<?php unset($__componentOriginalb87aea92886c70e75a59eddc9b2f746d); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if(can('view-stock-status', 'maintain-items')): ?>
                    <div class="tab-pane" id="stock_status">
                        <?php echo $__env->make('admin.item_centre.partials.stock_status', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endif; ?>
                <?php if(can('maintain-purchasing-data', 'maintain-items')): ?>
                    <div class="tab-pane" id="purchase_data">
                        <?php if (isset($component)) { $__componentOriginaldadb5ee47b9e4b0be9520cb326a548ba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldadb5ee47b9e4b0be9520cb326a548ba = $attributes; } ?>
<?php $component = App\View\Components\ItemCentre\PurchaseData::resolve(['itemId' => ''.e($item->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('item-centre.purchase-data'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ItemCentre\PurchaseData::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldadb5ee47b9e4b0be9520cb326a548ba)): ?>
<?php $attributes = $__attributesOriginaldadb5ee47b9e4b0be9520cb326a548ba; ?>
<?php unset($__attributesOriginaldadb5ee47b9e4b0be9520cb326a548ba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldadb5ee47b9e4b0be9520cb326a548ba)): ?>
<?php $component = $__componentOriginaldadb5ee47b9e4b0be9520cb326a548ba; ?>
<?php unset($__componentOriginaldadb5ee47b9e4b0be9520cb326a548ba); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if(can('assign-inventory-items', 'maintain-items')): ?>
                    <div class="tab-pane" id="inventory_items">
                        <?php if (isset($component)) { $__componentOriginal257767457cc0f4faf1cd26af40d31928 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal257767457cc0f4faf1cd26af40d31928 = $attributes; } ?>
<?php $component = App\View\Components\ItemCentre\InventoryItems::resolve(['itemId' => ''.e($item->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('item-centre.inventory-items'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ItemCentre\InventoryItems::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal257767457cc0f4faf1cd26af40d31928)): ?>
<?php $attributes = $__attributesOriginal257767457cc0f4faf1cd26af40d31928; ?>
<?php unset($__attributesOriginal257767457cc0f4faf1cd26af40d31928); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal257767457cc0f4faf1cd26af40d31928)): ?>
<?php $component = $__componentOriginal257767457cc0f4faf1cd26af40d31928; ?>
<?php unset($__componentOriginal257767457cc0f4faf1cd26af40d31928); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if(can('price-change-history ', 'maintain-items')): ?>
                    <div class="tab-pane" id="price_change_history">
                        <?php if (isset($component)) { $__componentOriginald3b04522b3f63ea5971da69582e33c61 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald3b04522b3f63ea5971da69582e33c61 = $attributes; } ?>
<?php $component = App\View\Components\ItemCentre\PriceChangeHistory::resolve(['itemId' => ''.e($item->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('item-centre.price-change-history'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ItemCentre\PriceChangeHistory::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald3b04522b3f63ea5971da69582e33c61)): ?>
<?php $attributes = $__attributesOriginald3b04522b3f63ea5971da69582e33c61; ?>
<?php unset($__attributesOriginald3b04522b3f63ea5971da69582e33c61); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald3b04522b3f63ea5971da69582e33c61)): ?>
<?php $component = $__componentOriginald3b04522b3f63ea5971da69582e33c61; ?>
<?php unset($__componentOriginald3b04522b3f63ea5971da69582e33c61); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if(can('update-bin-location', 'maintain-items')): ?>
                    <div class="tab-pane" id="bin_location">
                        <?php if (isset($component)) { $__componentOriginalc9cf13f51aaff97eab7cd33627d92b9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9cf13f51aaff97eab7cd33627d92b9d = $attributes; } ?>
<?php $component = App\View\Components\ItemCentre\BinLocation::resolve(['itemId' => ''.e($item->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('item-centre.bin-location'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ItemCentre\BinLocation::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9cf13f51aaff97eab7cd33627d92b9d)): ?>
<?php $attributes = $__attributesOriginalc9cf13f51aaff97eab7cd33627d92b9d; ?>
<?php unset($__attributesOriginalc9cf13f51aaff97eab7cd33627d92b9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9cf13f51aaff97eab7cd33627d92b9d)): ?>
<?php $component = $__componentOriginalc9cf13f51aaff97eab7cd33627d92b9d; ?>
<?php unset($__componentOriginalc9cf13f51aaff97eab7cd33627d92b9d); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if(can('manage-discount', 'maintain-items')): ?>
                    <div class="tab-pane" id="discounts">
                        <?php if (isset($component)) { $__componentOriginal0a3695cbf152e332222bb8c5bec348a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0a3695cbf152e332222bb8c5bec348a3 = $attributes; } ?>
<?php $component = App\View\Components\ItemCentre\Discounts::resolve(['itemId' => ''.e($item->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('item-centre.discounts'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ItemCentre\Discounts::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0a3695cbf152e332222bb8c5bec348a3)): ?>
<?php $attributes = $__attributesOriginal0a3695cbf152e332222bb8c5bec348a3; ?>
<?php unset($__attributesOriginal0a3695cbf152e332222bb8c5bec348a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0a3695cbf152e332222bb8c5bec348a3)): ?>
<?php $component = $__componentOriginal0a3695cbf152e332222bb8c5bec348a3; ?>
<?php unset($__componentOriginal0a3695cbf152e332222bb8c5bec348a3); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if(can('manage-promotions', 'maintain-items')): ?>
                    <div class="tab-pane" id="promotions">
                        <?php if (isset($component)) { $__componentOriginal0608191a46930696efbd8c3c576a6a81 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0608191a46930696efbd8c3c576a6a81 = $attributes; } ?>
<?php $component = App\View\Components\ItemCentre\Promotions::resolve(['itemId' => ''.e($item->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('item-centre.promotions'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ItemCentre\Promotions::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0608191a46930696efbd8c3c576a6a81)): ?>
<?php $attributes = $__attributesOriginal0608191a46930696efbd8c3c576a6a81; ?>
<?php unset($__attributesOriginal0608191a46930696efbd8c3c576a6a81); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0608191a46930696efbd8c3c576a6a81)): ?>
<?php $component = $__componentOriginal0608191a46930696efbd8c3c576a6a81; ?>
<?php unset($__componentOriginal0608191a46930696efbd8c3c576a6a81); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if(can('route-pricing', 'maintain-items')): ?>
                    <div class="tab-pane" id="route_pricing">
                        <?php if (isset($component)) { $__componentOriginalfb3720af6ddb3b583ef81c9821c5a189 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfb3720af6ddb3b583ef81c9821c5a189 = $attributes; } ?>
<?php $component = App\View\Components\ItemCentre\RoutePricingComponent::resolve(['itemId' => ''.e($item->id).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('item-centre.route-pricing-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\ItemCentre\RoutePricingComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfb3720af6ddb3b583ef81c9821c5a189)): ?>
<?php $attributes = $__attributesOriginalfb3720af6ddb3b583ef81c9821c5a189; ?>
<?php unset($__attributesOriginalfb3720af6ddb3b583ef81c9821c5a189); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfb3720af6ddb3b583ef81c9821c5a189)): ?>
<?php $component = $__componentOriginalfb3720af6ddb3b583ef81c9821c5a189; ?>
<?php unset($__componentOriginalfb3720af6ddb3b583ef81c9821c5a189); ?>
<?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </section>
    <?php echo $__env->make('admin.item_centre.modals.adjust_item_stock', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.item_centre.modals.adjust_category_price', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('styles'); ?>
    <link href="<?php echo e(asset('assets/admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
    <div id="loader-on"
        style="position: fixed; top: 0; text-align: center; z-index: 999999;
                width: 100%;  height: 100%; background: #000000b8; display:none;"
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="<?php echo e(asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script>
        // Immediately patch Select2 after it loads
        (function() {
            if (typeof $.fn.select2 !== 'undefined') {
                var originalSelect2 = $.fn.select2;
                
                $.fn.select2 = function(options) {
                    if (options === 'destroy') {
                        return this.each(function() {
                            var $this = $(this);
                            try {
                                if ($this.hasClass('select2-hidden-accessible')) {
                                    originalSelect2.call($this, 'destroy');
                                }
                            } catch (e) {
                                // Silently handle destroy errors
                            }
                        });
                    }
                    
                    return this.each(function() {
                        var $this = $(this);
                        try {
                            return originalSelect2.call($this, options);
                        } catch (e) {
                            console.warn('Select2 operation failed:', e);
                            return $this;
                        }
                    });
                };
                
                // Copy over any static methods/properties
                for (var prop in originalSelect2) {
                    if (originalSelect2.hasOwnProperty(prop)) {
                        $.fn.select2[prop] = originalSelect2[prop];
                    }
                }
            }

            // Global error handler for uncaught JavaScript errors
            window.addEventListener('error', function(e) {
                if (e.message && e.message.includes('select2') && 
                    (e.message.includes('destroy') || e.message.includes('not using Select2'))) {
                    e.preventDefault();
                    console.warn('Select2 error suppressed:', e.message);
                    return false;
                }
            });

            // Handle unhandled promise rejections related to Select2
            window.addEventListener('unhandledrejection', function(e) {
                if (e.reason && e.reason.toString().includes('select2')) {
                    e.preventDefault();
                    console.warn('Select2 promise rejection suppressed:', e.reason);
                }
            });
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="<?php echo e(asset('js/sweetalert.js')); ?>"></script>
    
    <script>
        // Global error handler for Select2 issues
        $(document).ready(function() {

            // Override console.error to catch Select2 errors
            var originalError = console.error;
            console.error = function() {
                var args = Array.prototype.slice.call(arguments);
                var message = args.join(' ');
                
                // Suppress specific Select2 errors that are not critical
                if (message.includes('select2') && 
                    (message.includes('destroy') || message.includes('not using Select2'))) {
                    console.warn('Select2 warning (suppressed):', message);
                    return;
                }
                
                // Call original error function for other errors
                originalError.apply(console, args);
            };

            // Add safe Select2 methods to jQuery prototype
            $.fn.safeSelect2 = function(options) {
                return this.each(function() {
                    var $this = $(this);
                    try {
                        if ($this.hasClass('select2-hidden-accessible')) {
                            $this.select2('destroy');
                        }
                        $this.select2(options || {});
                    } catch (e) {
                        console.warn('Safe Select2 failed for element:', this, e);
                    }
                });
            };

            $.fn.safeSelect2Destroy = function() {
                return this.each(function() {
                    var $this = $(this);
                    try {
                        if ($this.hasClass('select2-hidden-accessible')) {
                            $this.select2('destroy');
                        }
                    } catch (e) {
                        console.warn('Safe Select2 destroy failed for element:', this, e);
                    }
                });
            };
        });

        // Safe Select2 destroy function
        function safeSelect2Destroy(selector) {
            try {
                var $element = $(selector);
                if ($element.length && $element.hasClass('select2-hidden-accessible')) {
                    $element.select2('destroy');
                }
            } catch (e) {
                console.warn('Select2 destroy failed for selector: ' + selector, e);
            }
        }

        // Safe Select2 initialization
        function safeSelect2Init(selector, options) {
            try {
                // Validate selector is not empty
                if (!selector || selector.trim() === '') {
                    console.warn('Empty selector passed to safeSelect2Init');
                    return;
                }
                
                var $element = $(selector);
                if ($element.length) {
                    // Destroy existing Select2 if present
                    if ($element.hasClass('select2-hidden-accessible')) {
                        $element.select2('destroy');
                    }
                    // Initialize Select2
                    $element.select2(options || {});
                }
            } catch (e) {
                console.warn('Select2 initialization failed for selector: ' + selector, e);
            }
        }

        function refreshTable(table) {
            table.DataTable().ajax.reload();
        }

        function printStockCard(input) {
            var url = "<?php echo e(route('maintain-items.stock-movements', ['stockIdCode' => $item->stock_id_code])); ?>?" + $(input)
                .parents(
                    'form').serialize() + '&type=print';
            print_this(url);
        }

        // Initialize Select2 for location dropdown in modal when modal is shown
        $('#manage-stock-model').on('shown.bs.modal', function () {
            // Small delay to ensure DOM is ready
            setTimeout(function() {
                safeSelect2Init('#location-input', {
                    placeholder: 'Please select',
                    allowClear: true
                });
            }, 100);
        });

        // Clean up Select2 when modal is hidden
        $('#manage-stock-model').on('hidden.bs.modal', function () {
            safeSelect2Destroy('#location-input');
            // Clear form values to prevent issues
            $('#current_qty_available').val('');
            $('#quantity-input').val('');
        });

        // Handle tab switching to reinitialize Select2 if needed
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");
            if (target === '#stock_movements') {
                // Reinitialize Select2 for stock movements tab
                setTimeout(function() {
                    if (typeof safeSelect2Init === 'function') {
                        safeSelect2Init("#storeLocation");
                        safeSelect2Init("#moveType");
                    }
                }, 100);
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/item_centre/show.blade.php ENDPATH**/ ?>