@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="box-title">Trade Agreement</h3>
                    </div>
                    <hr>
                    <div class="col-md-6">
                        <div class="row">
                            <form method="get" action="">
                                {{-- <div class="col-md-3">
                                    {!! Form::select(
                                        'status',
                                        ['Pending' => 'Pending', 'Rejected' => 'Rejected', 'Approved' => 'Approved'],
                                        request()->status ?? 'Approved',
                                        [
                                            'maxlength' => '255',
                                            'placeholder' => 'Select Status',
                                            'required' => false,
                                            'class' => 'form-control',
                                            'id' => 'select_2',
                                        ],
                                    ) !!}
                                </div> --}}
                                {{-- <div class="col-md-1" style="margin-right: 40px">
                                    <button type="submit" class="btn btn-success" id="filter-btn"> <i
                                            class="fa fa-filter"></i> &nbsp;
                                        Filter</button>
                                </div> --}}
                                <div class="col-md-4">
                                    <a href="{{ route($model . '.create') }}" class="btn btn-danger" id="create-btn"> <i
                                            class="fa fa-plus"></i> &nbsp; Create Trade
                                        Agreement</a>
                                </div>
                            </form>

                            <div class="col-md-3">
                                <form id="download-trade-agreements" action="{{ route('download_trade_agreements') }}"
                                    method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-primary" name="intent" id="download-pdf"
                                                value="Download Pdf">
                                                <i class="fa fa-file-pdf"></i> PDF
                                            </button>
                                        </div>
                                        @if (can('excel', 'trade-agreement'))
                                            <div class="col-md-6">
                                                <button type="submit" class="btn btn-primary" name="intent"
                                                    id="download-excel" value="Download Excel">
                                                    <i class="fa fa-file-excel"></i> EXCEL
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-4 locked-suppliers">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h5>Locked Suppliers</h5>
                                        <i class="fa fa-lock"></i>
                                    </div>
                                    <p>{{ $locked_count }} out of {{ $total_count }}</p>
                                </div>
                            </div>

                            <div class="col-sm-4 open-suppliers">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <h5>Open Suppliers</h5>
                                        <i class="fa fa-unlock"></i>
                                    </div>
                                    <p>{{ $unlocked_count }} out of {{ $total_count }}</p>
                                </div>
                            </div>

                            <div class="col-sm-4 open-suppliers">
                                <div class="small-box bg-aqua">
                                    <div class="inner">
                                        <h5>Signed in Portal</h5>
                                        <i class="fa fa-key" style="color: black"></i>
                                    </div>
                                    <p>{{ $signed_in_portal_count }} out of {{ $total_count }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th width="7%">S.No.</th>
                                <th>Reference</th>
                                <th>Supplier Code</th>
                                <th>Supplier</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Signed-in Portal?</th>
                                <th>Status</th>
                                <th class="noneedtoshort">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if (isset($trades) && !empty($trades))
                                <?php $b = 1; ?>
                                @foreach ($trades as $trade)
                                    <tr>
                                        <td>{!! $b !!}</td>
                                        <td>{!! $trade->reference !!}</td>
                                        <td>{!! $trade->supplier->supplier_code !!}</td>
                                        <td>{!! $trade->supplier->name !!}</td>
                                        <td>{!! @$trade->supplier->users->first()->name !!}</td>
                                        <td>{!! $trade->date !!}</td>
                                        <td class="{{ !$trade->linked_to_portal ? 'bg-aqua' : 'bg-green' }}">
                                            {{ $trade->linked_to_portal ? 'Yes' : 'No' }}</td>
                                        {{-- <td>{!! $trade->status !!}</td> --}}
                                        <td>
                                            @if ($trade?->is_locked == 0)
                                                Open
                                            @elseif ($trade?->is_locked == 1)
                                                Locked
                                            @else
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class='span-action'>
                                                <a href="{{ route($model . '.edit', $trade->id) }}">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            </span>
                                            @if ($trade->status == 'Approved')
                                                <span class='span-action'>
                                                    <a href="{{ route($model . '.get_document', $trade->id) }}">
                                                        <i class="fa fa-file"></i>
                                                    </a>
                                                </span>
                                            @endif
                                            @if (can('email', 'trade-agreement'))
                                                <span class='span-action'>
                                                    <a title="send email" href="#" data-toggle="modal"
                                                        data-target="#send-to-supplier-modal"
                                                        data-email="{{ $trade->supplier->email }}"
                                                        data-id="{{ $trade->supplier->id }}">
                                                        <i class="fa fa-envelope"></i>
                                                    </a>
                                                </span>
                                            @endif
                                            @if (can('lock', 'trade-agreement'))
                                                <span class='span-action'>
                                                    <a href="#"
                                                        onclick="openTheLockModal('{{ route('trade-agreement.lock_agreement', $trade->id) }}', '{{ $trade->is_locked ? 'Un Lock' : 'Lock' }}' ,'{!! $trade->reference !!}'); return false;">
                                                        @if ($trade->is_locked)
                                                            <i class="fa fa-lock" style="color:red"></i>
                                                        @else
                                                            <i class="fa fa-unlock" style="color:green"></i>
                                                        @endif
                                                    </a>
                                                </span>
                                            @endif
                                            @if (can('billing', 'trade-agreement'))
                                                <span class='span-action'>
                                                    <a
                                                        href="{{ route('trade-agreement.subscription_charges', $trade->id) }}">
                                                        <i class="fa fa-usd"></i>
                                                    </a>
                                                </span>
                                            @endif
                                            @if (can('email-subscribers', 'trade-agreement'))
                                                <span class='span-action'>
                                                    <a
                                                        href="{{ route('trade-agreement.email_subscribers', $trade->id) }}">
                                                        <i class="fa fa-comments"></i>
                                                    </a>
                                                </span>
                                            @endif
                                            @if (can('delete', 'trade-agreement') && !$trade->linked_to_portal)
                                                <span class='span-action'>
                                                  <x-actions.delete-record identifier="trd{{ $trade->id }}" action="{{ route('trade-agreement.destroy', $trade->id) }}" />
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <?php $b++; ?>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <x-suppliers.email-supplier-form />
    </section>
    <!-- Modal -->
    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="" method="post" class="submitMe">
            @csrf
            @method('PUT')
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style type="text/css">
        .span-action {
            display: inline-block;
            margin-right: 5px;
        }

        /* ALL LOADERS */
        .loader {
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before,
        #loader-1:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 100%;
            border: 10px solid transparent;
            border-top-color: #3498db;
        }

        #loader-1:before {
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after {
            border: 10px solid #ccc;
        }


        /* start custom css */

        .locked-suppliers,
        .open-suppliers {
            margin-bottom: -10px;
            margin-top: -15px;
        }

        .small-box {
            padding: 10px;
            min-width: 150px;
        }

        .small-box .inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .small-box .inner h5 {
            margin: -10px;
        }

        .small-box i {
            font-size: 1.5em;
        }

        i.fa.fa-lock {
            color: black;
        }

        i.fa.fa-unlock {
            color: black;
        }

        .small-box p {
            margin: 0;
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .small-box {
                padding: 8px;
            }

            .small-box .inner h5 {
                margin: -7px;
                font-size: 0.95em;
            }

            .small-box p {
                font-size: 0.85em;
            }
        }

        @media (max-width: 576px) {

            .locked-suppliers,
            .open-suppliers {
                margin-bottom: 5px;
                margin-top: 0;
            }

            .small-box {
                padding: 6px;
                min-width: auto;
            }

            .small-box .inner h5 {
                margin: -5px;
                font-size: 0.85em;
            }

            .small-box i {
                font-size: 1.2em;
            }

            .small-box p {
                font-size: 0.8em;
            }
        }

        /* end custom css */

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>
    <div id="loader-on"
        style="
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
"
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    {{-- <script src="{{ asset('js/form.js') }}"></script> --}}
    <script>
        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
            $('#select_2').select2()

            $('#download-trade-agreements').on('submit', function(e) {
                e.preventDefault();

                var submitButton = $(this).find('button[type="submit"]:focus');
                var intentValue = submitButton.val();

                $('#filter-btn').prop('disabled', true)
                $('#create-btn').css({
                    'pointer-events': 'none',
                    'cursor': 'not-allowed',
                    'opacity': '0.6'
                });

                submitButton.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentValue);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = (intentValue === 'Download Pdf' || intentValue === 'Download Excel') ?
                    'blob' : 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (intentValue === 'Download Pdf' || intentValue === 'Download Excel') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = intentValue === 'Download Pdf' ?
                                'TRADE-AGREEMENTS.pdf' :
                                'TRADE-AGREEMENTS.xlsx';
                            link.click();
                            form.successMessage('File downloaded successfully.')
                        } else {
                            form.successMessage('File downloaded successfully.')
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        var errorMessage = 'Something went wrong.';
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response && response.error) {
                                errorMessage = response.error;
                            }
                        } catch (err) {}
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    submitButton.prop('disabled', false).html(submitButton.data('original-text'));
                    $('#filter-btn').prop('disabled', false)
                    $('#create-btn').css({
                        'pointer-events': 'auto',
                        'cursor': 'pointer',
                        'opacity': '1'
                    });
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.',
                    });
                    submitButton.prop('disabled', false).html(submitButton.data('original-text'));
                    $('#filter-btn').prop('disabled', false)
                    $('#create-btn').css({
                        'pointer-events': 'auto',
                        'cursor': 'pointer',
                        'opacity': '1'
                    });
                };

                xhr.send(formData);
            });

            $('#download-trade-agreements button[type="submit"]').each(function() {
                $(this).data('original-text', $(this).html());
            });
        })

        function openTheLockModal(url, status, trade) {
            $("#modelId form").attr('action', url);
            $('#modelId .modal-title').html(status + " Trade Agreement");
            $('#modelId .modal-body').html("Click confirm to " + status + " this trade agreement: " + trade);
            $('#modelId').modal('show');
        }
    </script>
@endsection
