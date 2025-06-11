{{--<!DOCTYPE html>--}}
{{--<html>--}}

{{--<head>--}}
{{--    <meta charset="utf-8" />--}}
{{--    <title>Login - #</title>--}}

{{--    <link rel="icon" href="{{ asset('favicon.ico') }}">--}}

{{--    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">--}}
{{--    <link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}

{{--    <style>--}}
{{--        .error {--}}
{{--            color: red;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}

{{--<body>--}}
{{--    <div class="main-doc">--}}
{{--        <div class="container">--}}
{{--            <div class="row login-page">--}}
{{--                <div class="col-md-6 text-center vertical-center">--}}
{{--                    <div id="login" class="">--}}
{{--                        <img src="{{ asset('/assets/admin/images/loginLogo.png') }}" Efficentrix POS & ERP System">--}}
{{--                    </div>--}}
{{--                </div>--}}


{{--                <div class="col-md-6" style="padding-right: 0;">--}}
{{--                    @if (session('active_message'))--}}
{{--                        <div class="alert alert-danger" style="margin:10px;">--}}
{{--                            {{ session('active_message') }}--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                    <div id="login">--}}
{{--                        {!! Form::open(['route' => 'admin.make.login', 'class' => 'validate form-horizontal']) !!}--}}

{{--                        <div class="input-group">--}}
{{--                            @include('message')--}}
{{--                        </div>--}}

{{--                        <div class="input-group">--}}
{{--                            <span class="fontawesome-user"></span>--}}

{{--                            {!! Form::text('username', null, [--}}
{{--                                'class' => 'form-control',--}}
{{--                                'id' => 'username',--}}
{{--                                'maxlength' => '255',--}}
{{--                                'placeholder' => 'User Name',--}}
{{--                                'required' => true,--}}
{{--                            ]) !!}--}}
{{--                            <div class="form-group">--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="input-group">--}}
{{--                            <span class="fontawesome-lock"></span>--}}
{{--                            <input type="password" name="password" id="password" class="form-control" required--}}
{{--                                placeholder="Password" />--}}
{{--                            <div class="form-group">--}}
{{--                            </div>--}}
{{--                            <input type="submit" value="Login" class="btn btn-primary">--}}
{{--                        </div>--}}
{{--                        {{ Form::close() }}--}}
{{--                    </div>--}}
{{--                </div>--}}


{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <script src="{{ asset('assets/admin/bower_components/jquery/dist/jquery.min.js') }}"></script>--}}
{{--    <!-- jQuery UI 1.11.4 -->--}}

{{--    <script src="{{ asset('assets/admin/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>--}}
{{--    <script src="{{ asset('assets/admin/jquery.validate.min.js') }}"></script>--}}
{{--    <script>--}}
{{--        $(".validate").validate();--}}
{{--    </script>--}}
{{--</body>--}}
{{--</html>--}}

        {{-- <!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="robots" content="noindex, nofollow" />
    <title>Login | Efficentrix POS & ERP System</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <script src="//unpkg.com/alpinejs" defer></script>

    <link rel="stylesheet" href="{{ asset('new_look/output.css') }}"/>

    <style>
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            width: 100%;
        }
    </style>
</head>

<body class="h-full bg-white min-h-sreen">
<div class="lg:grid lg:grid-cols-12 gap-2">
    <!-- Left -->
    <div class="lg:col-span-5 px-4 lg:px-8 py-8">
        <div class="w-full flex flex-col items-center justify-center max-w-96 mx-auto mt-12 lg:mt-24 ">
            <div class="flex h-30 shrink-0 items-center">
                <img class="w-50 h-30" src="{{ asset('/assets/admin/images/loginLogo.png') }}" alt="Efficentrix">
            </div>
            <p class="text-3xl font-semibold leading-9 text-center mt-6 text-gray-900 w-full"></p>
            <p class="text-base leading-normal text-center text-gray-500 w-full mt-3">Input your login  details.</p>

            @include('message')

            <form action="{{ route('admin.make.login') }}" class="mt-8 w-full" method="post">
                {{ csrf_field() }}
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium leading-none text-gray-900">Username</label>
                    <div class="mt-1.5">
                        <input type="text" name="username" id="name"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-bizwiz-600 sm:text-sm sm:leading-6"
                               placeholder="Enter your email" value="{{ old('username') }}" required autofocus />
                    </div>
                </div>


                <!-- Pass -->
                <div class="mt-5" x-data="{showPassword:false}">
                    <label for="pass" class="block text-sm font-medium leading-none text-gray-900">Password</label>
                    <div class="mt-1.5 relative">
                        <input name="password" id="pass" x-bind:type="showPassword ? 'text' : 'password'"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-bizwiz-600 sm:text-sm sm:leading-6"
                               placeholder="Enter your password" required/>

                        <button x-on:click="showPassword = !showPassword" type="button" class="absolute right-0 top-2 mr-2 hover:scale-105 transition-all ease-in-out duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak x-show="showPassword" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak x-show="!showPassword" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full mt-6">Sign in</button>
            </form>
        </div>
    </div>


    <!-- Right -->
    <div class="col-span-7 hidden lg:flex w-full relative h-full min-h-screen rounded-tl-[60px] rounded-bl-[60px]  justify-end items-end bg-cover bg-no-repeat"
         style="background-image: url('/new_look/login.png')">
        <div class="absolute px-12 pb-[56px]">
            <p class="text-5xl font-medium leading-[56px] text-white">Total Control of Your Business</p>

            <div class="mt-14">
                <p class="text-3xl font-semibold leading-9 text-white">Efficentrix POS and ERP</p>
                <p class="text-base font-semibold mt-1 leading-7 text-white">Copyright © {{ date() }}. Altrom Technologies. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/admin/bower_components/jquery/dist/jquery.min.js') }}"></script>
</body>
</html> --}}

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
            <img class="w-50 h-30" src="{{ asset('/assets/admin/images/loginLogo.png') }}" alt="Efficentrix">
            @include('message')

            <form action="{{ route('admin.make.login') }}" class="mt-8 w-full" method="post">
                {{ csrf_field() }}
            <p>Please Enter your Account details</p>
            <input type="email" class="form-control" name="username" placeholder="Email/Username" value="{{ old('username') }}" required autofocus>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            {{-- <a href="#" class="text-light">Forgot Password?</a> --}}
            <button class="btn sign-in-btn" type="submit">Sign in</button>
            </form>
            {{-- <div class="social-icons mt-3">
                <a href="#"><i class="bi bi-google"></i></a>
                <a href="#"><i class="bi bi-apple"></i></a>
                <a href="#"><i class="bi bi-facebook"></i></a>
            </div> --}}
            {{-- <a href="#" class="mt-3 text-light">Create an account</a> --}}
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
