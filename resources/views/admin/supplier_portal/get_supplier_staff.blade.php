@extends('layouts.admin.admin')

@section('content')
    <section class="content">
       

        <script>

        </script>

        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                    <div class="d-flex flex-column">
                        <h3 class="box-title"> Supplier - {{$supplier->name}} ({{$supplier->supplier_code}})</h3>
                    </div>

                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Staff Name</th>
                            <th 
                                >
                                Email</th>
                            <th >Id Number</th>
                            <th >Phone Number</th>
                            <th >Role</th>
                            <th > Created At</th>
                            <th > Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    <button class="btn btn-danger" onclick="load_data()">Load More</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </section>

    
    <!-- Modal -->
    <div class="modal fade" id="edit_staff" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <form action="{{route('supplier-portal.update_supplier_staff',$supplier->id)}}" class="submitMe" method="post">
            @csrf
            <input type="hidden" name="staff_id" id="staff_id" value="">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Staff Details</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Name *</label>
                            <input type="text" name="staff_full_name" id="staff_name" class="form-control" placeholder="Enter Staff Name" >
                        </div>
                        <div class="form-group">
                            <label for="">Email *</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email">
                        </div>
                        <div class="form-group">
                            <label for="">ID number</label>
                            <input type="text" name="id_number" id="id_number" class="form-control" placeholder="Enter ID number">
                        </div>
                        <div class="form-group">
                            <label for="">Phone Number *</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control" placeholder="Enter phone number">
                        </div>

                        <div class="form-group">
                            <label for="">Password</label>
                            <input type="text" name="password" id="password" class="form-control" placeholder="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('uniquepagescript')
<style type="text/css">
        

        


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

<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{ asset('js/form.js') }}"></script>
    <script type="text/javascript">
        function load_data(){
            $.ajax({
                type: "GET",
                url: "{{route('supplier-portal.get_supplier_staff',$supplier->id)}}",
                data: {
                    offset:current_page
                },
                success: function (response) {
                    current_page = current_page + 100;
                    console.log(current_page);
                    $.each(response, function (indexInArray, valueOfElement) { 
                        let child = `<tr>
                            <th style="width: 3%;" scope="row">${indexInArray + 1}</th>
                            <td>${ valueOfElement['name'] }</td>
                            <td>${ valueOfElement['email'] }</td>
                            <td>${ valueOfElement['id_number'] ?? '' }</td>
                            <td>${ valueOfElement['phone_number'] ?? '' }</td>
                            <td>${ valueOfElement['role']['name'] }</td>
                            <td>${ valueOfElement['created_at'] }</td>
                            <td>
                                <a href="#" onclick="openEditModal(${valueOfElement['id']},'${valueOfElement['name']}','${valueOfElement['email']}','${valueOfElement['id_number']}','${valueOfElement['phone_number']}'); return false;">
                                    <i class="fa fa-pen"></i></a>
                            </td>
                        </tr>`;
                        $('.table tbody').append(child)
                    });
                }
            });
        }
        $(document).ready(function () {
            current_page = 0;
            load_data()
        });

        function openEditModal(id,name,email,id_number,phone){
            $('#edit_staff #staff_id').val(id);
            $('#edit_staff #staff_name').val(name);
            $('#edit_staff #email').val(email);
            $('#edit_staff #id_number').val(id_number);
            $('#edit_staff #phone_number').val(phone);
            $('#edit_staff').modal('show');
        }
       
    </script>


@endsection
