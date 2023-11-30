@extends('layouts.dashboard')

@section('title', 'Jadwal Ujian')

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
                                <button class="btn btn-success btn-sm mr-1" data-toggle="modal" data-target="#modalCreate">
                                    <i class="fas fa-plus mr-1"></i>
                                    Tambah
                                </button>
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
                    <div class="card-header p-2">
                        <div class="row justify-content-between">
                            {{-- filter --}}
                            <div class="col-md-4 col-6 mb_2">
                                <select id="filter_dosen" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($jadwals->unique('dosen_id') as $jadwal)
                                        <option value="{{ $jadwal->dosen->nama }}">{{ $jadwal->dosen->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-6 mb_2">
                                <select id="filter_kelas" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($jadwals->unique('kelas_id') as $jadwal)
                                        <option value="{{ $jadwal->kelas->kode }}">{{ $jadwal->kelas->kode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-6 ">
                                <select id="filter_matkul" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($jadwals->unique('matkul_id') as $jadwal)
                                        <option value="{{ $jadwal->matkul->nama }}">{{ $jadwal->matkul->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">

                        <div class="alert card card-outline card-primary alert-dismissible in-table info fade show"
                            role="alert">
                            <strong>NOTE: </strong>
                            <ul class="info-ul">
                                <li>
                                    Jika status ujiannya <span class="badge badge-secondary">draft</span>
                                    berarti ujian dengan jadwal tersebut belum dibuat oleh dosen.
                                </li>
                                <li class="mt-2">
                                    Jika di actionnya ada tombol reset, maka ujian tersebut adalah parent dari
                                    kelas & matakuliah yang sama, dan sudah membuat ujian.
                                    Untuk childnya tidak akan muncul tombol resetnya.
                                </li>
                            </ul>

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <table class="table table-hover" id="tableJadwalUjian">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dosen</th>
                                    <th>Kelas</th>
                                    <th>Matakuliah</th>
                                    <th>Tanggal Ujian</th>
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

    @include('dashboard.admin.manajemen-kuliah._modal._modal-jadwal-ujian')
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

            var table = $('#tableJadwalUjian').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : "{{ route('manajemen.kuliah.jadwal.admin.ujian.index') }}",
                    data: function(d) {
                        d.filterKelas = filterKelas;
                        d.filterMatkul = filterMatkul;
                        d.filterDosen = filterDosen;

                        return d;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    {
                        data: 'dosen_jadwal',
                        name: 'dosen_jadwal',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('width', '15%');
                        }
                    },
                    { data: 'kelas_jadwal', name: 'kelas_jadwal' },
                    {
                        data: 'matkul_jadwal',
                        name: 'matkul_jadwal',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('width', '20%');
                        }
                    },
                    {
                        data: 'tgl_ujian',
                        name: 'tgl_ujian',
                        render: function(data, type, row) {
                            let output;
                            row.ended_at == null ? output = row.started_at + ' WIB' :
                                output = row.started_at + ' - ' + row.ended_at + ' WIB';

                            return moment(data).format('dddd, DD MMMM YYYY') + ', <br>' + output;
                        }
                    },
                    {
                        data: 'status_ujian',
                        name: 'status_ujian',
                        render: function(data, type, row) {
                            if (data == 'draft') {
                                return '<span class="badge badge-secondary">Draft</span>';
                            } else if (data == 'aktif') {
                                return '<span class="badge badge-success">Aktif</span>';
                            } else if (data == 'nonaktif') {
                                return '<span class="badge badge-primary">Selesai</span>';
                            }
                        }
                    },
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
                ],
            }); // End Datatable

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

            // Refresh Datatable
            $('#refreshTable').on('click', function(e) {
                e.preventDefault();

                $('#filter_kelas').val("").trigger('change');
                $('#filter_matkul').val("").trigger('change');
                $('#filter_dosen').val("").trigger('change');

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

            initSelect2("#filter_kelas", "Filter Kelas");
            initSelect2("#filter_matkul", "Filter Mata Kuliah");
            initSelect2("#filter_dosen", "Filter Dosen");
            initSelect2('#add_dosen', 'Pilih Dosen', '#modalCreate');
            initSelect2('#add_status_ujian', 'Pilih Status Ujian', '#modalCreate');
            initSelect2('#add_matkul', 'Pilih Matakuliah', '#modalCreate');
            initSelect2('#add_kelas', 'Pilih Kelas', '#modalCreate');
            initSelect2("#add_dosen_can_manage", 'Silahkan Pilih', '#modalCreate');

            // ============================= BUAT DATA ============================= //

            $("#formAddJadwalUjian").on('submit', function(e) { // Insert Data baru
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
                        $(document).find('.form-control').removeClass(
                            'is-invalid');
                    },
                    complete: function() {
                        $('.submitAdd').removeAttr('disabled');
                        $('.submitAdd').html('Tambah');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            if (res.tipe == 'validation') {
                                $.each(res.errors, function(key, val) {
                                    $('span.' + key + '_error').text(val[0]);
                                    $("#add_" + key).addClass('is-invalid');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: res.title,
                                    html: res.message,
                                });
                            }
                        } else {
                            $('#formAddJadwalUjian')[0].reset();
                            $('#modalCreate').modal('hide');

                            table.ajax.reload(null, false);

                            setTimeout(function() {
                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                            }, 500);
                        }
                    }, // end success
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                }); // end ajax
            }); // end form add

            function dropdownDisableCreate() { // ini untuk mengosongkan dropdown
                $("#add_kelas").attr('disabled', 'disabled');
                $("#add_matkul").attr('disabled', 'disabled');
                $("#add_kelas").empty();
                $("#add_matkul").empty();
            }

            function dropdownNormalCreate() { // ini untuk menormalkan dropdown
                $("#add_kelas").removeAttr("disabled");
                $("#add_matkul").removeAttr("disabled");
                $("#add_kelas").empty();
                $("#add_matkul").empty();
            }

            // dinamis dropdown ketika dosen dipilih
            $("#add_dosen").on('change', function() {
                let dosen_id = $(this).val();

                if (dosen_id) { // jika dosen dipilih
                    appendCreate(dosen_id);
                } else {
                    dropdownDisableCreate();
                }
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
                                $("#add_kelas").append(`
                                    <option value="${value.id}">${value.kode}</option>
                                `);
                            });

                            // ini untuk mengisi dropdown matkul
                            $("#add_matkul").append('<option value="">Pilih Matakuliah</option>');
                            $.each(res.matkul, function(key, value) {
                                $("#add_matkul").append(`<option value="${value.id}">${value.nama}</option>`);
                            });
                        } else {
                            dropdownDisableCreate();
                        }
                    } // end success
                }); // end ajax
            }

            // modal close reset form
            $('#modalCreate').on('hidden.bs.modal', function() {
                $("#add_dosen").select2("val", ' ');
                $("#add_status_ujian").select2("val", ' ');
                $("#add_matkul").select2("val", ' ');
                $("#add_kelas").select2("val", ' ');
                $("#add_dosen_can_manage").select2("val", ' ');
                $("#add_tanggal_ujian").val(' ');
            });

            // ============================= EDIT DATA ============================= //

            $(document).on('click', '.edit_btn', function() { // edit button to show modal

                var id = $(this).attr('id');

                $.ajax({
                    method: 'GET',
                    url: "{{ route('manajemen.kuliah.jadwal.admin.ujian.show', ':id') }}"
                        .replace(':id', id),
                    success: function(res) {
                        if (res.status == 200) {
                            $("#modalEdit").modal('show');

                            let data = res.data;

                            // show data value
                            $('#edit_id').val(id);
                            $('#edit_status_ujian').val(data.status_ujian);
                            $('#edit_started').val(data.started_at);
                            $('#edit_ended').val(data.ended_at);
                            $('#edit_dosen').val(data.dosen.id);
                            $('#edit_tanggal_ujian').val(data.tanggal);
                            $('#edit_dosen_can_manage').val(data.dosen_can_manage);

                            // Select2 form edit
                            initSelect2("#edit_dosen", 'Pilih Dosen', '#modalEdit');
                            initSelect2("#edit_dosen_can_manage", 'Silahkan pilih..', '#modalEdit');

                            dropdownFunc(res);

                        } else {
                            $("#modalEdit").modal('hide');

                            $(document).find('span.error-text').text('');
                            $(document).find('input.form-control').removeClass(
                                'is-invalid');

                            Swal.fire({
                                icon: 'warning',
                                html: res.message,
                            });
                        } // end if
                    }, // end success
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                }); // end ajax
            }); // end on submit

            // memperbarui data jadwal ujian
            $('#formEditJadwalUjian').on("submit", function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();

                $.ajax({
                    url: "{{ route('manajemen.kuliah.jadwal.admin.ujian.update', ':id') }}".replace(':id', id),
                    type: $(this).attr('method'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
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
                            if (res.tipe == 'validation') {
                                $.each(res.errors, function(key, val) {
                                    $('span.edit_' + key + '_error').text(val[0]);
                                    $("#edit_" + key).addClass('is-invalid');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: res.title,
                                    html: res.message,
                                });
                            }
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
                }); // end ajax
            }); // end on submit

            // function untuk menampilkan dropdown dinamis
            function dropdownFunc(res) {
                let data = res.data;

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.dropdownEdit', ':id') }}"
                        .replace(':id', data.dosen.id),
                    dataType: "JSON",
                    success: function(resp) {
                        if (resp) {
                            $("#edit_kelas").empty();
                            $("#edit_matkul").empty();

                            $("#dropdownDinamis").html(resp);

                            initSelect2("#edit_matkul", 'Pilih matkul', '#modalEdit');
                            initSelect2("#edit_kelas", 'Pilih kelas', '#modalEdit');

                            $('#edit_kelas').val(data.kelas.id).trigger('change');
                            $('#edit_matkul').val(data.matkul.id).trigger('change');
                        } else {
                            $("#edit_kelas").empty();
                            $("#edit_matkul").empty();
                        }
                    } // end success
                }); // end ajax

                // on change select dosen
                $("#edit_dosen").on('change', function() {
                    let dosen_id = $(this).val();

                    if (dosen_id) {
                        appendEdit(dosen_id);
                    } else {
                        $("#edit_kelas").empty();
                        $("#edit_matkul").empty();
                    }
                });
            } // end function

            function appendEdit(dosen_id) { // function untuk menampilkan dropdown dinamis
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
                                $("#edit_kelas").append(`
                                    <option value="${value.id}">${value.kode}</option>
                                `);
                            });

                            $.each(res.matkul, function(key, value) {
                                $("#edit_matkul").append(`
                                    <option value="${value.id}">${value.nama}</option>
                                `);
                            });
                        } else {
                            $("#edit_kelas").empty();
                            $("#edit_matkul").empty();
                        }
                    }
                }); // end ajax
            }

            // ============================= HAPUS DATA ============================= //

            $(document).on('click', '.del_btn', function(e) { // delete button to show modal
                e.preventDefault();

                var id = $(this).attr('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.ujian.show', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $('#modalDelete').modal('show');

                        let data = res.data;
                        let tanggal = moment(data.tanggal).format('dddd, DD MMMM YYYY');

                        $('#modalDelete #text_del').text(
                            `Apakah anda yakin ingin menghapus Jadwal Ujian\t
                            "Tanggal ${tanggal}, Jam ${data.started_at} s.d. ${data.ended_at}\tWIB" ?`
                        );
                        $('#del_id').val(id);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                }); // end ajax
            }); // end on click

            // delete data jadwal ujian
            $('#formHapusJadwalUjian').on("submit", function(e) {
                e.preventDefault();

                let id = $('#del_id').val();

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.ujian.delete', ':id') }}"
                        .replace(':id', id),
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "id": id,
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
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                }); // end ajax
            }); // end on submit

            // ============================= RESET DATA ============================= //

            $(document).on("click", '#resetData', function (e) { // show confirm reset data
                e.preventDefault();

                let id = $(this).val();
                let text = `
                    <span class='font-weight-bold'>APAKAH ANDA YAKIN?</span> <hr>
                    Jika anda mereset jadwal ujian ini,\tmaka semua data yang
                    sama dengan jadwal ujian(kelas \t&\t matakuliah)\t ini akan dihapus.\t
                    Seperti Ujian,\t Soal,\t Jawaban,\t Nilai,\t dan lain-lain.
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

            // function reset data
            function resetData(id) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.ujian.reset') }}",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function (res) {
                        if (res.status == 200) {
                            table.ajax.reload(null, false);

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

        }); // end ready
    </script>
@endpush
