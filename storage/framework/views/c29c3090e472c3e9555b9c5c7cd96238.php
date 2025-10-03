




















































































        

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #000, #0f3d28);
            height: 100vh;
        }

        .login-container {
            display: flex;
            height: 100%;
            width: 80%;
            margin: auto;
        }

        .login-form {
            background: #000;
            color: #fff;
            padding: 2rem;
            border-radius: 10px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form input {
            margin-bottom: 1rem;
        }

        .sign-in-btn {
            background-color: #28a745;
            border: none;
        }

        .sign-in-btn:hover {
            background-color: #218838;
        }

        .social-icons a {
            font-size: 1.5rem;
            margin: 0 0.5rem;
            color: #fff;
        }

        .testimonial {
            background: #28a745;
            color: #fff;
            padding: 2rem;
            border-radius: 10px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin: auto 10px;
        }

        .testimonial .quote {
            font-style: italic;
        }

        .testimonial .navigation-buttons button {
            background: #fff;
            color: #28a745;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="container">

    </div>
    <div class="login-container">
        <!-- Left Side (Login Form) -->
        <div class="login-form">
            <img class="w-50 h-30" src="<?php echo e(asset('/assets/admin/images/loginLogo.png')); ?>" alt="Efficentrix">
            <?php echo $__env->make('message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <form action="<?php echo e(route('admin.make.login')); ?>" class="mt-8 w-full" method="post">
                <?php echo e(csrf_field()); ?>

            <p>Please Enter your Account details</p>
            <input type="email" class="form-control" name="username" placeholder="Email/Username" value="<?php echo e(old('username')); ?>" required autofocus>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            
            <button class="btn sign-in-btn" type="submit">Sign in</button>
            </form>
            
            
        </div>

        <!-- Right Side (Testimonial) -->
        <div class="testimonial">
            <h3>Efficentrix POS and ERP.</h3>
            <p class="quote">“Efficient and Centralized data.”</p>
            <p><strong></strong><br></p>
            <div class="navigation-buttons">
                <button>&larr;</button>
                <button>&rarr;</button>
            </div>
            <div class="mt-4 p-3 bg-light text-dark rounded">
                <p><strong>Modules</strong></p>
                <ul>
                    <li>POS</li>
                    <li>Accounting</li>
                    <li>Stock Managemant</li>
                    <li>HRM</li>
                    <li>Warehouse</li>
                    <li>Procurement</li>
                    <li>Inventory</li>
                    <li>Reports</li>
                    
                    
                </ul>
                <p></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\kaninichapchap\resources\views/admin/users/login.blade.php ENDPATH**/ ?>