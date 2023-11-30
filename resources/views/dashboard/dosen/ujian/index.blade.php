@extends('layouts.dashboard')

@section('title', 'Ujian ' . $jadwal->matkul->nama . ' - ' . $jadwal->kelas->kode)

@section('content')
    @if (Session::has('success') || Session::has('error'))
        <div class="alert_success" data-flashdata="{{ Session::get('success') }}"></div>
        <div class="alert_error" data-flashdata="{{ Session::get('error') }}"></div>
    @endif

    <div class="container-fluid">
        <div class="row sticky" style="z-index: 1037 !important">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('manajemen.kuliah.jadwal.dosen.ujian.index') }}"
                                class="btn btn-primary btn-back btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <h5 class="font-weight-bold m-0 p-0 ml-2">
                                Ujian {{ $jadwal->matkul->nama }} - {{ $jadwal->kelas->kode }}
                            </h5>

                            @php
                                $tipe = $jadwal->ujian->tipe_soal == 'Essay' ? 'essay' : 'pg';
                            @endphp

                            <a href="{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.'.$tipe.'.edit', encrypt($jadwal->id)) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fas fa-external-link-alt mr-1"></i> Edit Ujian
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($ujian != null)
            @if ($ujian->tipe_soal == 'Pilihan Ganda')
                @include('dashboard.dosen.ujian.soal.pg.index')
            @else
                @include('dashboard.dosen.ujian.soal.essay.index')
            @endif
        @endif {{-- End if --}}
    </div> {{-- End container --}}

@endsection

@push('js')
    <script>
        $("#judulHalaman").html("");

        $(document).ready(function() {
            $.ajaxSetup({
                headers: $("meta[name='csrf-token']").attr('content')
            });
        });

        if (localStorage.getItem(`${noIndukUser}_fromDashboard`) == "true") {
            $(".btn-back").attr("href", "{{ route('dosen.dashboard') }}");
            $(".btn-back").click(function() {
                localStorage.removeItem(`${noIndukUser}_fromDashboard`);
            });
            $("a").click(function() {
                localStorage.removeItem(`${noIndukUser}_fromDashboard`);
            });
        }

        // Alert
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
    </script>
@endpush
