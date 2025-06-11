@extends('layouts.admin.admin')

@section('content')
    <style>
        .bg-grey th,
        .bg-grey td,
        .bg-grey {
            background-color: #eee;
        }
    </style>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="box-title">Chart of Accounts</h3>
                    @if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin')
                        <div align="right">
                            {{-- <a href="{!! route('chart-of-accounts.newindex') !!}" class="btn btn-success">Old {!! $title !!}</a> --}}
                            <a href="{!! route($model . '.create') !!}" class="btn btn-success">Add {!! $title !!}</a>
                            <a href="{!! route('chart-of-accounts.downloadCoaitems') !!}" class="btn btn-danger">Excel</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    @include('message')
                </div>

                <div class="container-fluid" style="margin-top: 10px">
                    <div class="linefixer"></div>
                    @foreach ($lists as $accountSection)
                        <ul class="permission-list level-0">
                            <li>
                                <div class="permission-title">
                                    <div class="remove-line"></div>
                                    <i class="toggle-icon fa fa-plus-square"></i>
                                    <i class="folder-icon fa fa-folder"></i>
                                    {{ $accountSection->section_name }} ({{ $accountSection->section_number }})
                                </div>
                                <ul class="permission-children" style="display: none;">
                                    @foreach ($accountSection->getWaAccountGroup as $accountGroup)
                                        <li>
                                            <div class="permission-title">
                                                <i class="toggle-icon fa fa-plus-square"></i>
                                                <i class="folder-icon fa fa-folder"></i>
                                                {{ $accountGroup->group_name }} ({{ $accountGroup->sequence_in_tb }})
                                            </div>
                                            <ul class="permission-children" style="display: none;">

                                                @foreach ($accountGroup->accountSubSections as $subSection)
                                                    <li>
                                                        <div class="permission-title">
                                                            <i class="toggle-icon fa fa-plus-square"></i>
                                                            <i class="folder-icon fa fa-folder"></i>
                                                            {{ $subSection->section_name }}
                                                            ({{ $subSection->section_code }})
                                                        </div>
                                                        <ul class="permission-children" style="display: none;">
                                                            <li class="permission-permission">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">#</th>
                                                                            <th scope="col">Account Code</th>
                                                                            <th scope="col">Account Name</th>
                                                                            <th scope="col">Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($subSection->accounts as $index => $account)
                                                                            <tr>
                                                                                <th scope="row">{{ $index + 1 }}</th>
                                                                                @php
                                                                                    $startDate = \Carbon\Carbon::now()
                                                                                        ->subMonth()
                                                                                        ->format('Y-m-d');
                                                                                    $endDate = \Carbon\Carbon::now()->format(
                                                                                        'Y-m-d',
                                                                                    );
                                                                                @endphp
                                                                                <td>
                                                                                    <a href="{{ route('trial-balance.account', ['account' => $account->account_code]) }}?start-date={{ $startDate }}&end-date={{ $endDate }}&showing=100"
                                                                                        target="_blank">
                                                                                        {{ $account->account_code }}
                                                                                    </a>
                                                                                </td>
                                                                                </td>
                                                                                <td>{{ $account->account_name }}</td>
                                                                                <td>
                                                                                    <a href="{{ route('chart-of-accounts.show', ['chart_of_account' => $account->account_code]) }}"
                                                                                        style="cursor: pointer;font-weight:bolder" target="_blank">
                                                                                        <i class="fa fa-edit"></i>
                                                                                    </a>
                                                                                    <a href="{{ route('admin.account-inquiry.index', ['account' => $account->id]) }}"
                                                                                        style="cursor: pointer;font-weight:bolder" target="_blank">
                                                                                        <i class="fa fa-link"></i>
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <style>
        .permission-list {
            list-style: none;
            padding-left: 20px;
            position: relative;
        }

        .permission-list>li {
            position: relative;
        }

        .remove-line {
            background-color: white;
            height: 20px;
            width: 20px;
            left: -20px;
            top: 0;
            position: absolute;
        }

        .permission-children {
            list-style: none;
            padding-left: 20px;
            position: relative;
            display: none;
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
            left: -15px;
            border-top: 2px dotted #ccc;
            width: 15px;
        }

        .permission-title::before:first-child {
            display: none
        }

        .permission-title .fa {
            margin-right: 10px;
            position: relative;
        }

        .permission-children {
            padding-left: 20px;
            position: relative;
        }

        .permission-children>li::before {
            content: "";
            position: absolute;
            top: -4px;
            left: 5px;
            border-left: 2px solid #ccc;
            height: calc(100% + 7px);
        }

        .permission-title.open+.permission-children>li::before {
            display: block;
        }

        .permission-children>li:not(:has(> ul, > ol))::before {
            display: none;
        }

        .permission-title .folder-icon {
            color: #000;
        }

        .permission-title.open .folder-icon {
            color: #FEF179;
            transform: rotate(90deg);
        }

        .fa-folder-open:before {
            content: "\f07c";
            color: #FEF179;
        }

        i.folder-icon.fa.fa-folder {
            color: #FEF179;
        }

        .table {
            margin-left: 20px;
            margin-bottom: 20px;
            margin-top: 20px;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggles = document.querySelectorAll('.permission-title');
            toggles.forEach(function(toggle) {
                var icon = toggle.querySelector('.toggle-icon');
                var children = toggle.nextElementSibling;
                children.style.display = "none";
                icon.classList.remove('fa-minus-square');
                icon.classList.add('fa-plus-square');

                toggle.addEventListener('click', function() {
                    var icon = this.querySelector('.toggle-icon');
                    var folder = this.querySelector('.folder-icon');
                    var children = this.nextElementSibling;

                    if (children.style.display === "none" || children.style.display === "") {
                        children.style.display = "block";
                        icon.classList.remove('fa-plus-square');
                        icon.classList.add('fa-minus-square');
                        folder.classList.add('fa-folder-open');
                        folder.classList.remove('fa-folder');
                    } else {
                        children.style.display = "none";
                        icon.classList.remove('fa-minus-square');
                        icon.classList.add('fa-plus-square');
                        folder.classList.add('fa-folder');
                        folder.classList.remove('fa-folder-open');
                    }
                });
            });
        });
    </script>
@endsection
