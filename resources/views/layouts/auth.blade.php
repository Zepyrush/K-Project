<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/image/logo.png') }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/auth/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/auth/sub-main.css') }}">

    @stack('css')

    <style>

        .main-footer{
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            

        }

    </style>
</head>

<body>
    @yield('content')

    <footer class="main-footer">
        <div class="text-center d-none d-sm-block">
            &copy; {{ date('Y') }} <b>Pedro & Harrif</b>. 
        </div>
    </footer>
    

   

    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery/validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery/validate/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap4/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/plugins/fontawesome-free/css/svg-with-js.css') }}"></script>

    <script>
        $(document).ready(function () {
            $(window).bind('resize', function() { // window resize
                var sizeWindow = $(window).width();
                if (sizeWindow < 767.98) {
                    $('#bannerLogin').attr('class', '');
                    $('#bannerLogin > div').addClass('d-none');
                    $(".login-wrap").css("border-radius", "25px");
                    $(".login-wrap > div").addClass("mt-2");
                } else {
                    $('#bannerLogin').attr('class',
                        'text-wrap p-4 p-lg-5 text-center d-flex align-items-center order-md-last');
                    $('#bannerLogin > div').removeClass('d-none');
                    $(".login-wrap > div").removeClass("mt-2");
                }
            }).trigger('resize');
        });
    </script>

    @stack('js')
</body>

</html>
