@extends('layouts.dashboard')

@section('title', 'Jadwal Kuliah')

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

            @if ($jadwalHariIni->isNotEmpty())
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <h5 class="m-0 p-0 font-weight-bold d-flex flex-row">
                                    <i class="fas fa-calendar-alt text-primary mr-2"></i>
                                    Daftar Jadwal Kuliah <span class="text-primary ml-1">Hari Ini</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($jadwalHariIni as $jadwal)
                    @include('dashboard.mahasiswa._jadwal._container')
                @endforeach
            @endif

            @if ($jadwals->count() > 0)
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header {{ $jadwals->hasPages() ? 'p-2' : '' }}">
                            <div class="d-flex flex-row align-items-center justify-content-between">
                                <h5 class="m-0 p-0 font-weight-bold">
                                    <i class="fas fa-calendar-alt text-primary mr-1
                                        {{ $jadwals->hasPages() ? 'ml-2' : '' }}">
                                    </i>

                                    Daftar Jadwal Kuliah
                                </h5>
                                {{ $jadwals->links('pagination::simple-bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

                @foreach ($jadwals as $jadwal)
                    @include('dashboard.mahasiswa._jadwal._container')
                @endforeach

            @endif
        </div> {{-- end row --}}
    </div> {{-- end container --}}
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(".toMateri, .toTugas").click(function() {
                localStorage.setItem(`${noIndukUser}_jadwal`, 'true');
            });
        });
    </script>
@endpush
