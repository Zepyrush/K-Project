@extends('layouts.dashboard')

@section('title', 'Tugas ' . $jadwal->matkul->nama)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 sticky">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex flex-row align-items-center justify-content-between">
                            <a href="javascript:void(0)" class="btn btn-primary btn-back btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <h5 class="m-0 font-weight-bold">
                                @yield('title')
                            </h5>

                            <div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card card-primary card-outline mb-2">
                    <div class="card-header p-1">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#listTugas" data-toggle="tab">
                                   Daftar Tugas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#tugasSelesai" data-toggle="tab">
                                    Tugas Selesai
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header p-2">
                        <button class="btn btn-primary btn-sm print-tugas" id="cetakTugasTable">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                        <button id="refreshTugasTable" class="btn btn-warning btn-sm ml-1"
                            data-toggle="tooltip" title="Refresh Table">
                            <i class="fas fa-sync"></i>
                        </button>

                        <button class="btn btn-primary btn-sm print-tugas" id="cetakTugasSelesaiTable"
                            style="display: none">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                        <button id="refreshTugasSelesaiTable" class="btn btn-warning btn-sm ml-1"
                            data-toggle="tooltip" title="Refresh Table" style="display: none">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>

                    <div class="card-body table-responsive">
                        <div class="tab-content">
                            <div class="active tab-pane fade show" id="listTugas">
                                <table id="tugasTable" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Judul</th>
                                            <th>Pertemuan</th>
                                            <th>Deskripsi</th>
                                            <th>Deadline</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade show" id="tugasSelesai">
                                <table id="tugasSelesaiTable" class="table table-hover" style="width:1200px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Judul</th>
                                            <th>Pertemuan</th>
                                            <th>Nilai</th>
                                            <th>Komentar Dosen</th>
                                            <th>Tugas Anda</th>
                                            <th>Dinilai Pada</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal CREATE AND EDIT --}}
    <div class="modal fade" id="modalKirimTugas" tabindex="-1" role="dialog" aria-labelledby="replayTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-centered">
                <div class="modal-header p-2">
                    <h5 class="modal-title font-weight-bold ml-2" id="replayTitle">Form - Kirim Tugas</h5>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="formKirimTugas" action="#" autocomplete="off" method="POST">
                    @csrf
                    @method('POST')

                    <input type="hidden" name="tugas_id" id="tugas_id">
                    <input type="hidden" name="jadwal_id" id="jadwal_id" value="{{ encrypt($jadwal->id) }}">

                    <div class="modal-body">
                        <div class="form-group">
                            <label class="d-flex align-items-center" for="file">
                                Upload Tugas
                            </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="send_file" name="file">
                                <label class="custom-file-label" for="create-file">
                                    Silahkan pilih file..
                                </label>
                                <span class="invalid-feedback d-block error-text file_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="submitSend btn btn-success">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (localStorage.getItem(`${noIndukUser}_fromDashboard`) == "true") { // Jika dari dashboard
                $(".btn-back").attr("href", "{{ route('mahasiswa.dashboard') }}");
                $(".btn-back, a").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
            } else if (localStorage.getItem(`${noIndukUser}_jadwal`) == 'true') { // Jika dari jadwal
                $(".btn-back").attr("href", "{{ route('manajemen.kuliah.jadwal.mahasiswa.index') }}");
                $(".btn-back, a").click(function() {
                    localStorage.removeItem(`${noIndukUser}_jadwal`);
                });
            } else {
                $(".btn-back").attr("href", "{{ route('manajemen.kuliah.kelas.mahasiswa.index', encrypt($jadwal->id)) }}");
            }

            // DataTable untuk table dengan id tugasTable
            var tableTugas = $("#tugasTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manajemen.kuliah.kelas.mahasiswa.tugas', encrypt($jadwal->id)) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'judul',
                        name: 'judul'
                    },
                    {
                        data: 'pertemuan',
                        name: 'pertemuan',
                        render: function(data, type, row) {
                            return "<span class='badge badge-primary rounded-circle' style='width:25px;height:25px;'><h6 class='m-0'>" +
                                data + "</h6></span>";
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('text-align', 'center');
                        }
                    },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'deadline', name: 'deadline', },
                    { className: 'noPrint', data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            });

            // DataTable untuk table dengan id tugasSelesaiTable
            var tableTugasSelesai = $("#tugasSelesaiTable").DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manajemen.kuliah.kelas.mahasiswa.tugas.selesai', encrypt($jadwal->id)) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'judul', name: 'judul' },
                    {
                        data: 'pertemuan',
                        name: 'pertemuan',
                        render: function(data, type, row) {
                            return "<span class='badge badge-primary rounded-circle' style='width:25px;height:25px;'><h6 class='m-0'>" +
                                data + "</h6></span>";
                        }
                    },
                    { data: 'nilai', name: 'nilai', },
                    { data: 'komentar', name: 'komentar', },
                    {
                        className: 'noPrint',
                        data: 'link_tugas',
                        name: 'link_tugas',
                        render: function(data, type, row) {
                            return "<a download href='" + data +
                                "' target='_blank' class='btn btn-sm btn-primary'>Download <i class='ml-1 fas fa-download'></i></a>";
                        }
                    },
                    { data: 'create', name: 'create', },
                ],
            });

            if ($("a[href='#listTugas'].nav-link").hasClass('active')) {
                $("#cetakTugasTable").on("click", function() {
                    tableTugas.button(0).trigger();
                });

                $("#refreshTugasTable").on("click", function() {
                    tableTugas.ajax.reload(null, false);
                });
            }

            $("a.nav-link").click(function() {
                if ($(this).attr('href') === '#listTugas') { // Jika tab "#listTugas" di-klik
                    $("#cetakTugasTable").show('fade').off("click").on("click", function() {
                        tableTugas.button(0).trigger();
                    });

                    $("#refreshTugasTable").show('fade').off("click").on("click", function() {
                        tableTugas.ajax.reload(null, false);
                    });

                    $("#cetakTugasSelesaiTable").hide('fade');
                    $("#refreshTugasSelesaiTable").hide('fade');
                } else { // Jika tab selain "#listTugas" di-klik
                    $("#cetakTugasTable").hide('fade');
                    $("#refreshTugasTable").hide('fade');

                    $("#cetakTugasSelesaiTable").show('fade').off("click").on("click", function() {
                        tableTugasSelesai.button(0).trigger();
                    });

                    $("#refreshTugasSelesaiTable").show('fade').off("click").on("click", function() {
                        tableTugasSelesai.ajax.reload(null, false);
                    });
                }
            });

            // untuk mengasih width 100% pada table tugas selesai
            $("#tugasSelesaiTable").css("width", "100%");
            tableTugasSelesai.columns.adjust().draw();

            $(document).on('click', '.send_btn', function (e) {
                e.preventDefault();

                let jadwal_id = $("#jadwal_id").val();
                let tugas_id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.kelas.mahasiswa.lihat.tugas', ['jadwalId', 'tugasId']) }}"
                        .replace('jadwalId', jadwal_id).replace('tugasId', tugas_id),
                    success: function (res) {
                        if (res.status == 200) {
                            $("#modalKirimTugas").modal('show');
                            $("#tugas_id").val(tugas_id);

                            var label = $("#send_file").next('.custom-file-label');

                            if (res.tugas_mhs != null) {
                                label.html(res.tugas_mhs.file_or_link);
                            } else {
                                label.html("Silahkan pilih file..");
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                                allowOutsideClick: false,
                            });
                        }
                    },
                });
            });

            $('#formKirimTugas').on('submit', function (e) {
                e.preventDefault();

                let jadwal_id = $("#jadwal_id").val();
                let tugas_id = $("#tugas_id").val();

                $.ajax({
                    type: $(this).attr('method'),
                    url: "{{ route('manajemen.kuliah.kelas.mahasiswa.store.tugas', ['jadwalId', 'tugasId']) }}"
                        .replace('jadwalId', jadwal_id).replace('tugasId', tugas_id),
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitSend').attr('disabled', true);
                        $('.submitSend').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('input.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.submitSend').removeAttr('disabled');
                        $('.submitSend').html('Kirim');
                    },
                    success: function (res) {
                        if (res.status == 500) {
                            if (res.error == 'validation') {
                                $.each(res.message, function (prefix, val) {
                                    $('span.' + prefix + '_error').text(val[0]);
                                    $('#send_' + prefix).addClass('is-invalid');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.message,
                                });
                            }
                        } else {
                            $("#modalKirimTugas").modal('hide');

                            if (res.changed == false) {
                                Toast.fire({
                                    icon: 'warning',
                                    title: res.message,
                                });
                            } else {

                                tableTugasSelesai.ajax.reload(null, false);

                                Toast.fire({
                                    icon: 'success',
                                    title: res.message,
                                });
                            }

                            $("#send_file").val("");
                            $("#send_file").next('.custom-file-label').html("Silahkan pilih file..");
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
            });

            // Show label file name
            $(document).on('change', '#send_file', function () {
                let fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@endpush
