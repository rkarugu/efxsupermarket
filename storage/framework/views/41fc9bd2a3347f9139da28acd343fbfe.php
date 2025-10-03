<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $title ? $title : 'Admin'; ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="robots" content="noindex, nofollow" />
    
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/bower_components/bootstrap/dist/css/bootstrap.min.css')); ?>">
    
    <!-- Font Awesome -->
    <link href="<?php echo e(asset('assets/fontawesome/css/fontawesome.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/fontawesome/css/solid.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/fontawesome/css/brands.css')); ?>" rel="stylesheet" />

    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/bower_components/Ionicons/css/ionicons.min.css')); ?>">

    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')); ?>">
    
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/css/AdminLTE.min.css')); ?>">
    
    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/css/skins/_all-skins.min.css')); ?>">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/admin_custom.css')); ?>">

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        .action_crud span {
            float: left;
            padding-left: 3px;
        }

        .error {
            color: red;
        }

        .no-padding-h {
            margin-top: 10px;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td,
        .table>caption+thead>tr:first-child>td,
        .table>caption+thead>tr:first-child>th,
        .table>colgroup+thead>tr:first-child>td,
        .table>colgroup+thead>tr:first-child>th,
        .table>thead:first-child>tr:first-child>td,
        .table>thead:first-child>tr:first-child>th {
            border: 1px solid #e1e1e1;
        }

        .insertimageicon {
            display: none;
        }

        textarea {
            resize: none;
        }

        input[type="file"] {
            color: transparent;
        }

        .treeview-menu>li>a>.fas {
            width: 20px;
        }

        /* Treeview specific styles */
        .treeview-menu {
            display: none;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .treeview-menu > li {
            margin: 0;
            padding: 0;
        }

        .treeview-menu > li > a {
            padding: 5px 5px 5px 15px;
            display: block;
            font-size: 14px;
            color: #333;
        }

        .treeview-menu > li > a:hover {
            color: #555;
            background: transparent;
        }

        .treeview-menu > li.active > a {
            color: #fff;
            background: #3c8dbc;
        }

        .treeview > a > .pull-right-container {
            position: absolute;
            right: 10px;
            top: 50%;
            margin-top: -7px;
        }

        .treeview > a > .pull-right-container > .fa-angle-left {
            width: auto;
            height: auto;
            padding: 0;
            margin-right: 10px;
            -webkit-transition: transform 0.2s ease-in-out;
            -o-transition: transform 0.2s ease-in-out;
            transition: transform 0.2s ease-in-out;
        }

        .treeview.menu-open > a > .pull-right-container > .fa-angle-left {
            -webkit-transform: rotate(-90deg);
            -ms-transform: rotate(-90deg);
            -o-transform: rotate(-90deg);
            transform: rotate(-90deg);
        }
    </style>
</head>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/includes/head.blade.php ENDPATH**/ ?>