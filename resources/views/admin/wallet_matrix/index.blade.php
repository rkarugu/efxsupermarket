@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border ">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>{{ $title }}</h3>
                    @if(isset($permission[$pmodule.'___add']) || $permission == 'superadmin')
                        <div align="right"><a href="{!! route('wallet-matrix.create')!!}" class="btn btn-success">Add Matrix</a>
                        </div>
                    @endif   
                    </div>
                  
                <hr>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_10">
                        <thead>
                        <tr>
                            <th >#</th>

                            <th >Parameter</th>
                            <th >Salesman %</th>
                            <th >Driver %</th>
                            <th >Turn Boy %</th>
                            <th >Driver GRN %</th>
                            <th >Action</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($walletParameters as $parameter)
                            <tr>
                                <th>{{$loop->iteration }}</th>
                            <td>{{$parameter->parameter}}</td>
                            <td>{{$parameter->salesman ?? '-'}}</td>
                            <td>{{$parameter->delivery_driver ?? '-'}}</td>
                            <td>{{$parameter->turn_boy}}</td>
                            <td>{{$parameter->driver_grn}}</td>

                            <td>
                                <div class="action-button-div">
                                    @if(isset($permission[$pmodule.'___edit']) || $permission =='superadmin')
                                        <a href="{{ route('wallet-matrix.edit', $parameter->id)}}"><i class="fas fa-pen" title="edit"></i></a>
                                    @endif

                                 </div>
                            </td>
                        </tr>
                                
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    </section>

@endsection
@section('uniquepagescript')
<script>
    $(document).ready(function() {
        $('.btn-decline').click(function() {
            var discountBandId = $(this).data('discount-band-id');
            $('#confirmationModal').find('#discount_band_id').val(discountBandId);
            $('#confirmationForm').attr('action', '{{ route('discount-bands.approve', ['discountBandId' => ':discountId']) }}'.replace(':discountId', discountBandId));
        });
    
        $('#confirmationModal').on('show.bs.modal', function(event) {
            var modal = $(this);
            modal.find('.btn-submit-updated-center').off('click').on('click', function() {
                // Here you can submit the form
                modal.find('form').submit();
                // Close the modal
                modal.modal('hide');
            });
        });


        $('.btn-decline2').click(function() {
            var discountBandId = $(this).data('discount-band-id');
            $('#confirmationModal2').find('#discount_band_id').val(discountBandId);
            $('#confirmationForm2').attr('action', '{{ route('discount-bands.delete', ['discountBandId' => ':discountId']) }}'.replace(':discountId', discountBandId));
        });
    
        $('#confirmationModal2').on('show.bs.modal', function(event) {
            var modal = $(this);
            modal.find('.btn-submit-updated-center2').off('click').on('click', function() {
                // Here you can submit the form
                modal.find('form').submit();
                // Close the modal
                modal.modal('hide');
            });
        });

        
   

    






       

    });
    </script>
@endsection



