@extends('layouts.dashboard')

@section('title', 'Jadwal Mengajar Anda')

@section('content')
    @if (Session::has('success'))
        <div class="alert_success" data-flashdata="{{ Session::get('success') }}"></div>
    @elseif (Session::has('error'))
        <div class="alert_error" data-flashdata="{{ Session::get('error') }}"></div>
    @endif

    <div class="container-fluid">
        <div class="row">

            @if ($jadwalHariIni->isEmpty() && $jadwals->isEmpty())
                <div class="col-lg-12">
                    <div class="alert card card-primary card-outline">
                        <p class="m-0 p-0">
                            <i class="fas fa-exclamation-triangle text-primary"></i>
                            <span class="ml-2">
                                Oops.. Jadwal kuliah anda belum ada, silahkan laporkan ini ke
                                admin kuliah ya!
                            </span>
                        </p>
                    </div>
                </div>
            @endif

             {{-- INI UNTUK JADWAL YANG MENGAJAR HARI INI --}}
            @if ($jadwalHariIni->isNotEmpty())
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="m-0 p-0 font-weight-bold">
                                <i class="fas fa-calendar-alt text-primary mr-1"></i>
                                Daftar Jadwal Kuliah Hari Ini
                            </h5>
                        </div>
                    </div>
                </div>

                @foreach ($jadwalHariIni as $jadwal)
                    @include('dashboard.dosen.jadwal._sub._container-jadwal-kuliah')
                @endforeach
            @endif

            {{-- INI UNTUK JADWAL YANG TIDAK MENGAJAR HARI INI --}}
            @if ($jadwals->isNotEmpty())
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header {{ $jadwals->hasPages() ? 'p-2' : '' }}">
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <h5 class="m-0 p-0 font-weight-bold">
                                    <i class="fas fa-calendar-alt text-primary mr-1 {{ $jadwals->hasPages() ? 'ml-2' : '' }}"></i>
                                    Daftar Jadwal Kuliah
                                </h5>
                                {{ $jadwals->links('pagination::simple-bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @foreach ($jadwals as $jadwal)
               @include('dashboard.dosen.jadwal._sub._container-jadwal-kuliah')
            @endforeach
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.masuk').click(function() {
                Swal.fire({
                    icon: 'error',
                    html: 'Anda tidak dapat masuk ke kelas ini, karena tidak sesuai dengan jadwal',
                    allowOutsideClick: false,
                })
            });

            $(".toMateri, .toTugas").click(function() {
                localStorage.setItem(`${noIndukUser}_jadwal`, 'true');
            });

            const notifSuccess = $('.alert_success').data('flashdata');
            const notifError = $('.alert_error').data('flashdata');

            if (notifSuccess) {
                Toast.fire({
                    icon: 'success',
                    title: notifSuccess
                });
            } else if (notifError) {
                Swal.fire({
                    icon: 'error',
                    html: notifError,
                    allowOutsideClick: false,
                });
            }
        });
    </script>
@endpush
