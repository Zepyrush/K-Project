@extends('layouts.dashboard')

@section('title', 'Data Dosen')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline sticky">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="m-0 p-0 font-weight-bold ml-2">
                                <i class="fa fa-user-tie text-primary mr-1"></i> @yield('title')
                            </h5>
                            <div>
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalCreate">
                                    <i class="fas fa-plus mr-1"></i> Tambah
                                </button>
                                <button id="cetakTable" class="btn btn-primary btn-sm ml-1">
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
                    <div class="card-body table-responsive">
                        <table id="dosenTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dosen</th>
                                    <th>Kelas</th>
                                    <th>Matakuliah</th>
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

    @include("dashboard.admin.manage-users._modal._modal-dosen")

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

            $(document).on('change', 'input[type="file"]', function(event) {
                let fileName = $(this).val();

                if (fileName == undefined || fileName == "") {
                    $(this).next('.custom-file-label').html('Tidak ada gambar yang dipilih..')
                } else {
                    $(this).next('.custom-file-label').html(event.target.files[0].name);
                }
            });

            var table = $('#dosenTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    type: "GET",
                    url: "{{ route('manage.users.dosen.index') }}",
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    {
                        data: 'dosen',
                        name: 'dosen',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('width', '25%');
                        }
                    },
                    {
                        data: 'nama_kelas',
                        name: 'nama_kelas',
                        render: function(data, type, row) {
                            let badge = '';

                            $.each(data, function(key, val) {
                                badge += `<span class="badge badge-primary mr-1">${val},\t</span>`;
                            });

                            return badge;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('width', '20%');
                        }
                    },
                    {
                        data: 'nama_matkul',
                        name: 'nama_matkul',
                        render: function(data, type, row) {
                            let badge = '';

                            $.each(data, function(key, val) {
                                badge += `<span class="badge badge-primary mr-1">${val},\t</span>`;
                            });

                            return badge;
                        }
                    },
                    { className: 'noPrint', data: 'action', name: 'action', orderable: false, searchable: false },
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

            function initSelect2(id, placeholder, dropdownParent) {
                $(id).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(dropdownParent),
                });
            }

            initSelect2('#add_matkul', 'Pilih Matakuliah', '#modalCreate');
            initSelect2('#add_kelas', 'Pilih Kelas', '#modalCreate');

            // insert data
            $('#formAddDosen').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
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
                                localStorage.removeItem(`${noIndukUser}_modalCreate`);
                                localStorage.setItem(`${noIndukUser}_msgDosen`, 'sukses');
                                window.location.href = "{{ route('manage.users.user.index') }}"
                            } else {
                                $('#formAddDosen')[0].reset();
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
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            $('#modalCreate').on('hidden.bs.modal', function() {
                $("#add_kelas").select2("val", 'All');
                $("#add_matkul").select2("val", 'All');
            });

            initSelect2('#edit_kelas', 'Pilih Kelas', '#modalEdit');
            initSelect2('#edit_matkul', 'Pilih Matakuliah', '#modalEdit');

            // edit data show
            $("body").on('click', '.editBtn', function(e) {
                e.preventDefault();

                var id = $(this).attr('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('manage.users.dosen.show', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $('#modalEdit').modal('show');

                        $('#edit_id').val(id);

                        $.each(res.data, function(key, value) {
                            $('#modalEdit #edit_' + key).val(value);
                        });
                        $('#modalEdit #edit_foto').html(res.data.user.foto);

                        // foreach select2 kelas
                        var kelas = [];
                        $.each(res.data.kelas, function(key, value) {
                            kelas.push(value.id);
                        });
                        $('#edit_kelas').val(kelas).trigger('change');

                        // foreach select2 matkul
                        var matkuls = [];
                        $.each(res.data.matkuls, function(key, value) {
                            matkuls.push(value.id);
                        });
                        $('#edit_matkul').val(matkuls).trigger('change');
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melihat data ini",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // update data
            $('#formEditDosen').on("submit", function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();

                $.ajax({
                    url: "{{ route('manage.users.dosen.update', ':id') }}".replace(':id', id),
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
                                allowOutsideClick: false,
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
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // delete data show
            $("body").on('click', '.delBtn', function(e) {
                e.preventDefault();

                var id = $(this).attr('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('manage.users.dosen.show', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $('#modalDelete').modal('show');

                        $('#del_id').val(id);

                        $('#modalDelete #text_del').text(
                            `Apakah anda yakin akan menghapus Dosen dengan NIP\t
                            ${res.data.nip} \t(${res.data.nama}) ?`
                        );
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melihat data ini",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // delete data
            $('#formHapusDosen').on("submit", function(e) {
                e.preventDefault();

                let id = $('#del_id').val();

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('manage.users.dosen.delete', ':id') }}".replace(':id', id),
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
                                allowOutsideClick: false,
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
                                html: "Anda tidak memiliki akses untuk menghapus data ini",
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
