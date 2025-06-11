
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Verification | Efficentrix POS & ERP System</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <script src="//unpkg.com/alpinejs" defer></script>

    <link rel="stylesheet" href="{{ asset('new_look/output.css') }}"/>
{{--    <script src="{{ asset('new_look/Components.js') }}"></script>--}}

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
            <div class="flex h-24 shrink-0 items-center">
                <img class="w-44 h-24" src="{{ asset('/assets/admin/images/loginLogo.png') }}" alt="Efficentrix POS & ERP System">
            </div>
            <p class="text-3xl font-semibold leading-9 text-center mt-6 text-gray-900 w-full">Welcome!</p>
            <p class="text-base leading-normal text-center text-gray-500 w-full mt-3">Welcome back! Please enter your details.</p>

            @include('message')

            <form action="{{ route('admin.user_otp') }}" class="mt-8 w-full validate" method="post">
                {{ csrf_field() }}
                <!-- Name -->
                <div>
                    <?php

                    function maskPhoneNumber($number) {
                        if (strlen($number) < 8) return $number; 
                    $firstPart = substr($number, 0, 2);
                    $secondPart = '*';
                    $thirdPart = substr($number, 3, 2);
                    $fourthPart = '***';
                    $fifthPart = substr($number, 8);
                    return $firstPart . $secondPart . $thirdPart . $fourthPart . $fifthPart;
                    }
                    ?>
                    <label for="name" class="block text-sm font-medium leading-none text-gray-900">Phone Number</label>
                    <div class="mt-1.5">
                        <input type="text" name="phone_number" id="name"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-bizwiz-600 sm:text-sm sm:leading-6"
                               placeholder="Enter your email" value="{{ maskPhoneNumber($row->phone_number) }}" required readonly />
                    </div>
                </div>


                <!-- Pass -->
                <div class="mt-5" x-data="{showPassword:false}">
                    <label for="pass" class="block text-sm font-medium leading-none text-gray-900">OTP</label>
                    <div class="mt-1.5 relative">
                        <input name="otp" id="otp" x-bind:type="showPassword ? 'text' : 'password'"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-bizwiz-600 sm:text-sm sm:leading-6"
                               placeholder="Enter your otp" required/>

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
                <div class="flex w-full">
                  <button type="submit" value="Login"  class="btn-primary  mt-6" style="
                  margin-right: 5px;
              ">Verify OTP</button>
            <a href="{{route('admin.user_resend_otp')}}" class="btn-primary  mt-6" style="
            margin-right: 5px;
        ">Resend OTP</a>
            <a href="{{route('admin.logout')}}" class="btn-primary mt-6">Back to login</a>

                </div>
            </form>
        </div>
    </div>


    <!-- Right -->
    <div class="col-span-7 hidden lg:flex w-full relative h-full min-h-screen rounded-tl-[60px] rounded-bl-[60px]  justify-end items-end bg-cover bg-no-repeat"
         style="background-image: url('/new_look/login.png')">
        <div class="absolute px-12 pb-[56px]">
            <p class="text-5xl font-medium leading-[56px] text-white">Forging the Future of the Retail Market and Distribution of Quality Products!</p>

            <div class="mt-14">
                <p class="text-3xl font-semibold leading-9 text-white">Bizwiz POS & ERP System</p>
                <p class="text-base font-semibold mt-1 leading-7 text-white">Copyright © 2024. RetailPay. All rights reserved.</p>
            </div>
        </div>
    </div>
</div>


<script src="{{asset('assets/admin/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->

<script src="{{asset('assets/admin/bower_components/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/admin/jquery.validate.min.js')}}"></script>
<script>
 $(".validate").validate();
 </script>
    <style type="text/css">
            

            .error{
            color:red;
            }

            
        </style>
   
</body>
</html>