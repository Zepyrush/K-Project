<!DOCTYPE html>
<html lang="en">

<head>
    @stack('head')
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('assets/image/logo.png') }}" type="image/x-icon" />
    {{-- CSS LINK  --}}
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/cropperjs/cropper.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/sub-apps.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/summernote-audio.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">

    <style>
        .note-group-select-from-files {
            display:none;
        }

        table.dataTable {
            width: 100% !important;
        }

        .sticky_sub {
            top: 135px;
        }
    </style>
    @stack('css')
</head>

<body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">

    <div class="wrapper">

        {{-- Jika mau pake loader (loading halaman) --}}

        {{-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animationShake" src="{{ asset('assets/image/logo.png') }}" height="60" width="60">
            <div class="wrapper_text">
                <div id="text_load">Loading</div>
                <div class="wrapper_dot" id="firstWrap">
                    <div class="dot"></div>
                </div>
                <div class="wrapper_dot" id="secondWrap">
                    <div class="dot"></div>
                </div>
                <div class="wrapper_dot" id="thirdWrap">
                    <div class="dot"></div>
                </div>
            </div>
        </div> --}}

        @include('layouts.sub-dashboard.topbar')

        @include('layouts.sub-dashboard.sidebar')

        <div class="content-wrapper">

            <section class="content-header">

                <div class="container-fluid d-none">
                    <div class="row">
                        <div class="col-sm-12" id="judulHalaman">
                            <h1>@yield('title')</h1>
                        </div>
                    </div>
                </div>

            </section>

            <section class="content">

                @yield('content')

            </section>

        </div>

        @include('layouts.sub-dashboard.footer')
    </div>

    {{-- MODAL LOGOUT --}}
    <div class="modal fade" id="logModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    Apakah kamu yakin ingin Logout?
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>

                    <a href="{{ route('logout') }}" type="button" class="btn btn-danger btn-logout">
                        Logout

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- JS LINK --}}
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/moment/locale/id.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery/validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery/validate/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap4/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/cropperjs/cropper.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/summernote/summernote-audio.js') }}"></script>
    <script src="{{ asset('assets/plugins/medium/medium-zoom.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/adminlte.min.js') }}"></script>

    {{-- agar script tidak di obfuscate(hash) --}}
    <script ignore--minify>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        const noIndukUser = '{{ Auth::user()->no_induk }}';
    </script>

    <script>
        $(document).ready(function() {
            // set moment locale to indonesia
            moment.locale('id');

            // tooltip hide on click
            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover',
            });

            // all id logoutButton click
            let logoutButton = document.querySelectorAll('#logoutButton');
            for (let i = 0; i < logoutButton.length; i++) {
                logoutButton[i].addEventListener('click', function(e) {
                    e.preventDefault();

                    $('#logModal').modal('show');
                });
            }

            // logout button click
            $('.btn-logout').click(function(e) {
                e.preventDefault();
                $('#logout-form').submit();
                localStorage.removeItem(`${noIndukUser}_sidebarScrollTop`); // hapus scroll top sidebar
            });

            // close modal on click outside
            $('[data-dismiss="modal"]').click(function() {
                $('.error-text').text('');
                $('.form-control').removeClass('is-invalid');
                // $('.custom-select').val(null).trigger('change');

                if (localStorage.getItem(`${noIndukUser}_modalCreate`) == 'open') {
                    localStorage.removeItem(`${noIndukUser}_modalCreate`);
                }
            });

            // set min date to today
            let dtToday = new Date();
            let month = dtToday.getMonth() + 1;
            let day = dtToday.getDate();
            let year = dtToday.getFullYear();
            (month < 10) ? month = '0' + month.toString() : month = month.toString();
            (day < 10) ? day = '0' + day.toString() : day = day.toString();
            let minDate = year + '-' + month + '-' + day;
            $('input.dated').attr('min', minDate);

            // membuat default datatable
            $.extend(true, $.fn.dataTable.defaults, {
                buttons: [
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: ':not(.noPrint)'
                        }
                    }
                ],
                pageLength: 25,
                lengthMenu: [
                    [25, 50, 100, 250, 500],
                    [25, 50, 100, 250, 500]
                ],
                language: {
                    'url': "{{ asset('assets/plugins/datatables/datatables-language/idn.json') }}"
                },
                fnDrawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip({
                        trigger: 'hover'
                    });
                }
            });
        });
    </script>

    @stack('js')
</body>

</html>
