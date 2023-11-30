@extends('layouts.dashboard')

@section('title', 'Dashboard Admin')

@section('content')
    @if (Session::has('success') || Session::has('error'))
        <div class="notif-info" data-status="{{ Session::get('success') }}"></div>
        <div class="notif-error" data-status="{{ Session::get('error') }}"></div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">

                    @include('dashboard.admin._dashboard._information-data')
                </div>

                <div class="row">
                    @include('dashboard.admin._dashboard._table-last-seen')

                    <div class="col-lg-7">
                        @include('dashboard._profile._tab-content')
                    </div>

                </div> {{-- END ROW --}}

            </div> {{-- END COL-LG-12 --}}
        </div> {{-- END ROW --}}
    </div> {{-- END CONTENT --}}
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
    </script>
@endpush
