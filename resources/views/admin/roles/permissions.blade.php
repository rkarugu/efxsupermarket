@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{!! $title !!}</h3>
            </div>

            @include('message')
            {!! Form::model($row, [
                'method' => 'PATCH',
                'route' => ['users.permissions.updateform', $row->slug],
                'class' => 'validate',
                'enctype' => 'multipart/form-data',
            ]) !!}
            {{ csrf_field() }}
            <div class="linefixer"></div>
            @include('admin.roles.roles-permissions-includes.management-dashboard')

            @include('admin.roles.roles-permissions-includes.sales-and-receivables')

            @include('admin.roles.roles-permissions-includes.delivery-and-logistics')

            @include('admin.roles.roles-permissions-includes.purchases')

            @include('admin.roles.roles-permissions-includes.supplier_portal')

            @include('admin.roles.roles-permissions-includes.account-payables')

            @include('admin.roles.roles-permissions-includes.inventory')
            
            @include('admin.roles.roles-permissions-includes.hr-and-payroll')

            @include('admin.roles.roles-permissions-includes.general-ledger')

            @include('admin.roles.roles-permissions-includes.fleet-management')

            @include('admin.roles.roles-permissions-includes.asset-management')

            @include('admin.roles.roles-permissions-includes.help-desk')

            @include('admin.roles.roles-permissions-includes.system-setup')

            @include('admin.roles.roles-permissions-includes.communications-centre')

            <div class="box-footer">
                {!! Form::submit(trans('Update'), ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>

        {{-- <div class="card-block table-responsive">
            <table class="table table-bordered table-striped table-condensed">
                <thead>
                    <tr>

                        <th style="width:25%">Module</th>
                        <th style="width:75%">Actions</th>


                    </tr>
                </thead>
                <?php $parents_managing = [
                    'menu-setup' => 'red',
                    'system-setup' => 'red',
                    'manage-orders' => 'red',
                    'app-users' => 'red',
                    'recipes' => 'red',
                    'WEBACCOUNTING-ERP' => 'red',
                    'Reports' => 'red',
                    'condiments' => 'green',
                    'Menu-Item' => 'green',
                    'postpaid-orders' => 'green',
                    'feedback' => 'red',
                    'bills' => 'violet',
                    'dashboard' => 'red',
                    'manage-delivery-orders' => 'red',
                    'dispatch-and-delivery' => 'red',
                    'alerts-and-notifications' => 'red',
                ]; ?>
                @foreach ($permisssion_array as $module => $permission_data)
                    <tr>
                        <td @if (isset($parents_managing[$module])) style="color:{!! $parents_managing[$module] !!}" @endif>
                            {!! ucfirst($module) !!}</td>
                        <td class="all_modules_check" id="{!! $module !!}">
                            @foreach ($permission_data as $p_key => $permission)
                                <span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label class="checkbox-inline">
                                        <input type="checkbox" value="{!! $p_key !!}" id="{!! $module . '___' . $p_key !!}"
                                            name="{!! strtolower($module) . '___' . $p_key !!}" <?php
                                           if (isset($previous_permissions[strtolower($module) . '___' . $p_key])){
                                               ?> checked
                                            <?php } ?>>&nbsp;&nbsp;{!! ucfirst($p_key) !!}
                                    </label></span>

                                @if ($p_key == 'view')
                                    <input id="hidden_{!! $module !!}" type="hidden"
                                        name="{!! strtolower($module) . '___' . $p_key !!}"
                                        value="@if (isset($previous_permissions[strtolower($module) . '___' . $p_key])) view @endif" />
                                @endif
                            @endforeach
                        </td>
                    </tr>
                @endforeach

            </table>


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div> --}}

    </section>
@endsection

@section('uniquepagestyle')
    <style>
        .linefixer {
            background-color: white;
            width: 6px;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 99999;
        }

        .permission-list {
            list-style: none;
            padding-left: 20px;
            position: relative;
        }

        .permission-list>ul:first-child {
            display: none;
        }

        .permission-list>li {
            position: relative;
        }

        .permission-list>li::before {
            content: "";
            position: absolute;
            top: -25px;
            left: -35px;
            border-left: 2px solid #ccc;
            height: calc(100% + 25px);
        }

        .permission-title {
            cursor: pointer;
            display: flex;
            align-items: center;
            position: relative;
        }

        .permission-title::before {
            content: "";
            position: absolute;
            top: 50%;
            left: -33px;
            border-top: 2px dotted #ccc;
            width: 20px;
        }

        div.container-fluid::before {
            width: 30px;
            height: 20px;
            background-color: #fff;
            position: absolute;
            left: 0;
            z-index: 999;
        }

        .permission-title .fa {
            margin-right: 10px;
        }

        .permission-children {
            list-style: none;
            padding-left: 20px;
            position: relative;
        }

        .checkbox-inline {
            margin-left: 0;
        }

        .toggle-icon {
            color: #000;
        }

        .folder-icon {
            color: #FEF179;
        }

        .permission-title.open .folder-icon {
            color: #FEF179;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script>
        $(document).ready(function() {

            // $('body').addClass('sidebar-collapse');

            $('.permission-title').click(function() {
                var $icon = $(this).find('.toggle-icon');
                var $children = $(this).next('.permission-children');

                $children.slideToggle();

                $icon.toggleClass('fa-plus-square fa-minus-square');

                $(this).find('.folder-icon').toggleClass('fa-folder fa-folder-open');
            });

            // $('input[type="checkbox"]').click(function() {
            //     var clickedCheckbox = $(this);
            //     var module = clickedCheckbox.attr('name').split('___')[0];
            //     var action = clickedCheckbox.val();

            //     var tableCheckbox = $('table.table-bordered.table-striped.table-condensed').find(
            //         'input[type="checkbox"][name="' + module + '___' + action + '"]');

            //     tableCheckbox.prop('checked', clickedCheckbox.is(':checked'));

            //     if (action === 'view') {
            //         var hiddenInput = $('input[type="hidden"][name="' + module + '___view"]');
            //         hiddenInput.val(clickedCheckbox.is(':checked') ? 'view' : '');
            //     } else {
            //         if (clickedCheckbox.is(':checked') && action !== 'view') {
            //             tableCheckbox.prop('checked', true);
            //             tableCheckbox.prop('disabled', true);
            //             $("#hidden_" + module).val('view');
            //         } else if (clickedCheckbox.is(':checked')) {
            //             var anyUnchecked = $(document.querySelectorAll('.all_modules_check #' + module +
            //                 ' input[type="checkbox"]:not(:checked)')).length > 0;
            //             if (!anyUnchecked) {
            //                 tableCheckbox.prop('checked', true);
            //                 tableCheckbox.prop('disabled', true);
            //             }
            //             $("#hidden_" + module).val(clickedCheckbox.is(':checked') ? 'view' : '');
            //         } else {
            //             tableCheckbox.prop('checked', false);
            //             tableCheckbox.prop('disabled', false);
            //             $("#hidden_" + module).val('');
            //         }
            //     }
            // });

            $('input[type="checkbox"]').click(function() {
                var clickedCheckbox = $(this);
                var module = clickedCheckbox.attr('name').split('___')[0];
                var action = clickedCheckbox.val();

                if (action === 'view') {
                    var hiddenInput = $('input[type="hidden"][name="' + module + '___view"]');
                    hiddenInput.val(clickedCheckbox.is(':checked') ? 'view' : '');
                } else {
                    var viewCheckbox = $('table.table-bordered.table-striped.table-condensed').find(
                        'input[type="checkbox"][name="' + module + '___view"]');

                    if (clickedCheckbox.is(':checked') && !viewCheckbox.is(':checked')) {
                        viewCheckbox.prop('checked', true);
                        $("#hidden_" + module).val('view');
                    } else if (!clickedCheckbox.is(':checked')) {
                        var anyChecked = $('table.table-bordered.table-striped.table-condensed').find(
                                'input[type="checkbox"][name^="' + module + '"]').not('[name$="___view"]')
                            .is(':checked');
                        if (!anyChecked && viewCheckbox.is(':checked')) {
                            viewCheckbox.prop('checked', true);
                            $("#hidden_" + module).val('view');
                        }
                    }
                }
            });


            $(document).ready(function() {
                $('table.table-bordered.table-striped.table-condensed tr').each(function() {
                    var module = $(this).find('td:first').text().trim();
                    var tableCheckbox = $(this).find('input[type="checkbox"][name="' + module +
                        '___view"]');
                    var numberOfChecked = $(this).find('input:checkbox:checked').length;

                    if (numberOfChecked >= 1) {
                        if (numberOfChecked === 1) {
                            tableCheckbox.prop('checked', true);
                            tableCheckbox.prop('disabled', false);
                        } else {
                            tableCheckbox.prop('checked', true);
                            tableCheckbox.prop('disabled', true);
                        }
                    } else {
                        tableCheckbox.prop('checked', false);
                        tableCheckbox.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
