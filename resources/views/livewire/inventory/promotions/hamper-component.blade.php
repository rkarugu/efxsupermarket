<div>
    <div class="step-container">
        <form class="validate">
        @if($step == 1)
            <!-- Step 1: Hamper Details -->
            <div class="step">
                <h2>Hamper Details</h2>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" wire:model="hamper.name" class="form-control">
                    @error('hamper.name') <span class="text-danger error">{{ $message }}</span>@enderror

                </div>
                <div class="form-group">
                    <label for="code">Code</label>
                    <input type="text" id="code" wire:model="hamper.code" class="form-control">
                    @error('hamper.code') <span class="text-danger error">{{ $message }}</span>@enderror

                </div>

                <div class="form-group">
                    <label for="category">Category</label>

                    <select  id="category" wire:model="hamper.category" class="form-control mlselect">
                        <option value="">Select</option>
                        @foreach($categories as  $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('hamper.category') <span class="text-danger error">{{ $message }}</span>@enderror

                </div>
                <div class="form-group">
                    <label for="packsize">Packsize</label>
                    <select  id="packsize" wire:model="hamper.packsize" class="form-control mlselect">
                        <option value="">Select</option>
                       @foreach($packsizes as $key => $packsize)
                           <option value="{{ $key }}">{{ $packsize }}</option>
                       @endforeach
                    </select>
                    @error('hamper.packsize') <span class="text-danger error">{{ $message }}</span>@enderror

                </div>
                <div class="form-group">
                    <label for="maximum_order_quantity">Maximum Order Quantity</label>
                    <input type="number" id="maximum_order_quantity" wire:model="hamper.maximum_order_quantity" class="form-control">
                    @error('hamper.maximum_order_quantity') <span class="text-danger error">{{ $message }}</span>@enderror

                </div>
                <div class="form-group">
                    <label for="tax_category">Tax Category</label>
                    <select id="tax_category" wire:model="hamper.tax_category" class="form-control select2">
                        <!-- Options here -->
                    </select>
                    @error('hamper.tax_category') <span class="text-danger error">{{ $message }}</span>@enderror

                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" wire:model="hamper.image" class="form-control">

                </div>
                <div class="form-group">
                    <label for="is_blocked">Is Blocked</label>
                    <input type="checkbox" id="is_blocked" wire:model="hamper.is_blocked" class="form-check-input">
                    @error('hamper.is_blocked') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="is_blocked">Hamper Items</label>
                    <input type="number" id="hamper_items" wire:model="hamper_items" class="form-control">
                    @error('hamper_items') <span class="text-danger error">{{ $message }}</span>@enderror
                </div>
                <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>
            </div>
        @elseif($step == 2)
            <!-- Step 2: Select Items Related to Hamper -->
            <div class="step">
                <h2>Select Items</h2>
                <div class="form-group">
                    <button type="button" class="btn btn-secondary" wire:click="addItem">Add Item</button>
                </div>
                <div id="repeaterContainer">
                    <!-- Repeater template -->
                    <div class="form-group row repeater-item">
                        <div class="col-md-2">
                            <label for="name">Name</label>
                            <select class="form-control mlselect item-select" name="items[][id]">
                                <option value="">Select</option>
                                @foreach($inventoryItems as $item)
                                    <option value="{{ $item -> id }}">{{ $item -> title }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error"></span>
                        </div>
                        <div class="col-md-2">
                            <label for="selling_price">Selling Price</label>
                            <input type="number" class="form-control selling-price-input" name="items[][selling_price]" placeholder="Selling Price">
                            <span class="text-danger error"></span>
                        </div>
                        <div class="col-md-2">
                            <label for="cost">Cost</label>
                            <input type="number" class="form-control cost-input" name="items[][cost]" placeholder="Cost">
                            <span class="text-danger error"></span>
                        </div>
                        <div class="col-md-2">
                            <label for="qty">Qty</label>
                            <input type="number" class="form-control qty-input" name="items[][qty]" placeholder="Qty">
                            <span class="text-danger error"></span>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger mt-4 remove-btn">Remove</button>
                        </div>
                    </div>

                    <!-- Buttons for adding/removing items -->
                    <div class="form-group row">
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary add-item-btn">Add Item</button>
                        </div>
                    </div>

                    <!-- Navigation buttons -->
                </div>
                <div class="form-group row">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-secondary back-btn">Back</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary next-btn">Next</button>
                    </div>
                </div>
{{--                @foreach($items as $index => $item)--}}
{{--                    <div class="form-group row">--}}
{{--                        <div class="col-md-2">--}}
{{--                            <label for="items.{{ $index }}.id">Name</label>--}}
{{--                            <select id="items.{{ $index }}.id" wire:model="items.{{ $index }}.id" class="form-control mlselect item">--}}
{{--                                <option value="">Select</option>--}}
{{--                                @foreach($inventoryItems as $item)--}}
{{--                                    <option value="{{ $item -> id }}">{{ $item -> title }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}

{{--                            @error('items.' . $index . '.id') <span class="text-danger error">{{ $message }}</span>@enderror--}}
{{--                        </div>--}}
{{--                        <div class="col-md-2">--}}
{{--                            <label for="itemSellingPrice{{ $index }}">Selling Price</label>--}}
{{--                            <input type="number" id="itemSellingPrice{{ $index }}" wire:model="items.{{ $index }}.selling_price" class="form-control" placeholder="Selling Price">--}}
{{--                            @error('items.' . $index . '.selling_price') <span class="text-danger error">{{ $message }}</span>@enderror--}}
{{--                        </div>--}}
{{--                        <div class="col-md-2">--}}
{{--                            <label for="itemCost{{ $index }}">Cost</label>--}}
{{--                            <input type="number" id="itemCost{{ $index }}" wire:model="items.{{ $index }}.cost" class="form-control" placeholder="Cost">--}}
{{--                            @error('items.' . $index . '.cost') <span class="text-danger error">{{ $message }}</span>@enderror--}}
{{--                        </div>--}}
{{--                        <div class="col-md-2">--}}
{{--                            <label for="itemQty{{ $index }}">Qty</label>--}}
{{--                            <input type="number" id="itemQty{{ $index }}" wire:model="items.{{ $index }}.qty" class="form-control" placeholder="Qty">--}}
{{--                            @error('items.' . $index . '.qty') <span class="text-danger error">{{ $message }}</span>@enderror--}}
{{--                        </div>--}}
{{--                        <div class="col-md-2 d-flex align-items-end">--}}
{{--                            <button type="button" class="btn btn-danger mt-4" wire:click="removeItem({{ $index }})" style="margin-top: 10%;">Remove</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--                <button type="button" class="btn btn-secondary" wire:click="prevStep">Back</button>--}}
{{--                <button type="button" class="btn btn-primary" wire:click="nextStep">Next</button>--}}
            </div>
        @elseif($step == 3)
            <!-- Step 3: Select Branch and Duration -->
            <div class="step">
                <h2>Select Branch and Duration</h2>
                <div class="form-group">
                    <label for="branch">Branch</label>
                    <select id="branch" wire:model="branches" class="form-control select2">
                        <!-- Options here -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="promotion_duration">Promotion Duration (Days)</label>
                    <input type="number" id="promotion_duration" wire:model="promotion_duration" class="form-control">
                </div>
                <button type="button" class="btn btn-secondary" wire:click="prevStep">Back</button>
                <button type="button" class="btn btn-success" wire:click="submit">Submit</button>
            </div>
        @endif
        </form>
    </div>

</div>

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
{{--    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />--}}
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
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Counter for managing unique IDs
            let counter = 0;

            // Function to add a new item
            function addItem() {
                counter++;
                let newItem = $('.repeater-item').first().clone();
                newItem.find('.search-input').val('');
                newItem.find('.item-select').val('');
                newItem.find('.selling-price-input').val('');
                newItem.find('.cost-input').val('');
                newItem.find('.qty-input').val('');
                newItem.find('.error').text('');

                // Update name and id attributes to make them unique
                newItem.find('.search-input').attr('name', 'items[' + counter + '][name]');
                newItem.find('.item-select').attr('name', 'items[' + counter + '][id]');
                newItem.find('.selling-price-input').attr('name', 'items[' + counter + '][selling_price]');
                newItem.find('.cost-input').attr('name', 'items[' + counter + '][cost]');
                newItem.find('.qty-input').attr('name', 'items[' + counter + '][qty]');

                newItem.appendTo('#repeaterContainer');
            }

            // Function to remove an item
            $(document).on('click', '.remove-btn', function() {
                $(this).closest('.repeater-item').remove();
            });

            // Function to handle back button click
            $(document).on('click', '.back-btn', function() {
                // Implement your logic for back button
                console.log('Back button clicked');
            });

            // Function to handle next button click
            $(document).on('click', '.next-btn', function() {
                // Implement your logic for next button
                console.log('Next button clicked');
            });

            // Function to handle add item button click
            $(document).on('click', '.add-item-btn', function() {
                addItem();
            });

            // Initialize with one item
            addItem();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                if ($('.mlselect').length) { // Check if element exists
                    $('.mlselect').select2();
                }
            })
        });
        $(document).ready(function() {
            $('.mlselect').on('change', function (e) {
                var data = $(this).val();
                @this.set('hamper.'+$(this).attr('id'), data);
            });

            $('.items').on('change', function (e) {
                let data = $(this).val();
                @this.set($(this).attr('wire:model'), data);
            });

        });
    </script>


@endpush
