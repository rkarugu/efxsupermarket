@extends('layouts.admin.admin')

@push('styles')    
<style>
    .monthly-target {
        height: 400px;
        padding-inline: 10px;
        overflow-y: scroll;
    }
</style>
@endpush

@section('content')
    <section class="content">
        @include('message')

        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3>{{ $stats->suppliers_count }}</h3>
                        <p>Suppliers</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-fw fa-users"></i>
                    </div>
                    <a href="{{ route('suppliers-overview.suppliers-list') }}" target="_blank" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ number_format($stats->suppliers_total_balance) }}</h3>
                        <p>Total Supplier Balance</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money"></i>
                    </div>
                    <a href="javascript:void()" class="small-box-footer">In KES</a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3>{{ number_format($stats->branch_requisitions_count) }}</h3>
                        <p>Branch Requisitions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <a href="{{ route('suppliers-overview.branch-requisitions') }}" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3>{{ $stats->purchase_orders_count }}</h3>
                        <p>LPOs Without GRN</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-fw fa-hourglass"></i>
                    </div>
                    <a href="{{ route('suppliers-overview.lpos-without-grn') }}" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ $stats->demands_count }}</h3>
                        <p>Pending Demands</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-fw fa-tree"></i>
                    </div>
                    <a href="{{ route('suppliers-overview.unprocessed-demands') }}" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ number_format($stats->total_demands_amount) }}</h3>
                        <p>Total Demand Amount</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money"></i>
                    </div>
                    <a href="javascript:void()" class="small-box-footer">In KES</a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h3>{{ $stats->returns_count }}</h3>
                        <p>Pending Goods Returns</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-fw fa-building"></i>
                    </div>
                    <a href="{{ route('suppliers-overview.pending-good-returns') }}" target="_blank" class="small-box-footer">Go To <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3>{{ number_format($stats->total_returns_amount) }}</h3>
                        <p>Total Returns Amount</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money"></i>
                    </div>
                    <a href="#" class="small-box-footer">In KES</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Supplier Credit Limits</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered datatable">
                            <thead>
                                <tr>
                                    <th>Supplier Name</th>
                                    <th style="text-align: right">Credit Limit</th>
                                    <th style="text-align: right">Balance</th>
                                    <th style="text-align: right">Variance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($supplierCreditLimits as $i => $supplier)   
                                    <tr>
                                        <td>{{ $supplier->name }}</td>
                                        <td style="text-align: right">{{ number_format($supplier->credit_limit), 2 }}</td>
                                        <td style="text-align: right">{{ number_format($supplier->supp_trans_sum_total_amount_inc_vat, 2) }}</td>
                                        <td style="text-align: right">{{ number_format($supplier->variance, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="col-md-7">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="box-title">Departmental Peformance (Current Month)</h3>
                            <a href="{{ route('suppliers-overview.departmental-performance') }}" target="_blank" style="text-decoration: underline">
                                Current Year
                                <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div id="department-performance-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Supplier Monthy Targets</h3>
                    </div>
                    <div class="box-body">
                        <div class="monthly-target">
                            @foreach ($supplierTargets as $supplier)
                                <div class="progress-group">
                                    <span class="progress-text">{{ $supplier->name }}</span>
                                    <span class="progress-number"><b>{{ number_format($supplier->monthly_sales) }}</b>/{{ number_format($supplier->monthly_target) }}</span>
                                    <div class="progress sm">
                                        <div class="progress-bar progress-bar-green" style="width: {{ $supplier->percentage_target_achieved }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-7">
                <div class="box box-solid bg-green-gradient">
                    <div class="box-header ui-sortable-handle" style="cursor: move;">
                    <i class="fa fa-calendar"></i>
                    <h3 class="box-title">Delivery Schedule</h3>
                    
                    <div class="pull-right box-tools">
                    
                    <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-bars"></i></button>
                    <ul class="dropdown-menu pull-right" role="menu">
                    <li><a href="#">Add new event</a></li>
                    <li><a href="#">Clear events</a></li>
                    <li class="divider"></li>
                    <li><a href="#">View calendar</a></li>
                    </ul>
                    </div>
                    <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                    </div>
                    
                    </div>
                    
                    <div class="box-body no-padding">
                    
                    <div id="calendar" style="width: 100%"><div class="datepicker datepicker-inline"><div class="datepicker-days" style=""><table class="table-condensed"><thead><tr><th colspan="7" class="datepicker-title" style="display: none;"></th></tr><tr><th class="prev">«</th><th colspan="5" class="datepicker-switch">May 2024</th><th class="next">»</th></tr><tr><th class="dow">Su</th><th class="dow">Mo</th><th class="dow">Tu</th><th class="dow">We</th><th class="dow">Th</th><th class="dow">Fr</th><th class="dow">Sa</th></tr></thead><tbody><tr><td class="old day" data-date="1714262400000">28</td><td class="old day" data-date="1714348800000">29</td><td class="old day" data-date="1714435200000">30</td><td class="day" data-date="1714521600000">1</td><td class="day" data-date="1714608000000">2</td><td class="day" data-date="1714694400000">3</td><td class="day" data-date="1714780800000">4</td></tr><tr><td class="day" data-date="1714867200000">5</td><td class="day" data-date="1714953600000">6</td><td class="day" data-date="1715040000000">7</td><td class="day" data-date="1715126400000">8</td><td class="day" data-date="1715212800000">9</td><td class="day" data-date="1715299200000">10</td><td class="day" data-date="1715385600000">11</td></tr><tr><td class="day" data-date="1715472000000">12</td><td class="day" data-date="1715558400000">13</td><td class="day" data-date="1715644800000">14</td><td class="day" data-date="1715731200000">15</td><td class="day" data-date="1715817600000">16</td><td class="day" data-date="1715904000000">17</td><td class="day" data-date="1715990400000">18</td></tr><tr><td class="day" data-date="1716076800000">19</td><td class="day" data-date="1716163200000">20</td><td class="day" data-date="1716249600000">21</td><td class="day" data-date="1716336000000">22</td><td class="day" data-date="1716422400000">23</td><td class="day" data-date="1716508800000">24</td><td class="day" data-date="1716595200000">25</td></tr><tr><td class="day" data-date="1716681600000">26</td><td class="day" data-date="1716768000000">27</td><td class="day" data-date="1716854400000">28</td><td class="day" data-date="1716940800000">29</td><td class="day" data-date="1717027200000">30</td><td class="day" data-date="1717113600000">31</td><td class="new day" data-date="1717200000000">1</td></tr><tr><td class="new day" data-date="1717286400000">2</td><td class="new day" data-date="1717372800000">3</td><td class="new day" data-date="1717459200000">4</td><td class="new day" data-date="1717545600000">5</td><td class="new day" data-date="1717632000000">6</td><td class="new day" data-date="1717718400000">7</td><td class="new day" data-date="1717804800000">8</td></tr></tbody><tfoot><tr><th colspan="7" class="today" style="display: none;">Today</th></tr><tr><th colspan="7" class="clear" style="display: none;">Clear</th></tr></tfoot></table></div><div class="datepicker-months" style="display: none;"><table class="table-condensed"><thead><tr><th colspan="7" class="datepicker-title" style="display: none;"></th></tr><tr><th class="prev">«</th><th colspan="5" class="datepicker-switch">2024</th><th class="next">»</th></tr></thead><tbody><tr><td colspan="7"><span class="month">Jan</span><span class="month">Feb</span><span class="month">Mar</span><span class="month">Apr</span><span class="month focused">May</span><span class="month">Jun</span><span class="month">Jul</span><span class="month">Aug</span><span class="month">Sep</span><span class="month">Oct</span><span class="month">Nov</span><span class="month">Dec</span></td></tr></tbody><tfoot><tr><th colspan="7" class="today" style="display: none;">Today</th></tr><tr><th colspan="7" class="clear" style="display: none;">Clear</th></tr></tfoot></table></div><div class="datepicker-years" style="display: none;"><table class="table-condensed"><thead><tr><th colspan="7" class="datepicker-title" style="display: none;"></th></tr><tr><th class="prev">«</th><th colspan="5" class="datepicker-switch">2020-2029</th><th class="next">»</th></tr></thead><tbody><tr><td colspan="7"><span class="year old">2019</span><span class="year">2020</span><span class="year">2021</span><span class="year">2022</span><span class="year">2023</span><span class="year focused">2024</span><span class="year">2025</span><span class="year">2026</span><span class="year">2027</span><span class="year">2028</span><span class="year">2029</span><span class="year new">2030</span></td></tr></tbody><tfoot><tr><th colspan="7" class="today" style="display: none;">Today</th></tr><tr><th colspan="7" class="clear" style="display: none;">Clear</th></tr></tfoot></table></div><div class="datepicker-decades" style="display: none;"><table class="table-condensed"><thead><tr><th colspan="7" class="datepicker-title" style="display: none;"></th></tr><tr><th class="prev">«</th><th colspan="5" class="datepicker-switch">2000-2090</th><th class="next">»</th></tr></thead><tbody><tr><td colspan="7"><span class="decade old">1990</span><span class="decade">2000</span><span class="decade">2010</span><span class="decade focused">2020</span><span class="decade">2030</span><span class="decade">2040</span><span class="decade">2050</span><span class="decade">2060</span><span class="decade">2070</span><span class="decade">2080</span><span class="decade">2090</span><span class="decade new">2100</span></td></tr></tbody><tfoot><tr><th colspan="7" class="today" style="display: none;">Today</th></tr><tr><th colspan="7" class="clear" style="display: none;">Clear</th></tr></tfoot></table></div><div class="datepicker-centuries" style="display: none;"><table class="table-condensed"><thead><tr><th colspan="7" class="datepicker-title" style="display: none;"></th></tr><tr><th class="prev">«</th><th colspan="5" class="datepicker-switch">2000-2900</th><th class="next">»</th></tr></thead><tbody><tr><td colspan="7"><span class="century old">1900</span><span class="century focused">2000</span><span class="century">2100</span><span class="century">2200</span><span class="century">2300</span><span class="century">2400</span><span class="century">2500</span><span class="century">2600</span><span class="century">2700</span><span class="century">2800</span><span class="century">2900</span><span class="century new">3000</span></td></tr></tbody><tfoot><tr><th colspan="7" class="today" style="display: none;">Today</th></tr><tr><th colspan="7" class="clear" style="display: none;">Clear</th></tr></tfoot></table></div></div></div>
                    </div>
                    
                    <div class="box-footer text-black">
                    <div class="row">
                    <div class="col-sm-6">
                    
                    <div class="clearfix">
                    <span class="pull-left">Task #1</span>
                    <small class="pull-right">90%</small>
                    </div>
                    <div class="progress xs">
                    <div class="progress-bar progress-bar-green" style="width: 90%;"></div>
                    </div>
                    <div class="clearfix">
                    <span class="pull-left">Task #2</span>
                    <small class="pull-right">70%</small>
                    </div>
                    <div class="progress xs">
                    <div class="progress-bar progress-bar-green" style="width: 70%;"></div>
                    </div>
                    </div>
                    
                    <div class="col-sm-6">
                    <div class="clearfix">
                    <span class="pull-left">Task #3</span>
                    <small class="pull-right">60%</small>
                    </div>
                    <div class="progress xs">
                    <div class="progress-bar progress-bar-green" style="width: 60%;"></div>
                    </div>
                    <div class="clearfix">
                    <span class="pull-left">Task #4</span>
                    <small class="pull-right">40%</small>
                    </div>
                    <div class="progress xs">
                    <div class="progress-bar progress-bar-green" style="width: 40%;"></div>
                    </div>
                    </div>
                    
                    </div>
                    
                    </div>
                    </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="box box-warning direct-chat direct-chat-warning">
                    <div class="box-header with-border">
                    <h3 class="box-title">Direct Chat</h3>
                    <div class="box-tools pull-right">
                    <span data-toggle="tooltip" title="" class="badge bg-yellow" data-original-title="3 New Messages">3</span>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-toggle="tooltip" title="" data-widget="chat-pane-toggle" data-original-title="Contacts">
                    <i class="fa fa-comments"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                    </div>
                    </div>
                    
                    <div class="box-body">
                    
                    <div class="direct-chat-messages">
                    
                    <div class="direct-chat-msg">
                    <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Alexander Pierce</span>
                    <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>
                    </div>
                    
                    <img class="direct-chat-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user1-128x128.jpg" alt="message user image">
                    
                    <div class="direct-chat-text">
                    Is this template really for free? That's unbelievable!
                    </div>
                    
                    </div>
                    
                    
                    <div class="direct-chat-msg right">
                    <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-right">Sarah Bullock</span>
                    <span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>
                    </div>
                    
                    <img class="direct-chat-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user3-128x128.jpg" alt="message user image">
                    
                    <div class="direct-chat-text">
                    You better believe it!
                    </div>
                    
                    </div>
                    
                    
                    <div class="direct-chat-msg">
                    <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-left">Alexander Pierce</span>
                    <span class="direct-chat-timestamp pull-right">23 Jan 5:37 pm</span>
                    </div>
                    
                    <img class="direct-chat-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user1-128x128.jpg" alt="message user image">
                    
                    <div class="direct-chat-text">
                    Working with AdminLTE on a great new app! Wanna join?
                    </div>
                    
                    </div>
                    
                    
                    <div class="direct-chat-msg right">
                    <div class="direct-chat-info clearfix">
                    <span class="direct-chat-name pull-right">Sarah Bullock</span>
                    <span class="direct-chat-timestamp pull-left">23 Jan 6:10 pm</span>
                    </div>
                    
                    <img class="direct-chat-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user3-128x128.jpg" alt="message user image">
                    
                    <div class="direct-chat-text">
                    I would love to.
                    </div>
                    
                    </div>
                    
                    </div>
                    
                    
                    <div class="direct-chat-contacts">
                    <ul class="contacts-list">
                    <li>
                    <a href="#">
                    <img class="contacts-list-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user1-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info">
                    <span class="contacts-list-name">
                    Count Dracula
                    <small class="contacts-list-date pull-right">2/28/2015</small>
                    </span>
                    <span class="contacts-list-msg">How have you been? I was...</span>
                    </div>
                    
                    </a>
                    </li>
                    
                    <li>
                    <a href="#">
                    <img class="contacts-list-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user7-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info">
                    <span class="contacts-list-name">
                    Sarah Doe
                    <small class="contacts-list-date pull-right">2/23/2015</small>
                    </span>
                    <span class="contacts-list-msg">I will be waiting for...</span>
                    </div>
                    
                    </a>
                    </li>
                    
                    <li>
                    <a href="#">
                    <img class="contacts-list-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user3-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info">
                    <span class="contacts-list-name">
                    Nadia Jolie
                    <small class="contacts-list-date pull-right">2/20/2015</small>
                    </span>
                    <span class="contacts-list-msg">I'll call you back at...</span>
                    </div>
                    
                    </a>
                    </li>
                    
                    <li>
                    <a href="#">
                    <img class="contacts-list-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user5-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info">
                    <span class="contacts-list-name">
                    Nora S. Vans
                    <small class="contacts-list-date pull-right">2/10/2015</small>
                    </span>
                    <span class="contacts-list-msg">Where is your new...</span>
                    </div>
                    
                    </a>
                    </li>
                    
                    <li>
                    <a href="#">
                    <img class="contacts-list-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user6-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info">
                    <span class="contacts-list-name">
                    John K.
                    <small class="contacts-list-date pull-right">1/27/2015</small>
                    </span>
                    <span class="contacts-list-msg">Can I take a look at...</span>
                    </div>
                    
                    </a>
                    </li>
                    
                    <li>
                    <a href="#">
                    <img class="contacts-list-img" src="https://adminlte.io/themes/v2.4.x/dist/img/user8-128x128.jpg" alt="User Image">
                    <div class="contacts-list-info">
                    <span class="contacts-list-name">
                    Kenneth M.
                    <small class="contacts-list-date pull-right">1/4/2015</small>
                    </span>
                    <span class="contacts-list-msg">Never mind I found...</span>
                    </div>
                    
                    </a>
                    </li>
                    
                    </ul>
                    
                    </div>
                    
                    </div>
                    
                    <div class="box-footer">
                    <form action="#" method="post">
                    <div class="input-group">
                    <input type="text" name="message" placeholder="Type Message ..." class="form-control">
                    <span class="input-group-btn">
                    <button type="button" class="btn btn-warning btn-flat">Send</button>
                    </span>
                    </div>
                    </form>
                    </div>
                    
                    </div>
            </div>

            <div class="col-md-7">
                <div class="box box-danger">
                    <div class="box-header with-border">
                    <h3 class="box-title">Latest Members</h3>
                    <div class="box-tools pull-right">
                    <span class="label label-danger">8 New Members</span>
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                    </div>
                    </div>
                    
                    <div class="box-body no-padding">
                    <ul class="users-list clearfix">
                    <li>
                    <img src="https://adminlte.io/themes/v2.4.x/dist/img/user1-128x128.jpg" alt="User Image">
                    <a class="users-list-name" href="#">Alexander Pierce</a>
                    <span class="users-list-date">Today</span>
                    </li>
                    <li>
                    <img src="https://adminlte.io/themes/v2.4.x/dist/img/user8-128x128.jpg" alt="User Image">
                    <a class="users-list-name" href="#">Norman</a>
                    <span class="users-list-date">Yesterday</span>
                    </li>
                    <li>
                    <img src="https://adminlte.io/themes/v2.4.x/dist/img/user7-128x128.jpg" alt="User Image">
                    <a class="users-list-name" href="#">Jane</a>
                    <span class="users-list-date">12 Jan</span>
                    </li>
                    <li>
                    <img src="https://adminlte.io/themes/v2.4.x/dist/img/user6-128x128.jpg" alt="User Image">
                    <a class="users-list-name" href="#">John</a>
                    <span class="users-list-date">12 Jan</span>
                    </li>
                    <li>
                    <img src="https://adminlte.io/themes/v2.4.x/dist/img/user2-160x160.jpg" alt="User Image">
                    <a class="users-list-name" href="#">Alexander</a>
                    <span class="users-list-date">13 Jan</span>
                    </li>
                    <li>
                    <img src="https://adminlte.io/themes/v2.4.x/dist/img/user5-128x128.jpg" alt="User Image">
                    <a class="users-list-name" href="#">Sarah</a>
                    <span class="users-list-date">14 Jan</span>
                    </li>
                    <li>
                    <img src="https://adminlte.io/themes/v2.4.x/dist/img/user4-128x128.jpg" alt="User Image">
                    <a class="users-list-name" href="#">Nora</a>
                    <span class="users-list-date">15 Jan</span>
                    </li>
                    <li>
                    <img src="https://adminlte.io/themes/v2.4.x/dist/img/user3-128x128.jpg" alt="User Image">
                    <a class="users-list-name" href="#">Nadia</a>
                    <span class="users-list-date">15 Jan</span>
                    </li>
                    </ul>
                    
                    </div>
                    
                    <div class="box-footer text-center">
                    <a href="javascript:void(0)" class="uppercase">View All Users</a>
                    </div>
                    
                    </div>
            </div>
        </div>

    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/utils.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        $("body").addClass('sidebar-collapse');

        $('.datatable').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'info': true,
            'autoWidth': false,
            'pageLength': 10,
            'initComplete': function (settings, json) {
                let info = this.api().page.info();
                let total_record = info.recordsTotal;
                if (total_record < 11) {
                    $('.dataTables_paginate').hide();
                }
            },
            'aoColumnDefs': [{
                'bSortable': false,
                'aTargets': 'noneedtoshort'
            }],
        });

        const displayCategoryData = (departmentPerformanceCategory) => {
            let url = new URL('{!! route('suppliers-overview.suppliers-sales-by-category') !!}');
        
            url.searchParams.set("category", departmentPerformanceCategory);
            url.searchParams.set("startDate", '');
            url.searchParams.set("endDate", '');
        
            window.open(url.toString(), '_blank');
        }

        // DEPARTMENTAL PERFORMANCE CHART
        const departmentPerformanceChartData = {!! $departmentPerformanceChartData !!}

        const departmentPerformanceCategories = Object.keys(departmentPerformanceChartData)
        const departmentPerformanceSeries = []
        for (let key in departmentPerformanceChartData) {
            departmentPerformanceSeries.push(departmentPerformanceChartData[key])
        }

        var departmentPerformanceOptions = {
            series: [{
                data: departmentPerformanceSeries,
                name: 'Sales'
            }],
            chart: {
                type: 'bar',
                height: 'auto',
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        displayCategoryData(departmentPerformanceCategories[config.dataPointIndex]);
                    }
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: departmentPerformanceCategories,
                labels: {
                    formatter: function (val) {
                        return numberWithCommas(val)
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return `KES ${numberWithCommas(val)}`
                    },
                },
            },
        };

        const departmentPerformanceChart = new ApexCharts(document.querySelector("#department-performance-chart"), departmentPerformanceOptions);
        departmentPerformanceChart.render();
    </script>

@endpush
