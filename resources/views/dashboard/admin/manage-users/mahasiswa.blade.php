@extends('layouts.dashboard')

@section('title', 'Data Mahasiswa')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline sticky">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="m-0 p-0 font-weight-bold ml-2">
                                <i class="fa fa-user-graduate text-primary mr-1"></i>
                                @yield('title')
                            </h5>

                            <div>
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalCreate">
                                    <i class="fas fa-plus mr-1"></i>
                                    Tambah
                                </button>
                                <button id="cetakTable" class="btn btn-primary btn-sm">
                                    <i class="fas fa-print mr-1"></i> Cetak
                                </button>
                                <button id="refreshTable" class="btn btn-warning btn-sm"
                                    data-toggle="tooltip" title="Refresh Table">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="row">
                            <div class="col-md-4 col-12 mb_2">
                                <select id="filterKelas" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($kelas as $item)
                                        <option value="{{ $item->kode }}">{{ $item->kode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-12">
                                <select id="filterFakultas" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($fakultas as $item)
                                        <option value="{{ $item->nama }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="mahasiswaTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Mahasiswa</th>
                                    <th>Kelas</th>
                                    <th>Fakultas</th>
                                    <th>Tanggal Daftar</th>
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

    @include("dashboard.admin.manage-users._modal._modal-mahasiswa")
    @include("dashboard.admin.manage-users._modal._modal-import-mahasiswa")
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (localStorage.getItem(`${noIndukUser}_modalCreate`) == 'open') {
                $('#modalCreate').modal('show');
            }

            // Show name file
            $(document).on('change', 'input[type="file"]', function(event) {
                let fileName = $(this).val();

                if (fileName == undefined || fileName == "") {
                    $(this).next('.custom-file-label').html('Tidak ada gambar yang dipilih..')
                } else {
                    $(this).next('.custom-file-label').html(event.target.files[0].name);
                }
            });

            // global variable
            let filterKelas = $('#filterKelas').val();
            let filterFakultas = $('#filterFakultas').val();

            // =====================================================================================================
            // DATATABLE
            // =====================================================================================================

            var table = $('#mahasiswaTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: 'GET',
                    url: "{{ route('manage.users.mahasiswa.index') }}",
                    data: function(d) {
                        d.f_kelas = filterKelas;
                        d.f_fakultas = filterFakultas;
                        return d;
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'mahasiswa', name: 'mahasiswa', },
                    { data: 'kelas_kode', name: 'kelas_kode' },
                    { data: 'fakultas_nama', name: 'fakultas_nama' },
                    { data: 'register_at', name: 'register_at' },
                    { className: 'noPrint', data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $("#refreshTable").on("click", function(e) {
                e.preventDefault();

                $("#filterKelas").val("").trigger("change");
                $("#filterFakultas").val("").trigger("change");

                table.ajax.reload(null, false);
            });

            // filter
            $('.filter').change(function() {
                filterKelas = $('#filterKelas').val();
                filterFakultas = $('#filterFakultas').val();
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

            initSelect2("#filterKelas", "Filter Kelas Mahasiswa");
            initSelect2("#filterFakultas", "Filter Fakultas Mahasiswa");
            initSelect2("#add_fakultas", "Pilih Fakultas", "#modalCreate");
            initSelect2("#add_kelas", "Pilih Kelas", "#modalCreate");

            // =====================================================================================================
            // INSERT DATA
            // =====================================================================================================

            $('#formAddMahasiswa').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitAdd').attr('disabled', true);
                        $('.submitAdd').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass(
                            'is-invalid');
                    },
                    complete: function() {
                        $('.submitAdd').removeAttr('disabled');
                        $('.submitAdd').html('Simpan');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            $.each(res.errors, function(key, val) {
                                $('span.' + key + '_error').text(val[0]);
                                $("#add_" + key).addClass('is-invalid');
                            });
                        } else if (res.status == 401) {
                            Swal.fire({
                                icon: 'error',
                                title: res.title,
                                html: res.message,
                            });
                        } else {
                            if (localStorage.getItem(`${noIndukUser}_modalCreate`) == 'open') {
                                // LocalStorage ini digunakan untuk membuat user mahasiswa lewat halaman pengguna
                                localStorage.removeItem(`${noIndukUser}_modalCreate`);
                                localStorage.setItem('msgMahasiswa', 'sukses');

                                window.location.href =
                                "{{ route('manage.users.user.index') }}";
                            } else {
                                $('#formAddMahasiswa')[0].reset();
                                $('#modalCreate').modal('hide');

                                table.ajax.reload(null, false);

                                setTimeout(function() {
                                    Toast.fire({
                                        icon: 'success',
                                        title: res.message
                                    });
                                }, 500);
                            }
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

            // RESET MODAL CREATE
            $('#modalCreate').on('hidden.bs.modal', function() {
                $("#add_kelas").select2("val", ' ');
                $("#add_fakultas").select2("val", ' ');
            });

            // INIT SELECT2
            initSelect2("#edit_kelas", "Pilih Kelas", "#modalEdit");
            initSelect2("#edit_fakultas", "Pilih Fakultas", "#modalEdit");

            // =====================================================================================================
            // SHOW MODAL EDIT
            // =====================================================================================================

            $("body").on('click', '.edit_btn', function(e) {
                e.preventDefault();

                var id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manage.users.mahasiswa.show', ';id') }}".replace(';id', id),
                    success: function(res) {
                        if (res.status == 200) {
                            let data = res.data;
                            $('#modalEdit').modal('show');

                            $('#edit_id').val(id);
                            $('#edit_nim').val(data.nim);
                            $('#edit_nama').val(data.nama);
                            $('#edit_email').val(data.email);
                            $('#edit_foto').html(data.user.foto);
                            $('#edit_kelas').val(data.kelas.id).trigger('change');
                            $('#edit_fakultas').val(data.fakultas.id).trigger(
                                'change');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini",
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // =====================================================================================================
            // UPDATE DATA
            // =====================================================================================================

            $('#formEditMahasiswa').on("submit", function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();

                $.ajax({
                    url: "{{ route('manage.users.mahasiswa.update', ';id') }}".replace(';id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('.submitEdit').attr('disabled', true);
                        $('.submitEdit').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('input.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.submitEdit').removeAttr('disabled');
                        $('.submitEdit').html('Update');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            $.each(res.errors, function(key, val) {
                                $('span.edit_' + key + '_error').text(val[0]);
                                $("#edit_" + key).addClass('is-invalid');
                            });
                        } else if (res.status == 401) {
                            Swal.fire({
                                icon: 'error',
                                title: res.title,
                                html: res.message,
                            });
                        } else {
                            $('#modalEdit').modal('hide');

                            table.ajax.reload(null, false);

                            setTimeout(function() {
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                            }, 500);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk mengedit data ini",
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // =====================================================================================================
            // SHOW MODAL DELETE
            // =====================================================================================================

            $("body").on('click', '.del_btn', function(e) {
                e.preventDefault();
                $('#modalDelete').modal('show');

                var id = $(this).val();
                var name = $(this).data('name');

                $('#del_id').val(id);
                $('#text_del').text(
                    `Apakah anda yakin ingin menghapus
                    data mahasiswa dengan nama \t"${name}" ?`
                );
            });

            // =====================================================================================================
            // DELETE DATA
            // =====================================================================================================

            $('#formHapusMahasiswa').on("submit", function(e) {
                e.preventDefault();

                let id = $('#del_id').val();

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('manage.users.mahasiswa.delete', ';id') }}".replace(';id', id),
                    data: {
                        "id": id,
                        "_token": "{{ csrf_token() }}",
                    },
                    beforeSend: function() {
                        $('.btnDelete').attr('disabled', true);
                        $('.btnDelete').html('<i class="fas fa-spin fa-spinner"></i>');
                    },
                    complete: function() {
                        $('.btnDelete').removeAttr('disabled');
                        $('.btnDelete').html('Hapus');
                    },
                    success: function(res) {
                        if (res.status == 401) {
                            Swal.fire({
                                icon: 'error',
                                title: res.title,
                                html: res.message,
                            });
                        } else {
                            $('#modalDelete').modal('hide');

                            table.ajax.reload(null, false);

                            // SET TIMEOUT UNTUK MENUNGGU MODAL HIDE
                            setTimeout(function() {
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                            }, 500);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini",
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            }); // End of Delete Data

            // =====================================================================================================
            // IMPORT DATA
            // =====================================================================================================

            var modal = $('.modal-import');
            var table = $('#mahasiswaTable').DataTable();
            var form = modal.find('form');
            var submit = form.find('button[type=submit]');
            var btnmodal = '.btn-import';

            $(btnmodal).on('click', function() {
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function () {
                form.find(".error-text").text("");
                form.find(".custom-file-input").removeClass("is-invalid");
                form.find(".custom-file-label").html("Pilih File");
                form.trigger('reset');
            });

            form.submit(function (e) {
                e.preventDefault();

                var formData = new FormData(this),
                    url = "{{ route('manage.users.mahasiswa.import') }}";

                $.ajax({
                    method: 'POST',
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'JSON',
                    beforeSend: function () {
                        modal.find('[data-dismiss="modal"]').attr("disabled", true);
                        form.find(".error-text").text("");
                        form.find(".custom-file-input").removeClass("is-invalid");
                        submit.attr("disabled", true);
                        submit.html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                    },
                    complete: function () {
                        modal.find('[data-dismiss="modal"]').attr("disabled", false);
                        submit.attr("disabled", false);
                        submit.html('Import');
                    },
                    success: function (res) {
                        if (res.status == 400) {
                            if (res.val == true) {
                                $.each(res.errors, function (key, value) {
                                    form.find("#import-" + key).addClass("is-invalid");
                                    form.find("#error-" + key).text(value);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.message,
                                });
                            }
                        } else {
                            Toast.fire({
                                icon: 'success',
                                title: res.message,
                            });

                            table.ajax.reload();
                            modal.modal('hide');
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            });

        }); //  End of Document Ready
    </script>
@endpush
