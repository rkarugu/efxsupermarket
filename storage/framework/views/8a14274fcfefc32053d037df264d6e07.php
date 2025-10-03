<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Page Not Found </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgba(26, 32, 44, 1);
            color: #fff;
        }

        #top-div {
            width: 90vw;
            height: 60vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #content-div {
            width: 70%;
            position: relative;
            border-top: 5px solid #d73925;
            border-radius: 3px;

        }

        #content-div h5 {
            font-size: 20px;
            font-weight: 700;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        #content-div code {
            font-size: 16px;
            display: block;
            margin-top: 8px;
        }

        #actions {
            display: flex;
        }

        .btn {
            background-color: #d73925;
            border: none;
            color: white;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 3px;
            font-weight: 600;
        }
    </style>
</head>
<body class="antialiased">
<div id="top-div">
    <div id="content-div">
        <h5> The page you requested is not available. </h5>
        <div id="actions">
            <a href="<?php echo e(url()->previous()); ?>" class="btn btn-primary"> Go Back </a>
            <a href="/" class="btn btn-primary" style="margin-left: 12px;"> Take Me Home </a>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/errors/404.blade.php ENDPATH**/ ?>