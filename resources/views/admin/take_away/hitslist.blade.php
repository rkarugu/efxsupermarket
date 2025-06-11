
@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="create_datatable">
                                    <thead>
                                        <tr>
                                        <th width="10%">S.No.</th>
                                        <th width="30%"  >Customer</th>
                                        <th width="20%"  >Created at</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $b = 1;?>
                                        @foreach($row as $list)
                                            <tr>
                                            <td>{!! $b !!}</td>
                                            <td>{!! $list->getAssociateUser->name!!}</td>
                                            <td>{!! date('Y-m-d H:i:s',strtotime($list->created_at))!!}</td>
                                            </tr>
                                            <?php $b++; ?>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


    </section>
   
@endsection
