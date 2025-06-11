@extends('layouts.admin.admin')
@section('content')
    <?php
    $logged_user_info_dash = getLoggeduserProfile();
    $my_permissions_dash = $logged_user_info_dash->permission;
    $route_name_dash = \Route::currentRouteName();

    ?>




    <section class="content">
        <div class="session-message-container">
            @include('message')
        </div>

        <div class="d-flex">
            <label class="switch">
                <input type="checkbox" onchange="showDash(this)" @if(isset(request()->show_dashboard)) checked @endif>
                <span class="slider round"></span>
            </label>
            <b>
                Show Dashboard
            </b>
        </div>
        <br>
        @if($logged_user_info_dash->role_id == 1 || isset($my_permissions_dash['dashboard___view']))
            @if(isset(request()->show_dashboard))
                <div class="part_1_dashboard">
                    <!-- Modal -->
                    <div class="modal fade" id="salesperson_report" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                        <form action="{{route('dashboard.salesperson_report')}}" method="GET">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Sales Person Report</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="">Choose From Date</label>
                                            <input type="date" name="date" id="salespersondate" class="form-control">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="">Choose To Date</label>
                                            <input type="date" name="todate" id="salespersontodate" class="form-control">
                                        </div>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Download Report</button>
                                    <button type="button" class="btn btn-danger" onclick="printgrn();return false;">Print Report</button>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="selling_report" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <form action="{{route('dashboard.selling_report')}}" method="GET">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Selling Report</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="">Choose From Date</label>
                                            <input type="date" name="date" id="selling_reportdate" class="form-control">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="">Choose To Date</label>
                                            <input type="date" name="todate" id="selling_reporttodate" class="form-control">
                                        </div>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Download Report</button>
                                    {{-- <button type="button" class="btn btn-danger" onclick="printgrn_selling_report();return false;">Print Report</button> --}}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box box-primary">
                    <div class="row dashboard_row_class">
                        <div class="col-lg-3 col-xs-6">

                            <div class="small-box bg-blue">
                                <div class="inner">
                                    <h3 style="font-size: 14px;">KES {!! $earningStats['this_week'] ?? '00' !!}</h3>
                                    <p>Total Sale</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="javascript:;" class="small-box-footer">This Week</a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">

                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3 style="font-size: 14px;">KES {!! $earningStats['last_week']?? '00' !!}</h3>
                                    <p>Total Sale</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="javascript:;" class="small-box-footer">Last Week</a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">

                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3 style="font-size: 14px;">KES {!! $earningStats['this_month'] ?? '00' !!}</h3>
                                    <p>Total Sale</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="javascript:;" class="small-box-footer">This Month</a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">

                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3 style="font-size: 14px;">KES {!! $earningStats['last_month'] ?? '00' !!}</h3>
                                    <p>Total Sale</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="javascript:;" class="small-box-footer">Last Month</a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">

                            <div class="small-box bg-orange">
                                <div class="inner">
                                    <h3 style="font-size: 14px;">KES {!! $earningStats['till_date'] ?? '00' !!}</h3>
                                    <p>Total Sale</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="javascript:;" class="small-box-footer">Year To Date</a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">

                            <div class="small-box bg-purple">
                                <div class="inner">
                                    <h3 style="font-size: 14px;">{!! $earningStats['last_year'] ?? '00' !!}</h3>
                                    <p>Last Year</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="javascript:;" class="small-box-footer">Last Year</a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">

                            <div class="small-box bg-yellow" onclick="$('#salesperson_report').modal('show');" style="cursor: pointer">
                                <div class="inner">
                                    @if($highestsellingsalesman)

                                        @foreach($highestsellingsalesman as $val)
                                            <p>{!! @$val->getrelatedEmployee->name !!} - {{ manageAmountFormat($val->totalamount) }}</p>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="#" onclick="return false" class="small-box-footer">Highest Selling Salesman</a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6">

                            <div class="small-box bg-orange" onclick="$('#selling_report').modal('show');" style="cursor: pointer">
                                <div class="inner">
                                    @if($highestsellingproducts)
                                        @foreach($highestsellingproducts as $val)
                                            <p>{{ $val->item_name }}</p>
                                        @endforeach
                                    @endif

                                </div>
                                <div class="icon">
                                    <i class="fa fa-bar-chart"></i>
                                </div>
                                <a href="#" onclick="return false" class="small-box-footer">Highest Selling products</a>
                            </div>
                        </div>


                    </div>


                </div>

                {!! Form::open(['route' => 'admin.dashboard','method'=>'get']) !!}

                <div class="row">


                    <div class="col-md-12">
                        <!-- Bar chart -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                    <?php
                                    $months = ['01' => 'Jan', '02' => 'Feb', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'Sept', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
                                    $years = [];

                                    for ($y = date('Y'); $y >= '2017'; $y--) {
                                        $years[$y] = $y;
                                    }


                                    ?>

                                <div class="col-lg-3 col-xs-6"><i class="fa fa-bar-chart"></i>
                                    <h3 class="box-title">Sales Transactions </h3></div>
                                <div class="col-lg-3 col-xs-6">

                                    {!!Form::select('sale_transaction_month', $months, date('m'), [ 'class' => 'form-control'  ])!!}

                                </div>
                                <div class="col-lg-3 col-xs-6">
                                    {!!Form::select('sale_transaction_year', $years, date('Y'), [ 'class' => 'form-control'  ])!!}
                                </div>
                                <div class="col-lg-3 col-xs-6">
                                    <button type="submit" class="btn btn-success">Get</button>
                                </div>


                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>

                                </div>
                            </div>
                            <div class="box-body">
                                <div id="sales-chart" style="height: 300px;width:100%"></div>
                            </div>
                            <!-- /.box-body-->
                        </div>
                        <!-- /.box -->

                        <!-- Donut chart -->

                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                <div class="row">


                    <div class="col-md-6">
                        <!-- Bar chart -->
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <i class="fa fa-bar-chart"></i>

                                <h3 class="box-title">User Registartion chart From {!! $end_date !!} To Today</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>

                                </div>
                            </div>
                            <div class="box-body">
                                <div id="bar-chart" style="height: 300px;width:100%"></div>
                            </div>
                            <!-- /.box-body-->
                        </div>
                        <!-- /.box -->

                        <!-- Donut chart -->

                        <!-- /.box -->
                    </div>
                    <!-- /.col -->
                </div>
                </form>

                </div>
            @else
                <div class="part_2_show">
                    <div class="box box-primary">
                        <div class="row ">
                            <div class="col-md-12">
                                <img src="{{ asset('assets/admin/images/dashboard.jpg') }}" height="100%" width="100%" alt=""/>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="box box-primary">
                <div class="row ">
                    <div class="col-md-12">
                        <img src="{{ asset('assets/admin/images/dashboard.jpg') }}" height="100%" width="100%" alt=""/>
                    </div>
                </div>
            </div>

        @endif

    </section>






    <style type="text/css">
        .dashboard_row_class {
            margin-top: 10px;
        }

        .card-columns {
            -moz-column-count: 2;
            -webkit-column-count: 2;
        }
    </style>

@endsection

@section('uniquepagescriptforchart')
    <script src="{{asset('assets/admin/Flot/jquery.flot.js')}}"></script>
    <script src="{{asset('assets/admin/Flot/jquery.flot.resize.js')}}"></script>
    <script src="{{asset('assets/admin/Flot/jquery.flot.pie.js')}}"></script>
    <script src="{{asset('assets/admin/Flot/jquery.flot.categories.js')}}"></script>

    <script type="text/javascript">
        /*
        * BAR CHART
        * ---------
        */

        var bar_data = {
            data: JSON.parse('{!! json_encode($getUsersRegistrStats) !!}'),
            color: '#3c8dbc'
        }
        $.plot('#bar-chart', [bar_data], {
            grid: {
                borderWidth: 1,
                borderColor: '#f3f3f3',
                tickColor: '#f3f3f3'
            },
            series: {
                bars: {
                    show: true,
                    barWidth: 0.5,
                    align: 'center'
                }
            },
            xaxis: {
                mode: 'categories',
                tickLength: 0
            }
        })
        /* END BAR CHART */
    </script>

    <script type="text/javascript">
        /*
        * BAR CHART
        * ---------
        */

        var bar_data = {
            data: JSON.parse('{!! json_encode($sales_transaction_stats) !!}'),
            color: '#3c8dbc'
        }
        $.plot('#sales-chart', [bar_data], {
            grid: {
                borderWidth: 1,
                borderColor: '#f3f3f3',
                tickColor: '#f3f3f3'
            },
            series: {
                bars: {
                    show: true,
                    barWidth: 0.5,
                    align: 'center'
                }
            },
            xaxis: {
                mode: 'categories',
                tickLength: 0
            },


            grid: {
                hoverable: true,

            }
        })

        $(document).ready(function () {

            $("#sales-chart").UseTooltip();
        });

        var previousPoint = null, previousLabel = null;

        $.fn.UseTooltip = function () {
            $(this).bind("plothover", function (event, pos, item) {
                if (item) {
                    if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                        previousPoint = item.dataIndex;
                        previousLabel = item.series.label;
                        $("#tooltip").remove();

                        var x = item.datapoint[0];
                        var y = item.datapoint[1];

                        var color = item.series.color;

                        //console.log(item.series.xaxis.ticks[x].label);                

                        showTooltip(item.pageX,
                            item.pageY,
                            color,
                            "<strong>Day </strong><br>" + item.series.xaxis.ticks[x].label + " : <strong> Amount: " + y + "</strong> ");
                    }
                } else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
            });
        };

        function showTooltip(x, y, color, contents) {
            $('<div id="tooltip">' + contents + '</div>').css({
                position: 'absolute',
                display: 'none',
                top: y - 40,
                left: x - 120,
                border: '2px solid ' + color,
                padding: '3px',
                'font-size': '9px',
                'border-radius': '5px',
                'background-color': '#fff',
                'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
                opacity: 0.9
            }).appendTo("body").fadeIn(200);
        }

        function showDash(inp) {
            if ($(inp).is(':checked')) {
                location.href = "{{route('admin.dashboard',['show_dashboard'=>1])}}";
            } else {
                location.href = "{{route('admin.dashboard')}}";
            }
        }

        /* END BAR CHART */
    </script>

    <script type="text/javascript">

        function printgrn() {
            jQuery.ajax({
                url: '{{route('dashboard.salesperson_report')}}',
                async: false,   //NOTE THIS
                type: 'GET',
                data: {date: $('#salespersondate').val(), 'todate': $('#salespersontodate').val(), 'request_type': 'print'},
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

        function printgrn_selling_report() {
            jQuery.ajax({
                url: '{{route('dashboard.selling_report')}}',
                async: false,   //NOTE THIS
                type: 'GET',
                data: {date: $('#selling_reportdate').val(), 'todate': $('#selling_reporttodate').val(), 'request_type': 'print'},
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