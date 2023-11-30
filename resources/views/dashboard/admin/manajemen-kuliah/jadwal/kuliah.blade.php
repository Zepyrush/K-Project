@extends('layouts.dashboard')

@section('title', 'Jadwal Kuliah')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline sticky">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="m-0 p-0 font-weight-bold ml-2">
                                <i class="fas fa-calendar-alt text-primary mr-1"></i> @yield('title')
                            </h5>
                            <div>
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalCreate">
                                    <i class="fas fa-plus mr-1"></i>
                                    Tambah
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
                        <div class="row justify-content-between">
                            {{-- filter --}}
                            <div class="col-md-4 col-6 mb_2">
                                <select id="filter_dosen" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($data_dosen as $d)
                                        <option value="{{ $d->nama }}">{{ $d->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-6 mb_2">
                                <select id="filter_kelas" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($data_kelas as $k)
                                        <option value="{{ $k->kode }}">{{ $k->kode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-6 ">
                                <select id="filter_matkul" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($data_matkul as $m)
                                        <option value="{{ $m->nama }}">{{ $m->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-hover" id="tableJadwal">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dosen</th>
                                    <th>Kelas</th>
                                    <th>Matakuliah</th>
                                    <th>Hari</th>
                                    <th>Jam Masuk</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.admin.manajemen-kuliah._modal._modal-jadwal-kuliah')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ============================ DATATABLE ============================ //

            let filterKelas = $('#filter_kelas').val(),
                filterMatkul = $('#filter_matkul').val(),
                filterDosen = $('#filter_dosen').val();

            let table = $('#tableJadwal').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : "{{ route('manajemen.kuliah.jadwal.admin.kuliah.index') }}",
                    data: function(d) {
                        d.filterKelas = filterKelas;
                        d.filterMatkul = filterMatkul;
                        d.filterDosen = filterDosen;

                        return d;
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'dosen_jadwal', name: 'dosen_jadwal'},
                    {data: 'kelas_jadwal', name: 'kelas_jadwal'},
                    {data: 'matkul_jadwal', name: 'matkul_jadwal'},
                    {data: 'hari', name: 'hari'},
                    {
                        data: 'started_at',
                        name: 'started_at',
                        render: function(data, type, row) {
                            let output;

                            row.ended_at == null ? output = data + ' WIB' :
                                output = data + ' - ' + row.ended_at + ' WIB';

                            return output;
                        }
                    },
                    {
                        className: 'noPrint', data: 'action',
                        name: 'action', orderable: false, searchable: false
                    },
                ]
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $('.filter').change(function() {
                filterKelas = $('#filter_kelas').val();
                filterMatkul = $('#filter_matkul').val();
                filterDosen = $('#filter_dosen').val();

                table.ajax.reload(null, false);
            });

            $('#refreshTable').click(function(e) {
                e.preventDefault();

                $('#filter_kelas').val('').trigger('change');
                $('#filter_matkul').val('').trigger('change');
                $('#filter_dosen').val('').trigger('change');

                table.ajax.reload(null, false);
            });

            // ============================= SELECT 2 ============================= //

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

            initSelect2('#add_dosen', 'Pilih Dosen', '#modalCreate');
            initSelect2('#add_matkul', 'Pilih Matakuliah', '#modalCreate');
            initSelect2('#add_kelas', 'Pilih Kelas', '#modalCreate');
            initSelect2('#add_hari', 'Pilih Hari', '#modalCreate');
            initSelect2("#filter_kelas", "Filter Kelas");
            initSelect2("#filter_matkul", "Filter Matakuliah");
            initSelect2("#filter_dosen", "Filter Dosen");

            // ============================= BUAT DATA ============================= //

            $('#formAddJadwal').on('submit', function(e) { // inserting data
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
                        $('.submitAdd').html('Tambah');
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

                            $('#formAddJadwal')[0].reset();
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
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            });

            function dropdownDisableCreate() { // ini untuk mengosongkan dropdown
                $("#add_kelas").attr('disabled', 'disabled');
                $("#add_matkul").attr('disabled', 'disabled');
                $("#add_kelas").empty();
                $("#add_matkul").empty();
            }

            function dropdownNormalCreate() { // ini untuk menormalkan dropdown
                $("#add_kelas").attr('disabled', false);
                $("#add_matkul").attr('disabled', false);
                $("#add_kelas").empty();
                $("#add_matkul").empty();
            }

            // dinamis dropdown dosen
            $("#add_dosen").on('change', function() {
                let dosen_id = $(this).val();

                (dosen_id) ? appendCreate(dosen_id) : dropdownDisableCreate();
            });

            function appendCreate(dosen_id) { // ini untuk mengisi dropdown
                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.dropdown', ':id') }}"
                        .replace(':id', dosen_id),
                    dataType: "JSON",
                    success: function(res) {
                        if (res) {
                            dropdownNormalCreate();

                            // ini untuk mengisi dropdown
                            $("#add_kelas").append('<option value="">Pilih Kelas</option>');
                            $.each(res.kelas, function(key, value) {
                                $("#add_kelas").append(
                                    `<option value="${value.id}">${value.kode}</option>`
                                );
                            });

                            // ini untuk mengisi dropdown matkul
                            $("#add_matkul").append(`<option value="">Pilih Matakuliah</option>`);
                            $.each(res.matkul, function(key, value) {
                                $("#add_matkul").append(
                                    `<option value="${value.id}">${value.nama}</option>`
                                );
                            });
                        } else {
                            dropdownDisableCreate();
                        }
                    }
                });
            }

            // modal close reset form
            $('#modalCreate').on('hidden.bs.modal', function() {
                $("#add_kelas").select2("val", ' ');
                $("#add_matkul").select2("val", ' ');
                $("#add_dosen").select2("val", ' ');
                $("#add_hari").select2("val", ' ');
            });

            // ============================= EDIT DATA ============================= //

            $(document).on('click', '.edit_btn', function() { // show modal edit

                var id = $(this).attr('id');

                $.ajax({
                    method: 'GET',
                    url: "{{ route('manajemen.kuliah.jadwal.admin.kuliah.show', ':id') }}".replace(':id', id),
                    success: function(res) {
                        if (res.status == 200) {
                            $("#modalEdit").modal('show');

                            let data = res.data;
                            let absens = data.absens;

                            if (absens.length >= 1) {
                                $("#modalEdit").find(".modal-footer")
                                    .addClass("justify-content-between").find("#resetData")
                                    .show("fade");

                                $("#resetData").val(id);
                            }

                            // show data value
                            $('#edit_id').val(id);
                            $('#edit_hari').val(data.hari);
                            $('#edit_started').val(data.started_at);
                            $('#edit_ended').val(data.ended_at);
                            $('#edit_dosen').val(data.dosen.id);

                            // Select2 form edit
                            initSelect2('#edit_dosen', 'Pilih Dosen', '#modalEdit');
                            initSelect2('#edit_hari', 'Pilih Hari', '#modalEdit');

                            dropdownEditDosen(data); // menampilkan data dosen
                        } else {
                            $("#modalEdit").modal('hide');

                            $(document).find('span.error-text').text('');
                            $(document).find('input.form-control')
                                .removeClass('is-invalid');

                            Swal.fire({
                                icon: 'warning',
                                html: response.message,
                            });
                        } // end if
                    }, // end success ajax show
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }

                }); // end ajax

            }); // end edit button

            // updating data to database
            $('#formEditJadwal').on("submit", function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();

                $.ajax({
                    url: "{{ route('manajemen.kuliah.jadwal.admin.kuliah.update', ':id') }}"
                        .replace(':id', id),
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
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }); // end update data

            function dropdownEditDosen(data) { // function dropdown edit dosen
                // menampilkan data dosen
                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.dropdownEdit', ':id') }}"
                        .replace(':id', data.dosen.id),
                    dataType: "JSON",
                    success: function(response) {

                        if (response) {
                            $("#edit_kelas").empty();
                            $("#edit_matkul").empty();

                            $("#dropdownDinamis").html(response);

                            initSelect2('#edit_matkul', 'Pilih Matakuliah', '#modalEdit');
                            initSelect2('#edit_kelas', 'Pilih Kelas', '#modalEdit');

                            $('#edit_kelas').val(data.kelas.id).trigger('change');
                            $('#edit_matkul').val(data.matkul.id).trigger('change');

                        } else {
                            $("#edit_kelas").empty();
                            $("#edit_matkul").empty();
                        }
                    } // end success
                });

                // ketika inputan select dosen diubah
                $("#edit_dosen").on('change', function() {
                    let dosen_id = $(this).val();

                    if (dosen_id) {
                        appendEdit(dosen_id);
                    } else {
                        $("#edit_kelas").empty();
                        $("#edit_matkul").empty();
                    }
                });
            } // end function dropdown edit dosen

            // function menampilkan data dropdown matkul dan kelas
            function appendEdit(dosen_id) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.dropdown', ':id') }}"
                        .replace(':id', dosen_id),
                    dataType: "JSON",
                    success: function(res) {
                        if (res) {
                            $("#edit_kelas").empty();
                            $("#edit_matkul").empty();

                            $.each(res.kelas, function(key, value) {
                                $("#edit_kelas").append(
                                    `<option value="${value.id}">${value.kode}</option>`
                                );
                            });

                            $.each(res.matkul, function(key, value) {
                                $("#edit_matkul").append(
                                    `<option value="${value.id}">${value.nama}</option>`
                                );
                            });
                        } else {
                            $("#edit_kelas").empty();
                            $("#edit_matkul").empty();
                        }
                    } // end success
                }); // end ajax
            } // end function appendEdit

            // modal edit reset
            $("#modalEdit").on("hidden.bs.modal", function () {
                $("#modalEdit").find(".modal-footer")
                    .removeClass("justify-content-between").find("#resetData")
                    .hide("fade");
                $("#resetData").val("");
            });

            // ============================= HAPUS DATA ============================= //

            $(document).on('click', '.del_btn', function(e) {// show modal delete
                e.preventDefault();

                var id = $(this).attr('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.kuliah.show', ':id') }}"
                        .replace(':id', id),
                    success: function(res) {
                        $('#modalDelete').modal('show');

                        let data = res.data;
                        $('#modalDelete #text_del').text(
                            `Apakah anda yakin ingin menghapus Jadwal Kuliah Hari ${data.hari},
                            \tJam ${data.started_at}\t s/d ${data.ended_at}\t WIB ?`
                        );
                        $('#del_id').val(id);

                        $("#kelas_id").val(data.kelas.id);
                        $("#matkul_id").val(data.matkul.id);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            });

            // deleting data with ajax
            $('#formHapusJadwal').on("submit", function(e) {
                e.preventDefault();

                let id = $('#del_id').val();

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.kuliah.delete', ':id') }}"
                        .replace(':id', id),
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": id,
                        "kelas_id": $("#kelas_id").val(),
                        "matkul_id": $("#matkul_id").val(),
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
                        if (res.status == 400) {
                            Swal.fire({
                                icon: 'error',
                                title: res.title,
                                html: res.message,

                            });
                        } else {
                            $('#modalDelete').modal('hide');

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
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            });

            // ============================= RESET DATA ============================= //

            $("#resetData").click(function (e) { // button reset datas
                e.preventDefault();

                let id = $(this).val();
                let text = `
                    <span class='font-weight-bold'>APAKAH ANDA YAKIN?</span> <hr>
                    Jika anda mereset jadwal kuliah ini,\tmaka semua data yang
                    berkaitan dengan jadwal kuliah ini akan dihapus.\tSeperti absensi dan tugas.
                `;

                Swal.fire({
                    icon: 'warning',
                    html: text,
                    allowOutsideClick: false,
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        resetData(id);
                    }
                });
            });

            function resetData(id) { // function reset data
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.kuliah.reset', ':id') }}"
                        .replace(':id', id),
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function (res) {
                        if (res.status == 200) {
                            $("#modalEdit").modal('hide');

                            Toast.fire({
                                icon: 'success',
                                title: res.message,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            });
                        }
                    }
                });
            }

        }); // end document ready
    </script>
@endpush
