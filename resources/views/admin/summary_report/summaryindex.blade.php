
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

                        <div class="box-header with-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="box-title">EOD Summary Report</h3>
                                {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                                    << Back to Sales and Receivables Reports </a> --}}
                            </div>
                        </div>

                        <div class="box-header with-border no-padding-h-b">
                           
                            @include('message')
                            <div class="col-md-12 no-padding-h table-responsive">
                                <form action="{{route('summary_report.summaryreport')}}" method="GET">                                  
                                    <div class="row">                                            
                                        <div class="col-md-3 form-group">
                                            <label for="">Choose From Date</label>
                                            <input type="date" name="date" id="date" class="form-control">
                                        </div>
                                        <div class="col-md-3 form-group">
                                            <label for="">Choose To Date</label>
                                            <input type="date" name="todate" id="todate" class="form-control">
                                        </div>
                                        <div class="col-md-3 ">
                                            <br>
                                            <button type="submit" class="btn btn-secondary">Download Report</button>
                                            <button type="button" class="btn btn-danger" onclick="printgrn();return false;">Print Report</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                           
                        </div>
                    </div>


    </section>
    
@endsection
@section('uniquepagescript')
<script type="text/javascript">

       function printgrn()
       {
            jQuery.ajax({
                url: '{{route('summary_report.summaryreport')}}',
                async:false,   //NOTE THIS
                 type: 'GET',
                data:{date:$('#date').val(),'todate':$('#todate').val(),'request_type':'print'},
              success: function (response) {
               
                var divContents = response;
                //alert(divContents);
                var printWindow = window.open('', '', 'width=600');
                printWindow.document.write(divContents);
                printWindow.document.close();
                printWindow.print();
                printWindow.close();
              }
            });
       }
   </script>
@endsection
