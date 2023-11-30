@extends('layouts.dashboard')

@section('title', 'Kelas ' . $jadwal->kelas->kode . ' - ' . $jadwal->matkul->nama)

@section('content')
    <div class="container-fluid">
        <div class="row sticky" style="z-index: 1037 !important;">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex flex-row align-items-center justify-content-between">
                            <div>
                                <a href="{{ route('manajemen.kuliah.jadwal.mahasiswa.index') }}"
                                    class="btn btn-sm btn-primary btn-back">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                                <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.materi', encrypt($jadwal->id)) }}"
                                    class="btn btn-sm btn-info mr-1 ml-1" data-toggle="tooltip" title="Materi Kelas">
                                    <i class="fas fa-book-open"></i>
                                </a>
                                <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.tugas', encrypt($jadwal->id)) }}"
                                    class="btn btn-sm bg-purple position-relative" data-toggle="tooltip" title="Tugas Kelas">
                                    <i class="fas fa-book"></i>

                                    @if ($tugas != false)
                                        <span class="badge badge-danger badge-pill notif">{{ $tugas }}</span>
                                    @endif
                                </a>
                            </div>

                            <h5 class="m-0 p-0 font-weight-bold">
                                @yield('title')
                            </h5>

                            <div>
                                @if ($prev)
                                    <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.index', encrypt($prev->id)) }}"
                                        class="btn btn-sm btn-success mr-1" data-toggle="tooltip"
                                        title="Kelas Sebelumnya ({{ $prev->matkul->nama }})" data-placement="left">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                @endif

                                @if ($next)
                                    <a href="{{ route('manajemen.kuliah.kelas.mahasiswa.index', encrypt($next->id)) }}"
                                        class="btn btn-sm btn-success" data-toggle="tooltip"
                                        title="Kelas Selanjutnya ({{ $next->matkul->nama }})" data-placement="left">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-12 mb-3">
                <div class="list-group mb-3">
                    @if ($waktu_presensi && $presensi)
                        @if ($sudah_presensi)
                            <button type="button"
                                class="list-group-item list-group-item-action active absen bg-secondary cursor_default">
                                <h6 class="m-0 p-0 text-white font-weight-bold text-center">
                                    SUDAH ABSEN
                                </h6>
                            </button>
                        @else
                            {{-- <form id="formPresensiMahasiswa"
                                action="{{ route('manajemen.kuliah.kelas.mahasiswa.presensi') }}" method="POST">
                                @csrf
                                @method('POST')

                                <input type="hidden" name="jadwal" value="{{ encrypt($jadwal->id) }}">

                                <button type="submit"
                                    class="list-group-item list-group-item-action active absen bg-success">
                                    <h6 id="buttonAbsen" class="m-0 p-0 text-white font-weight-bold text-center">
                                        ABSEN
                                    </h6>
                                </button>
                            </form> --}}
                        @endif
                    @else
                        <button type="button"
                            class="list-group-item list-group-item-action active absen bg-danger cursor_default">
                            <h6 class="m-0 p-0 text-white font-weight-bold text-center">
                                ABSEN BELUM DIBUKA
                            </h6>
                        </button>
                    @endif
                </div>

                @include('dashboard.mahasiswa._kelas._information')
            </div>

            <div class="col-lg-9">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <button id="cetakTable" class="btn btn-primary btn-sm">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="tableAbsensi" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>Matakuliah</th>
                                    <th>Pertemuan</th>
                                    <th>Tanggal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailInfoPresensi" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title ml-2 font-weight-bold" id="judulModalInfoPresensi"></h5>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-4">
                                    <b>Matakuliah</b>
                                </div>
                                <div id="matkul" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-4">
                                    <b>Status Kehadiran</b>
                                </div>
                                <div id="status" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-4">
                                    <b>Pertemuan</b>
                                </div>
                                <div id="pertemuan" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-4">
                                    <b>Tanggal</b>
                                </div>
                                <div id="tanggal" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-4">
                                    <b>Rangkuman</b>
                                </div>
                                <div id="rangkuman" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-4 col-4">
                                    <b>Berita Acara</b>
                                </div>
                                <div id="berita_acara" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                    </div> {{-- End Row --}}
                </div> {{-- End Modal Body --}}

                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>

            </div> {{-- End Modal Content --}}
        </div> {{-- End Modal Dialog --}}
    </div> {{-- End Modal --}}
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (localStorage.getItem(`${noIndukUser}_fromDashboard`) == "true") {
                $(".btn-back").attr("href", "{{ route('mahasiswa.dashboard') }}");
                $(".btn-back").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
                $("a").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
            }

            var table = $("#tableAbsensi").DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manajemen.kuliah.kelas.mahasiswa.index', encrypt($jadwal->id)) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data == 'hadir') {
                                return '<button class="btn btn-success btn-sm cursor_default">Hadir</button>';
                            } else if (data == 'tidak_hadir') {
                                return '<button class="btn btn-danger btn-sm cursor_default">Tidak Hadir</button>';
                            } else if (data == 'belum_absen') {
                                return '<button class="btn btn-warning btn-sm cursor_default">Belum Absen</button>';
                            }
                        }
                    },
                    { data: 'matkul', name: 'matkul' },
                    { data: 'pertemuan', name: 'pertemuan' },
                    { data: 'tanggal', name: 'tanggal' },
                    { className: 'noPrint', data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $("#formPresensiMahasiswa").on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#buttonAbsen').attr('disabled', true);
                        $('#buttonAbsen').html('<i class="fas fa-spin fa-spinner"></i>');
                    },
                    complete: function() {
                        $('#buttonAbsen').removeAttr('disabled');
                        $('#buttonAbsen').html('Absen');
                    },
                    success: function(res) {
                        if (res.success == true) {
                            Swal.fire({
                                icon: 'success',
                                html: res.message,
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan data ini",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            }); // End Submit Form

            $(document).on('click', '.detail_presensi', function(e) {
                e.preventDefault();

                let id = $(this).attr('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.kelas.mahasiswa.detailInfoPresensi', ':id') }}"
                        .replace(':id', id),
                    data: {
                        jadwal_id: "{{ encrypt($jadwal->id) }}",
                    },
                    dataType: "json",
                    success: function(res) {
                        $("#modalDetailInfoPresensi").modal('show');

                        let data = res.data;

                        $("#judulModalInfoPresensi").html("Detail Presensi Pertemuan " +
                            data.pertemuan);

                        for (let key in data) {
                            $("#" + key).html(data[key]);
                        }
                    }
                });
            });
        });
    </script>
@endpush
