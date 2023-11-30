@extends('layouts.dashboard')

@section('title', 'Kelas ' . $jadwal->kelas->kode . ' - ' . $jadwal->matkul->nama)

@push('css')
    <style>
        .cursor_p {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    @if (Session::has('success'))
        <div class="alert_success" data-flashdata="{{ Session::get('success') }}"></div>
    @elseif (Session::has('error'))
        <div class="alert_error" data-flashdata="{{ Session::get('error') }}"></div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('manajemen.kuliah.jadwal.dosen.kuliah.index') }}"
                                class="btn-back btn-sm btn btn-primary in-title">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <h5 class="font-weight-bold m-0 p-0 ml-2">
                                @yield('title')
                            </h5>

                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-12 mb-3">
                <div class="sticky">
                    {{-- Jika absensi belum dibuat --}}
                        @if (!$absen_has_created)
                            <div class="list-group mb-3">
                                <button type="button" id="addAbsensi" value="{{ encrypt($jadwal->id) }}"
                                    class="list-group-item list-group-item-action bg-success text-uppercase
                                    font-weight-bold active absen text-center">
                                    Buat Absensi
                                </button>
                            </div>
                        @endif

                        {{-- Jika tugas pertemuan-nya belum dibuat --}}
                        @if ($absen_has_created && !$tugas_has_created)
                            <div class="list-group mb-3">
                                <button id="modalTugas" type="button" class="list-group-item list-group-item-action
                                    active absen bg-primary font-weight-bold text-center text-uppercase"
                                    value="{{ encrypt($jadwal->id) }}">
                                    Buat Tugas
                                </button>
                            </div>
                        @endif

                    <div class="list-group mb-3">
                        <button type="button"
                            class="list-group-item list-group-item-action active cursor_default font-weight-bold">
                            Informasi Mahasiswa
                        </button>

                        <div id="infoKehadiranMhs"></div>
                    </div>

                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action active
                            font-weight-bold cursor_default">
                            Kelola Kelas
                        </button>

                        <a href="{{ route('manajemen.kuliah.materi.dosen.index', encrypt($jadwal->id)) }}"
                            class="list-group-item list-group-item-action">
                            <i class="fas fa-book mr-1"></i> Materi
                        </a>

                        <a href="{{ route('manajemen.kuliah.tugas.dosen.index', encrypt($jadwal->id)) }}"
                            class="list-group-item list-group-item-action position-relative">
                            <i class="fas fa-file-alt mr-1"></i> Tugas

                            @if ($tugas_belum_dinilai != 0)
                                <span class="badge badge-danger badge-pill float-right position-relative"
                                    style="top: 5px; padding:0 5px 0 5px;" data-toggle="tooltip"
                                    title="Ada tugas yang belum dinilai">
                                    &nbsp;
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">

                <form id="formRekapAbsen" action="{{ route('manajemen.kuliah.kelas.dosen.storeKehadiran') }}"
                    method="POST">
                    @csrf
                    @method('POST')

                    <div class="card card-primary card-outline sticky">
                        <div class="card-header p-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <a href="{{ route('manajemen.kuliah.jadwal.dosen.kuliah.index') }}"
                                        class="btn-back btn-sm btn btn-primary mr-1" style="display: none">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                    @if (!$absen_has_created)
                                        <button type="button" disabled class="btn btn-success btn-sm">
                                            Rekap Absensi
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-success submitAbsen btn-sm">
                                            Rekap Absensi
                                        </button>
                                    @endif
                                </div>

                                <div>
                                    <button id="cetakTable" class="btn btn-primary btn-sm">
                                        <i class="fas fa-print mr-1"></i> Cetak
                                    </button>

                                    <button id="refreshTable" class="btn btn-warning btn-sm ml-1"
                                        data-toggle="tooltip" title="Refresh Table">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-primary card-outline">
                        <input type="hidden" value="{{ $jadwal->id }}" name='jadwal'>
                        <input type="hidden" value="{{ $absen->id ?? '' }}" name='parent'>
                        <input type="hidden" value="{{ $absen->pertemuan ?? '' }}" name="pertemuan">

                        <div class="card-body table-responsive">
                            <table id="tableKelas" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Mahasiswa</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @include('dashboard.dosen._kelas._modal-create')
@endsection

@push('js')
    <script>
        // cek apakah dari absen masuknya ke kelasnya
        if (localStorage.getItem(`${noIndukUser}_fromAbsen`) == 'true') {
            $(".btn-back").attr("href", "{{ route('manajemen.kuliah.absen.dosen.index') }}");
            $(".btn-back").click(function() {
                localStorage.removeItem(`${noIndukUser}_fromAbsen`);
            });
            $(".nav-link").click(function() {
                localStorage.removeItem(`${noIndukUser}_fromAbsen`);
            });
        } else if (localStorage.getItem(`${noIndukUser}_fromDashboard`) == "true") { // cek apakah dari dashboard masuknya ke kelasnya
            $(".btn-back").attr("href", "{{ route('dosen.dashboard') }}");
            $(".btn-back").click(function() {
                localStorage.removeItem(`${noIndukUser}_fromDashboard`);
            });
            $(".nav-link").click(function() {
                localStorage.removeItem(`${noIndukUser}_fromDashboard`);
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(window).on('scroll', function() {
                if ($(this).scrollTop() > 65) {
                    $('.sticky .btn-back').show('fade');
                    $('.btn-back.in-title').css("visibility", "hidden");
                } else {
                    $('.sticky .btn-back').hide('fade');
                    $('.btn-back.in-title').css("visibility", "visible");
                }
            });

            $(document).on('change', 'input[type="file"]', function(event) { // show filename
                let fileName = $(this).val();

                if (fileName == undefined || fileName == "") {
                    $(this).next('.custom-file-label').html('Cari file soal tugas..')
                } else {
                    $(this).next('.custom-file-label').html(event.target.files[0].name);
                }
            });

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
                });
            }

            var table = $('#tableKelas').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('manajemen.kuliah.kelas.dosen.index', encrypt($jadwal->id)) }}",
                columns: [
                    {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'mahasiswa',name: 'mahasiswa'},
                    {data: 'status', name: 'status'},
                ],
                buttons: [
                    {
                        extend: 'print',
                        exportOptions: {
                            format: {
                                body: function (data, row, column, node) {
                                    // Hanya mencetak baris dengan status yang memiliki radio button yang dipilih
                                    if (column === 2) {
                                        var radioChecked = $(data).find('input[type="radio"]:checked');
                                        if (radioChecked.length > 0) {
                                            return radioChecked.siblings('label').text();
                                        } else {
                                            return '';
                                        }
                                    }
                                    return data;
                                }
                            }
                        }
                    }
                ]
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $("#refreshTable").on("click", function(e) {
                e.preventDefault();
                table.ajax.reload();
            });

            // insert rekap absen
            $("#formRekapAbsen").on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitAbsen').attr('disabled', true);
                        $('.submitAbsen').html('<i class="fas fa-spin fa-spinner"></i>');
                    },
                    complete: function() {
                        $('.submitAbsen').removeAttr('disabled');
                        $('.submitAbsen').html('Rekap Absen');
                    },
                    success: function(res) {
                        if (res.status == 500) {
                            Swal.fire({
                                icon: 'warning',
                                html: res.message,
                            });
                        } else {
                            fetchInfoKehadiranMhs();
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // show modal absensi
            $("#addAbsensi").on('click', function(e) {
                e.preventDefault();

                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.absen.dosen.create', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $("#modalAbsensiCreate").modal('show');

                        $("#add_jadwal").val(id);
                        $("#add_matkul").val(res.jadwal.matkul.nama);
                        $("#add_kelas").val(res.jadwal.kelas.id);

                        $("#modalAbsensiCreate .modal-title")
                            .html("Form - Buat Absensi Kelas " +  res.jadwal.kelas.kode);

                        if (res.absen != null) {
                            // jika pertemuan sudah 16
                            if (res.absen.pertemuan == 16) {
                                $("#add_pertemuan").val();
                                $(".submitAdd").attr('disabled', true).html("Max!");

                                let text = `
                                    <span class='font-weight-bold'>Pertemuan Mencapai Batas MAX</span> <hr>
                                    Halo, \t{{ Auth::user()->name }}.\t Silahkan hubungi admin {{ config('app.name') }}
                                    untuk mengulang pertemuan di kelas ${res.jadwal.kelas.kode}
                                `;

                                Swal.fire({
                                    icon: 'info',
                                    html: text,
                                    allowOutsideClick: false,
                                    confirmButtonText: 'Oke, mengerti',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $("#modalAbsensiCreate").modal('hide');
                                    }
                                });
                            } else {
                                $("#add_pertemuan").val(parseInt(res.absen.pertemuan) + 1);
                            }
                        } else {
                            $("#add_pertemuan").val(1);
                        }
                    }
                });
            });

            // insert absensi
            $("#formAbsenJadwal").on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitAdd').attr('disabled', true);
                        $('.submitAdd').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('input.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.submitAdd').removeAttr('disabled');
                        $('.submitAdd').html('Buat');
                    },
                    success: function(res) {
                        if (res.status == 200) {
                            Swal.fire({
                                icon: 'success',
                                html: res.message,
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else if (res.status == 401) {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            });
                        } else {
                            $.each(res.errors, function(prefix, val) {
                                $('span.' + prefix + '_error').text(val[0]);
                                $('#add_' + prefix).addClass('is-invalid');
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // show modal tugas
            $("#modalTugas").on('click', function(e) {
                e.preventDefault();

                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.tugas.dosen.create', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $("#modalCreateTugas").modal('show');
                        $("#tugas_pertemuan").val(res.pertemuan.pertemuan);

                        function select2Create() {
                            $("#tugas_tipe").select2({
                                placeholder: "Pilih tipe untuk soal tugas",
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#modalCreateTugas'),
                            });
                        }

                        select2Create();

                        $("#tugas_tipe").on('change', function() {
                            let tipe = $(this).val();

                            if (tipe == 'file') {
                                $("#tugas_file").show('fade');
                                $("#tugas_link").hide('fade');
                                $(".submitTugas").removeAttr('disabled');
                            } else if (tipe == 'link') {
                                $("#tugas_file").hide('fade');
                                $("#tugas_link").show('fade');
                                $(".submitTugas").removeAttr('disabled');
                            } else {
                                select2Create();
                                $("#tugas_file").hide('fade');
                                $("#tugas_link").hide('fade');
                                $(".submitTugas").attr('disabled', true);
                            }
                        });
                    }
                });
            });

            // insert tugas
            $("#formAddTugas").on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitTugas').attr('disabled', true);
                        $('.submitTugas').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.submitTugas').removeAttr('disabled');
                        $('.submitTugas').html('Buat Tugas');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            $.each(res.errors, function(key, val) {
                                $('span.tugas_' + key + '_error').text(val[0]);
                                $("#tugas_" + key).addClass('is-invalid');
                                $("#linkTugas").addClass('is-invalid');
                            });
                        } else if (res.status == 401) {
                            $("#modalCreateTugas").modal("hide");
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            })
                        } else {
                            $("#modalCreateTugas").modal("hide");

                            Swal.fire({
                                icon: 'success',
                                html: res.message,
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // reset modal tugas
            $("#modalCreateTugas").on('hidden.bs.modal', function() {
                $("#formAddTugas")[0].reset();
                $("#tugas_tipe").val(null).trigger('change');
                $("#tugas_file").hide('fade');
                $("#tugas_link").hide('fade');
                $(".submitTugas").attr('disabled', true);
                $(document).find('.form-control').removeClass('is-invalid');
            });


            // fetch info kehadiran mahasiswa
            fetchInfoKehadiranMhs();
            function fetchInfoKehadiranMhs() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.kelas.dosen.infoKehadiranMhs', encrypt($jadwal->id)) }}",
                    dataType: "json",
                    beforeSend: function() {
                        $("#infoKehadiranMhs").html('<div class="text-center mt-3 mb-3"><i class="fas fa-spin fa-spinner"></i></div>');
                    },
                    success: function (res) {
                        $("#infoKehadiranMhs").html(res);
                    }
                });
            }
        });
    </script>
@endpush
