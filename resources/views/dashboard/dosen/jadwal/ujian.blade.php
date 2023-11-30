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
                            <h5 class="m-0 p-0 font-weight-bold ml-2">
                                <i class="fas text-primary fa-calendar-alt mr-1"></i>
                                Jadwal Ujian
                            </h5>
                            <div>
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
                            {{-- filter --}}
                            <div class="col-md-4 col-6">
                                <select id="filter_kelas" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($data_kelas as $k)
                                        <option value="{{ $k->kode }}">{{ $k->kode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-6">
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
                        <table id="tableJadwalUjian" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Matakuliah</th>
                                    <th>Kelas</th>
                                    <th>Tanggal Ujian</th>
                                    <th>Jumlah</th>
                                    <th>Tipe Ujian</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div> {{-- col-lg-12 --}}
        </div> {{-- row --}}
    </div> {{-- container-fluid --}}

    <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="deleteTitle" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">

                <form action="" method="DELETE" id="formHapusJadwalUjian">
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

    @include('dashboard.dosen.jadwal._modal._modal-create')
@endsection

@push('js')
    <script>
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

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let filterKelas = $('#filter_kelas').val(),
                filterMatkul = $('#filter_matkul').val();

            let table = $('#tableJadwalUjian').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : "{{ route('manajemen.kuliah.jadwal.dosen.ujian.index') }}",
                    data: function(d) {
                        d.filterKelas = filterKelas;
                        d.filterMatkul = filterMatkul;

                        return d;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'matkul_jadwal', name: 'matkul_jadwal' },
                    { data: 'kelas_jadwal', name: 'kelas_jadwal' },
                    {
                        data: 'tanggal',
                        name: 'tanggal',
                        render: function(data, type, row) {
                            let jam;
                            (row.ended_at == null) ? jam = row.started_at: jam = row.started_at +
                                ' - ' + row.ended_at;

                            return data + ', <br>' + jam + ' WIB';
                        }

                    },
                    {
                        data: 'ujian_soal',
                        name: 'ujian_soal',
                        render: function(data, type, row) {
                            if (data == null) {
                                return '<span class="badge badge-danger text-uppercase">Belum Dibuat</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: "tipe_soal",
                        name: "tipe_soal",
                        render: function(data, type, row) {
                            if (data == null) {
                                return '<span class="badge badge-danger text-uppercase">Belum Dibuat</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        className: 'noPrint',
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            }); // End table

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $('.filter').change(function() {
                filterKelas = $('#filter_kelas').val();
                filterMatkul = $('#filter_matkul').val();

                table.ajax.reload(null, false);
            });

            // refresh table
            $('#refreshTable').on('click', function(e) {
                e.preventDefault();

                $('#filter_kelas').val("").trigger('change');
                $('#filter_matkul').val("").trigger('change');

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

            initSelect2("#filter_kelas", "Filter Kelas");
            initSelect2("#filter_matkul", "Filter Mata Kuliah");

            // show modal delete
            $(document).on('click', '.del_btn', function(e) {
                e.preventDefault();

                var id = $(this).attr('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.ujian.show', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $('#modalDelete').modal('show');

                        $('#modalDelete #text_del').text(
                            `Apakah anda yakin ingin menghapus Jadwal Ujian "Tanggal ${res.data.tanggal_ujian}, Jam ${res.data.started_at} s/d ${res.data.ended_at}" ?`
                        );
                        $('#del_id').val(id);
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
                }); // end ajax
            }); // end on click

            // delete
            $('#formHapusJadwalUjian').on("submit", function(e) {
                e.preventDefault();

                let id = $('#del_id').val();

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('manajemen.kuliah.jadwal.admin.ujian.delete', ':id') }}".replace(
                        ':id', id),
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
                }); // end ajax
            }); // end on submit

        }); // End document ready
    </script>
@endpush
