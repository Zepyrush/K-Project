@extends('layouts.dashboard')

@section('title', 'Dashboard Dosen')

@section('content')
    @if (Session::has('success') || Session::has('error'))
        <div class="notif-info" data-status="{{ Session::get('success') }}"></div>
        <div class="notif-error" data-status="{{ Session::get('error') }}"></div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                @include('dashboard.dosen._dashboard._information')

                <div class="row">
                    <div class="col-lg-6 mb-4">
                        @include('dashboard.dosen._dashboard._notifikasi-tugas')
                    </div>

                    <div class="col-lg-6 mb-4">
                        @include('dashboard.dosen._dashboard._notifikasi-absen')
                        <div class="mt-4">
                            @include('dashboard.dosen._dashboard._notifikasi-ujian')
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-5 mb-4">
                        @include('dashboard.dosen._dashboard._table-absensi-today')
                    </div>
                    <div class="col-lg-7">
                        @include('dashboard._profile._tab-content')
                    </div>

                </div>

            </div> {{-- END COL --}}
        </div> {{-- END ROW --}}
    </div> {{-- END CONTAINER --}}
@endsection

@push('js')
    <script>
        // Notifikasi jika ada notif dari controller
        const SwalToast = Swal.mixin({
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

        // show notif
        const notifInfo = $('.notif-info').data('status');
        const notifError = $('.notif-error').data('status');
        if (notifInfo) {
            SwalToast.fire({
                icon: 'info',
                title: notifInfo
            })
        } else if (notifError) {
            SwalToast.fire({
                icon: 'error',
                title: notifError
            })
        }

        $(document).ready(function () {
            $('.show_btn').click(function () {
                localStorage.setItem(`${noIndukUser}_fromDashboard`, "true");
            });
        });
    </script>
@endpush
