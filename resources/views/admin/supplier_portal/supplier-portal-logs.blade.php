@extends('layouts.admin.admin')

@section('content')
    <section class="content">
       

        <script>

        </script>

        <div class="box box-primary">
            <div class="box-header">
                <div class="box-header-flex">
                    <div class="d-flex flex-column">
                        <h3 class="box-title"> Supplier User Logs </h3>
                    </div>

                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <form action="" method="get" onsubmit="loadMoreData(); return false;">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">From Date</label>
                              <input type="date" name="from_date" value="{{date('Y-m-d')}}" id="from_date" class="form-control" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <label for="">To Date</label>
                              <input type="date" name="to_date" value="{{date('Y-m-d')}}" id="to_date" class="form-control" >
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                              <br>
                              <button type="submit" class="btn btn-danger">Filter</button>
                              <button id="exportButton" class="btn btn-danger" type="button">Export to Excel</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table" id="myTable">
                        <thead>
                        <tr>
                            <th >Date</th>
                            <th>Supplier User</th>
                            <th>Supplier</th>
                            <th>Role</th>
                            <th>User Ip</th>
                            <th >User Agent</th>
                            <th >Activity</th>
                        </tr>
                        </thead>

                        <tbody>
                        
                        </tbody>
                        
                    </table>
                    <button class="btn btn-danger" onclick="load_data()">Load More</button>
                </div>
            </div>
        </div>

    </section>
@endsection
@section('uniquepagescript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script type="text/javascript">
        var current_page = 0;
        const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        async function loadMoreData(){
            current_page = 0
            $('.table tbody').html('');
            await load_data();
        }
        function load_data(){
            return $.ajax({
                type: "GET",
                url: "{{route('supplier-portal.supplier-portal-logs')}}",
                data: {
                    offset: current_page,
                    from_date: function() {
                        return $('#from_date').val();
                    },
                    to_date: function() {
                        return $('#to_date').val();
                    }
                },
                success: function (response) {
                    current_page = current_page + 100;
                    console.log(current_page);
                    $.each(response, function (indexInArray, valueOfElement) { 
                        let date = new Date(valueOfElement['created_at']);
                        let child = `<tr>
                            <td>${ date.toLocaleDateString('en-US', options) }</td>
                            <td>${ valueOfElement['user_name'] }</td>
                            <td>${ valueOfElement['user']['supplier']['business_name'] }</td>
                            <td>${ valueOfElement['user']['role']['name'] }</td>
                            <td>${ valueOfElement['user_ip'] }</td>
                            <td>${ valueOfElement['user_agent'] }</td>
                            <td>${ valueOfElement['activity'] }</td>
                        </tr>`;
                        $('.table tbody').append(child)
                    });
                }
            });

        }
        $(document).ready(function () {
            $('#exportButton').click(async function(){
                await loadMoreData();
                let table = document.getElementById('myTable');
                let workbook = XLSX.utils.table_to_book(table, {sheet: "Sheet1"});
                XLSX.writeFile(workbook, 'supplier-portal-logs.xlsx');
            });
            load_data()
        });
       
    </script>


@endsection
