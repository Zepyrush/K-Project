@extends('layouts.dashboard')

@section('title', 'Tugas ' . $jadwal->matkul->nama . ' - Kelas ' . $jadwal->kelas->kode)

@section('content')
    <div class="container-fluid">

        <div class="row sticky">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            @if ($jadwalDiBuka)
                                <a href="javascript:void(0)" class="btn btn-primary btn-sm btn-back">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            @else
                                <a href="{{ route('manajemen.kuliah.jadwal.dosen.kuliah.index') }}"
                                     class="btn btn-primary btn-sm btn-back">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </a>
                            @endif

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
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center">
                            <button id="cetakTable" class="btn btn-primary btn-sm">
                                <i class="fas fa-print mr-1"></i> Cetak
                            </button>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="tugasTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Pertemuan</th>
                                    <th>Deadline</th>
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

    {{-- Modal Edit Tugas --}}
    <div class="modal fade" id="modalEditTugas" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <h5 class="modal-title font-weight-bold ml-2">Form - Edit Tugas</h5>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="formEditTugas" action="#" method="POST" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="jadwal" value="{{ encrypt($jadwal->id) }}">
                    <input type="hidden" name="kelas_id" value="{{ encrypt($jadwal->kelas->id) }}">
                    <input type="hidden" name="matkul_id" value="{{ encrypt($jadwal->matkul->id) }}">
                    <input type="hidden" id="edit_id">

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="pertemuan">Pertemuan</label>
                                    <input type="number" class="form-control pertemuan" id="tugas_pertemuan"
                                        name="pertemuan" placeholder="Masukkan pertemuan tugas disini" readonly>
                                    <span class="invalid-feedback d-block error-text tugas_pertemuan_error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="tipe">Tipe Tugas</label>
                                    <select id="tugas_tipe" name="tipe" class="form-control">
                                        <option value=""></option>
                                        <option value="file">File</option>
                                        <option value="link">Link</option>
                                    </select>
                                    <span class="invalid-feedback d-block error-text tugas_tipe_error"></span>
                                </div>

                                <div class="form-group" id="tugas_link" style="display: none;">
                                    <label for="file_or_link">Link Tugas</label>
                                    <input type="text" class="form-control" id="linkTugas" name="file_or_link"
                                        placeholder="Masukkan link untuk soal tugas">
                                    <span class="invalid-feedback d-block error-text tugas_file_or_link_error"></span>
                                </div>

                                <div class="form-group" id="tugas_file" style="display: none;">
                                    <label for="file_or_link">File Tugas</label>
                                    <div class="custom-file">
                                        <input id="fileTugas" type="file" name="file_or_link" class="custom-file-input">
                                        <label class="custom-file-label" for="file_or_link">
                                            Cari file soal tugas..
                                        </label>
                                    </div>
                                    <span class="invalid-feedback d-block error-text tugas_file_or_link_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="pengumpulan">Dealine Tugas</label>
                                    <input type="datetime-local" class="form-control" id="tugas_pengumpulan"
                                        name="pengumpulan" placeholder="Masukkan deadline tugas">
                                    <span class="invalid-feedback d-block error-text tugas_pengumpulan_error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="judul">Judul Tugas</label>
                                    <input type="text" class="form-control judul" id="tugas_judul" name="judul"
                                        placeholder="Masukkan judul tugas disini">
                                    <span class="invalid-feedback d-block error-text tugas_judul_error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi Tugas</label>
                                    <textarea name="deskripsi" id="tugas_deskripsi" class="form-control" rows="3"
                                        placeholder="Masukkan deskripsi tugas disini"></textarea>
                                    <span class="invalid-feedback d-block error-text tugas_deskripsi_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning submitTugas" disabled>Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal delete --}}
    <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="deleteTugas"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <form action="#" method="POST" id="formHapusTugas">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body">
                        <input id="del_id" type="hidden" name="id">
                        <p id="text_del"></p>
                    </div>

                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-danger btnDelete">
                            Hapus
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // cek apakah dari jadwal masuknya ke materinya
            if (localStorage.getItem(`${noIndukUser}_jadwal`) == 'true') {
                $("a.btn-back").attr("href", "{{ route('manajemen.kuliah.jadwal.dosen.kuliah.index') }}");
                $("a.btn-back").click(function() {
                    localStorage.removeItem(`${noIndukUser}_jadwal`);
                });
                $("a.nav-link").click(function () {
                    localStorage.removeItem(`${noIndukUser}_jadwal`);
                });
                // cek apakah dari dashboard masuknya ke materinya
            } else if (localStorage.getItem(`${noIndukUser}_fromDashboard`) == "true") {
                $("a.btn-back").attr("href", "{{ route('dosen.dashboard') }}");
                $("a.btn-back").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
                $("a").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
            } else {
                $("a.btn-back").attr("href", "{{ route('manajemen.kuliah.kelas.dosen.index', encrypt($jadwal->id)) }}");
            }

            var table = $('#tugasTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manajemen.kuliah.tugas.dosen.index', encrypt($jadwal->id)) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'judul', name: 'judul' },
                    {
                        data: 'deskripsi',
                        name: 'deskripsi',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('width', '45%');
                        }
                    },
                    { data: 'pertemuan', name: 'pertemuan' },
                    {
                        data: 'deadline',
                        name: 'deadline',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('width', '17%');
                        }
                    },
                    { className: 'noPrint', data: 'action', name: 'action', orderable: false, searchable: false},
                ],
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            // show modal edit tugas
            $(document).on('click', '.edit_btn', function(e) {
                e.preventDefault();

                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.tugas.dosen.edit', ':id') }}".replace(':id', id),
                    success: function(res) {

                        if (res.status == 404) {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                                allowOutsideClick: false,
                            });
                        } else {
                            $('#modalEditTugas').modal('show');

                            $("#edit_id").val(id);

                            $.each(res, function (key, val) { // looping object
                                if (key != 'id' && key != 'file_or_link') { // jika bukan id dan file_or_link
                                    (key == 'tipe') ?
                                        $("#tugas_tipe").val(val).trigger('change') :
                                        $("#tugas_" + key).val(val);
                                }
                            });

                            $(document).on('change', 'input[type="file"]', function(event) {
                                let fileName = $(this).val();

                                (fileName == undefined || fileName == "") ?
                                    $(this).next('.custom-file-label').html(res.file_or_link) :
                                    $(this).next('.custom-file-label').html(event.target.files[0].name);
                            });

                            function select2Edit() {
                                $("#tugas_tipe").select2({
                                    placeholder: "Pilih Tipe Tugas",
                                    allowClear: true,
                                    width: '100%',
                                    dropdownParent: $('#modalEditTugas'),
                                });
                            }

                            select2Edit();

                            if ($('#tugas_tipe option:selected').val() == 'file') {
                                $("#tugas_file").show('fade');
                                $("#tugas_file label.custom-file-label").html(res.file_or_link);
                                $(".submitTugas").removeAttr('disabled');
                            } else {
                                $("#tugas_link").show('fade');
                                $("#tugas_link input").val(res.file_or_link);
                                $(".submitTugas").removeAttr('disabled');
                            }

                            $(document).on('change', '#tugas_tipe', function() {
                                const selectedVal = $('#tugas_tipe option:selected').val();

                                if (selectedVal == 'file') {
                                    $("#tugas_file").show('fade');
                                    $("#tugas_link").hide('fade');
                                    $(".submitTugas").removeAttr('disabled');
                                } else if (selectedVal == 'link') {
                                    $("#tugas_file").hide('fade');
                                    $("#tugas_link").show('fade');
                                    $(".submitTugas").removeAttr('disabled');
                                } else {
                                    select2Edit();
                                    $("#tugas_file").hide('fade');
                                    $("#tugas_link").hide('fade');
                                    $(".submitTugas").attr('disabled', true);
                                }
                            });
                        }
                    }
                });
            });

            // reset modal edit tugas
            $("#modalEditTugas").on('hidden.bs.modal', function() {
                $("#formEditTugas")[0].reset();
                $("#tugas_tipe").val(null).trigger('change');
                $("#tugas_file").hide('fade');
                $("#tugas_link").hide('fade');
                $(".submitTugas").attr('disabled', true);
                $(document).find('.form-control').removeClass('is-invalid');
            });

            // update tugas
            $("#formEditTugas").on('submit', function(e) {
                e.preventDefault();

                let id = $("#edit_id").val();

                $.ajax({
                    type: $(this).attr('method'),
                    url: "{{ route('manajemen.kuliah.tugas.dosen.update', ':id') }}".replace(':id',
                        id),
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitTugas').attr('disabled', true);
                        $('.submitTugas').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.submitTugas').removeAttr('disabled');
                        $('.submitTugas').html('Update');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            if (res.validation == true) {
                                $.each(res.errors, function(key, val) {
                                    $('span.tugas_' + key + '_error').text(val[0]);
                                    $("#tugas_" + key).addClass('is-invalid');
                                    $("#linkTugas").addClass('is-invalid');
                                    $("#fileTugas").addClass('is-invalid');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.message,
                                });
                            }
                        } else {
                            $('#modalEditTugas').modal('hide');

                            table.ajax.reload(null, false);

                            Toast.fire({
                                icon: 'success',
                                title: res.message,
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // show modal delete tugas
            $(document).on('click', '.del_btn', function(e) {
                e.preventDefault();
                $("#modalDelete").modal('show');

                let id = $(this).val();
                let judul = $(this).data('judul');

                $("#del_id").val(id);
                $("#text_del").html("Apakah anda yakin ingin menghapus tugas \"" + judul + "\" ?");
            });

            // delete tugas
            $('#formHapusTugas').on('submit', function(e) {
                e.preventDefault();

                let id = $("#del_id").val();

                $.ajax({
                    type: $(this).attr('method'),
                    url: "{{ route('manajemen.kuliah.tugas.dosen.delete', ':id') }}".replace(':id',
                        id),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.btnDelete').attr('disabled', true);
                        $('.btnDelete').html('<i class="fas fa-spin fa-spinner"></i>');
                    },
                    complete: function() {
                        $('.btnDelete').removeAttr('disabled');
                        $('.btnDelete').html('Hapus');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            });
                        } else {
                            $('#modalDelete').modal('hide');

                            table.ajax.reload(null, false);

                            Toast.fire({
                                icon: 'success',
                                title: res.message,
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });
        });
    </script>
@endpush
