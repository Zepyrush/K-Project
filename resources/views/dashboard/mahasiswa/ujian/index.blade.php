@extends('layouts.dashboard')

@section('title', 'Jadwal Ujian')

@section('content')
    @if (Session::has('success') || Session::has('error'))
        <div class="alert_success" data-flashdata="{{ Session::get('success') }}"></div>
        <div class="alert_error" data-flashdata="{{ Session::get('error') }}"></div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline sticky">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="font-weight-bold m-0 p-0 ml-2">
                                <i class="fas fa-calendar-alt mr-1 text-primary"></i> Jadwal Ujian
                            </h5>

                            <h6 id="timerCont" class="m-0 px-2 py-1 bg-primary" style="border-radius: 4px;">
                                <i class="fas fa-clock mr-1"></i>  <span id="timer">00:00:00 WIB</span>
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card card-primary card-outline ">

                    <div class="card-header p-2">
                        <div class="row justify-content-between align-items-center">

                            <div class="col-md-4 col-6">
                                <select id="filter_matkul" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($jadwals->unique('matkul_id') as $jadwal)
                                        <option value="{{ $jadwal->matkul->nama }}">
                                            {{ $jadwal->matkul->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <button id="cetakTable" class="btn btn-primary btn-sm">
                                    <i class="fas fa-print mr-1"></i> Cetak
                                </button>
                                <button id="refreshTable" class="btn btn-warning btn-sm ml-1 mr-3"
                                    data-toggle="tooltip" title="Refresh Table">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-hover" id="tableUjian">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Judul</th>
                                    <th>Matakuliah</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>  {{-- col-lg-12 --}}
        </div> {{-- row --}}
    </div> {{-- container-fluid --}}

    @include('dashboard.mahasiswa.ujian._modal._modal-mulai-ujian')
@endsection

@push('js')
    <script>
        $("#judulHalaman").html("");

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

        var alertShown = false;

        let jamInterval = setInterval(() => {
            var now = new Date(),
                jam = now.getHours(),
                menit = now.getMinutes(),
                detik = now.getSeconds(),
                started = $('.belum_mulai').data('mulai');

            (jam < 10) ? jam = "0" + jam : jam;

            (menit < 10) ? menit = "0" + menit : menit;

            (detik < 10) ? detik = "0" + detik : detik;

            $("#timer")
                .html(jam + ":" + menit + ":" + detik + " WIB");

            var valueJam = jam + ":" + menit;

            if (valueJam == started && !alertShown) {
                alertShown = true;

                Swal.fire({
                    icon: 'info',
                    html: '<span class="font-weight-bold text-uppercase">Ujian dimulai</span> <hr> Ada ujian yang dimulai pada waktu ini. Silahkan klik tombol pensil untuk memulai ujian.',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            }
        }, 1000);

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on("click", ".belum_mulai", function () {
                Swal.fire({
                    icon: 'info',
                    html: '<span class="font-weight-bold text-uppercase">Ujian belum dimulai</span> <hr> Anda tidak dapat mengakses ujian ini karena ujian belum dimulai.',
                    allowOutsideClick: false,
                });
            });

            $(document).on("click", ".sudah_selesai", function () {
                Swal.fire({
                    icon: 'error',
                    html: '<span class="font-weight-bold text-uppercase">Ujian Sudah Selesai</span> <hr> Maaf anda tidak dapat mengakses ujian ini karena ujian sudah selesai. <span class="text-muted">Silahkan hubungi dosen pengampu untuk mengulang ujian.</span>',
                    allowOutsideClick: false,
                });
            });


            let filterMatkul = $('#filter_matkul').val();

            let table = $("#tableUjian").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : "{{ route('manajemen.kuliah.ujian.mahasiswa.index') }}",
                    data: function(d) {
                        d.filterMatkul = filterMatkul;
                        return d;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'judul_ujian', name: 'judul_ujian' },
                    { data: 'matkul_ujian', name: 'matkul_ujian' },
                    { data: 'tanggal', name: 'tanggal' },
                    {
                        data: 'started_at',
                        name: 'started_at',
                        render: function(data, type, row) {
                            let jam;
                            (row.ended_at == null) ?
                                jam = row.started_at + ' WIB' :
                                jam = row.started_at + ' - ' + row.ended_at + ' WIB';

                            return jam;
                        }
                    },
                    {
                        className: 'noPrint',
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $('.filter').change(function() {
                filterMatkul = $('#filter_matkul').val();
                table.ajax.reload(null, false);
            });

            $("#refreshTable").on("click", function(e) {
                e.preventDefault();

                $("#filter_matkul").val("").trigger("change");
                table.ajax.reload(null, false);
            });

            function initSelect2(id, placeholder, dropdownParent) {
                let dropdownParentVal = null;

                if (dropdownParent) {
                    dropdownParentVal = $(dropdownParent);
                }

                $(id).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: dropdownParentVal,
                });
            }

            initSelect2("#filter_matkul", "Filter Mata Kuliah");
        });
    </script>
@endpush
