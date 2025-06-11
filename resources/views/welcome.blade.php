<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @if (Auth::check())
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ url('/login') }}">Login</a>
                        <a href="{{ url('/register') }}">Register</a>
                    @endif
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    Laravel
                </div>

                <div class="links">
                    <a href="https://laravel.com/docs">Documentation</a>
                    <a href="https://laracasts.com">Laracasts</a>
                    <a href="https://laravel-news.com">News</a>
                    <a href="https://forge.laravel.com">Forge</a>
                    <a href="https://github.com/laravel/laravel">GitHub</a>
                </div>
            </div>
        </div>
    </body>
</html>



$data = DB::table('routes')
->select(
    'routes.route_name as route',
    'users.id as salesman_id',
    'users.name as salesman',
    'routes.tonnage_target',
    DB::raw("(SELECT SUM(wa_internal_requisition_items.total_cost_with_vat)
            FROM wa_internal_requisition_items LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
            WHERE  wa_internal_requisitions.route_id = routes.id
            AND (DATE(wa_internal_requisition_items.created_at) BETWEEN '$start' AND '$end')
        ) AS sales"),
    DB::raw("(SELECT COUNT(DISTINCT DATE(salesman_shifts.created_at)) 
            FROM salesman_shifts WHERE salesman_shifts.route_id = routes.id AND salesman_shifts.status IN ('open', 'close', 'not_started')
            AND (DATE(salesman_shifts.created_at) BETWEEN '$start' AND '$end')
        ) AS actual_frequency"),
    DB::raw("(SELECT COUNT(DISTINCT DATE(salesman_shifts.created_at)) 
            FROM salesman_shifts WHERE salesman_shifts.route_id = routes.id AND salesman_shifts.status IN ('open', 'close')
            AND (DATE(salesman_shifts.created_at) BETWEEN '$start' AND '$end')
        ) AS days_with_actual_shifts"),

    DB::raw("(
     (SELECT SUM((wa_internal_requisition_items.quantity * wa_inventory_items.net_weight)/1000) 
            FROM wa_internal_requisition_items LEFT JOIN wa_internal_requisitions ON wa_internal_requisitions.id = wa_internal_requisition_items.wa_internal_requisition_id
            LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_internal_requisition_items.wa_inventory_item_id
            WHERE wa_internal_requisitions.route_id = routes.id
            AND (DATE(wa_internal_requisitions.created_at) BETWEEN '$start' AND '$end')
        )
        - (SELECT SUM(COALESCE(wa_inventory_items.net_weight * wa_inventory_location_transfer_item_returns.received_quantity, 0) / 1000)
      FROM wa_inventory_location_transfer_item_returns
      LEFT JOIN wa_inventory_location_transfer_items ON wa_inventory_location_transfer_items.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_item_id
      LEFT JOIN wa_inventory_location_transfers ON wa_inventory_location_transfers.id = wa_inventory_location_transfer_item_returns.wa_inventory_location_transfer_id
      LEFT JOIN wa_inventory_items ON wa_inventory_items.id = wa_inventory_location_transfer_items.wa_inventory_item_id
      WHERE wa_inventory_location_transfers.route_id = routes.id
      AND wa_inventory_location_transfer_item_returns.status = 'received'
      AND wa_inventory_location_transfer_item_returns.return_status = '1'
      AND (DATE(wa_inventory_location_transfer_item_returns.updated_at) BETWEEN '$start' AND '$end')
     )
        ) AS achieved_tonnage"),

)->leftJoin('route_user', 'route_user.route_id', 'routes.id')
->leftJoin('users', 'users.id', 'route_user.user_id')
//            ->whereIn('routes.id',$uniqueRouteIds)
//            ->where('routes.is_physical_route', 1)
->groupBy('routes.id')
->get();
