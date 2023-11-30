@extends('layouts.dashboard')

@section('title', 'Data Pengguna')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline sticky">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="m-0 p-0 font-weight-bold ml-2">
                                <i class="fa fa-users text-primary mr-1"></i> @yield('title')
                            </h5>
                            {{-- dropdown --}}
                            <div>
                                <button class="btn btn-success btn-sm" type="button" data-toggle="modal"
                                    data-target="#modalPilihan">
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
                    <div class="card-header p-2">
                        <div class="row">
                            <div class="col-md-4 col-6">
                                <select id="filterPosisi" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($role_filters as $item)
                                        <option value="{{ ucfirst($item->name) }}">{{ ucfirst($item->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="userTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Pengguna</th>
                                    <th>Nomer Induk</th>
                                    <th>Posisi Pengguna</th>
                                    <th>Terakhir dilihat</th>
                                    <th>Status</th>
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

    @include("dashboard.admin.manage-users._modal._modal-admin")
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // csrf token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Show name file
            $(document).on('change', 'input[type="file"]', function(event) {
                let fileName = $(this).val();

                if (fileName == undefined || fileName == "") {
                    $(this).next('.custom-file-label').html('Tidak ada gambar yang dipilih..')
                } else {
                    $(this).next('.custom-file-label').html(event.target.files[0].name);
                }
            });

            /**
             * Section ini untuk membuat data user seperti mahasiswa dan dosen-
             * ketika dibuatnya lewat halaman ini.
             *
             * jadi algoritma nya adalah ketika kamu menekan tombol mahasiswa atau dosen-
             * dimodal pilihan, tombol akan merediect ke halaman mahasiswa atau dosen sesuai-
             * dengan pilihan yang kamu, nah ketika disimpan maka akan ada session yang-
             * akan menyimpan pesan sukses, habis itu akan meredirect kembali kehalaman ini.
             * */
            if (localStorage.getItem(`${noIndukUser}_msgMahasiswa`) == 'sukses') {
                localStorage.removeItem(`${noIndukUser}_msgMahasiswa`);

                Toast.fire({
                    icon: 'success',
                    title: 'Data Mahasiswa berhasil ditambahkan'
                });
            } else if (localStorage.getItem(`${noIndukUser}_msgDosen`) == 'sukses') {
                localStorage.removeItem(`${noIndukUser}_msgDosen`);

                Toast.fire({
                    icon: 'success',
                    title: 'Data Dosen berhasil ditambahkan'
                });
            }

            $('.dosenPage').click(function(e) {
                e.preventDefault();
                $("#modalPilihan").modal('hide');
                localStorage.setItem(`${noIndukUser}_modalCreate`, 'open');
                window.location.href = "{{ route('manage.users.dosen.index') }}";
            });

            $('.mahasiswaPage').click(function(e) {
                e.preventDefault();
                $("#modalPilihan").modal('hide');
                localStorage.setItem(`${noIndukUser}_modalCreate`, 'open');
                window.location.href = "{{ route('manage.users.mahasiswa.index') }}";
            });

            $("[data-target='#modalCreate']").click(function () {
                $("#modalPilihan").modal('hide');
            });

            // global variable
            let role_filter = $('#filterPosisi').val();

            // datatable
            var table = $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                bLengthChange: true,
                ajax: {
                    url: "{{ route('manage.users.user.index') }}",
                    type: "GET",
                    data: function(d) {
                        d.role = role_filter;
                        return d;
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'user', name: 'user'},
                    {data: 'no_induk', name: 'nomer_induk'},
                    {data: 'role_names', name: 'role'},
                    {data: 'last_seen', name: 'last_seen'},
                    {data: 'status', name: 'status'},
                    {
                        className: 'noPrint',
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('width', '15%');
                        }
                    },
                ]
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $("#refreshTable").on("click", function(e) {
                e.preventDefault();

                $("#filterPosisi").val("").trigger('change');
                table.ajax.reload(null, false);
            });

            // filter
            $(".filter").on('change', function() {
                role_filter = $('#filterPosisi').val();
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

            initSelect2("#filterPosisi", "Filter Posisi Pengguna");
            initSelect2("#add_role", "Pilih Role User", "#modalCreate");
            initSelect2("#edit_role", "Pilih Role User", "#modalEdit");

            $('#modalCreate').on('hidden.bs.modal', function() {
                $("#add_role").select2("val", ' ');
            });

            // insert data
            $('#formAddUser').on('submit', function(e) {
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
                                allowOutsideClick: false,
                            });
                        } else {

                            $('#add_role').select2('val', ' ');
                            $('#formAddUser')[0].reset();
                            $('#modalCreate').modal('hide');

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
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // edit data show
            $(document).on('click', '.editBtn', function(e) {
                e.preventDefault();

                let id = $(this).attr('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('manage.users.user.show', ':id') }}".replace(':id', id),
                    success: function(res) {
                        if (res.status == 200) {
                            $('#modalEdit').modal('show');

                            let data = res.data;

                            $('#edit_id').val(id);
                            $('#edit_no_induk').val(data.no_induk);
                            $('#edit_name').val(data.name);
                            $('#edit_email').val(data.email);
                            $('#edit_role').val(data.roles[0].id).trigger('change');
                            $('#edit_foto').html(data.foto);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                                allowOutsideClick: false,
                            });
                        }
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
            $('#formEditUser').on("submit", function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();

                $.ajax({
                    url: "{{ route('manage.users.user.update', ':id') }}".replace(':id', id),
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

            // show delete modal
            $(document).on('click', '.delBtn', function(e) {
                e.preventDefault();
                $('#modalDelete').modal('show');

                let id = $(this).attr('id');
                let noInduk = $(this).attr('data-noInduk');
                let name = $(this).attr('data-name');

                $('#del_id').val(id);
                $('#text_del').append(" " + noInduk + " (" + name + ") ?");
            });

            // delete data
            $("#formHapusUser").on('submit', function(e) {
                e.preventDefault();

                let id = $('#del_id').val();

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('manage.users.user.delete', ':id') }}".replace(':id', id),
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
                        $('#modalDelete').modal('hide');

                        if (res.status == 200) {
                            table.ajax.reload(null, false);

                            setTimeout(function() {
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                            }, 500);

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: res.title ?? 'Gagal',
                                html: res.message,
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melihat data ini",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" +
                                thrownError);
                        }
                    }
                });
            });

            // reset modal
            $('#modalDelete').on('hidden.bs.modal', function() {
                $('#text_del').text('Apakah anda yakin akan menghapus pengguna dengan Nomer Induk ');
            });
        });
    </script>
@endpush
