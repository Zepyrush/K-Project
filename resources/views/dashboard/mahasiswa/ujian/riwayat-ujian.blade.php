@extends('layouts.dashboard')

@section('title', 'Riwayat Ujian')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 sticky">
                <div class="card card-primary card-outline ">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="font-weight-bold m-0 p-0 ml-2">
                                <i class="fas fa-history text-primary mr-1"></i> Riwayat Ujian
                            </h5>
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

                    <div class="card-header p-2">
                        <div class="row justify-content-between align-items-center">

                            <div class="col-md-4 col-6">
                                <select id="filter_matkul" class="form-control filter">
                                    <option value="">Semua</option>
                                    @foreach ($jadwals->unique('matkul_id') as $jadwal)
                                        <option value="{{ $jadwal->matkul->nama }}">
                                            {{ $jadwal->matkul->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="card-body table-responsive">
                        <table id="tableRiwayatUjian" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Judul</th>
                                    <th>Matakuliah</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
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

    @include('dashboard.mahasiswa.ujian._modal._modal-hasil-ujian')
@endsection

@push('css')
    <style>
        body .select2-container {
            z-index: 1036 !important;
        }
    </style>

@endpush

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let filterMatkul = $('#filter_matkul').val();

            let table = $("#tableRiwayatUjian").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url : "{{ route('manajemen.kuliah.ujian.mahasiswa.riwayatUjian') }}",
                    data: function(d) {
                        d.filterMatkul = filterMatkul;
                        return d;
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'judul_ujian', name: 'judul_ujian' },
                    { data: 'matkul_ujian', name: 'matkul_ujian' },
                    { data: 'tanggal', name: 'tanggal' },
                    {
                        data: 'started_at',
                        name: 'started_at',
                        render: function(data, type, row) {
                            let jam;
                            (row.ended_at == null) ?
                                jam = row.started_at + ' WIB' :
                                jam = row.started_at + ' - ' + row.ended_at + ' WIB';

                            return jam;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('width', '14%');
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
            }); // end table

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $('.filter').change(function() {
                filterMatkul = $('#filter_matkul').val();
                table.ajax.reload(null, false);
            });

            $("#refreshTable").on("click", function(e) {
                e.preventDefault();

                $('#filter_matkul').val('').trigger('change');
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

            initSelect2("#filter_matkul", "Filter Mata Kuliah");
        }); // end document ready
    </script>
@endpush
