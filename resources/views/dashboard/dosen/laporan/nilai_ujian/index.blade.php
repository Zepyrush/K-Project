@extends('layouts.dashboard')

@section('title', 'Laporan Nilai Ujian')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">

                @if ($jadwals->isNotEmpty())
                    <div class="row">

                        <div class="col-lg-3 col-12">
                            <div class="card card-primary card-outline sticky">
                                <div class="card-header">
                                    <h5 class="font-weight-bold p-0 m-0">
                                        <i class="fas fa-school text-primary mr-1"></i> Daftar Kelas
                                    </h5>
                                </div>
                                <div class="card-body p-2">
                                    <div class="nav flex-column nav-pills" id="daftarKelas">
                                        @foreach ($jadwals as $jadwal)
                                            @php
                                                $key = $jadwal->kelas_id . '_' . $jadwal->matkul_id . '_' . $jadwal->dosen_id;
                                            @endphp

                                            <a id="{{ $key }}"
                                                class="nav-link btn_kelas {{ $loop->index == 0 ? 'active' : '' }}"
                                                data-toggle="pill" href="#tab-{{ $key }}">

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="m-0 p-0">
                                                        {{ $jadwal->kode }} ({{ $jadwal->matkulKode }})
                                                    </span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="dataNilaiUjian" class="col-lg-9 col-12"></div>
                    </div>
                @else
                    <div class="alert card card-primary card-outline">
                        <h5 class="font-weight-bold">
                            Perhatian!
                        </h5>
                        <p class="m-0 p-0 w-50">
                            Anda tidak memiliki jadwal ujian. Atau mungkin anda memiliki jadwal tapi ujian dijadwal tersebut belum dibuat,
                            jika ada.. silahkan buat ujian terlebih dahulu.
                        </p>
                    </div>
                @endif
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

            // first load a.active data
            let id = $(".btn_kelas.active").attr("id");
            loadData(id);

            $(".btn_kelas").on("click", function(e) {
                e.preventDefault();

                let id = $(this).attr("id");
                loadData(id);
            });

            function loadData(id) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.laporan.dosen.fetch.data.nilai.ujian') }}",
                    data: {
                        key_id: id
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $("#dataNilaiUjian").html(
                            '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>'
                        );
                    },
                    success: function(res) {
                        $("#dataNilaiUjian").html(res);

                        loadTableData(id);
                    } // end success
                }); // end ajax
            } // end function loadData

            function loadTableData(id) {
                let table = $("#tableLaporanUjian").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('manajemen.kuliah.laporan.dosen.fetch.table.nilai.ujian') }}",
                        type: "GET",
                        data: {
                            key_id: id
                        }
                    },
                    columns: [
                        {
                            data: 'DT_RowIndex', name: 'DT_RowIndex',
                            orderable: false,searchable: false
                        },
                        {data: 'mahasiswa',name: 'mahasiswa'},
                        {data: 's1',name: 's1'},
                        {data: 's2',name: 's2'},
                        {data: 's3',name: 's3'},
                        {data: 's4',name: 's4'},
                        {data: 's5',name: 's5'},
                        {data: 's6',name: 's6'},
                        {data: 's7',name: 's7'},
                        {data: 's8',name: 's8'}
                    ]
                }); // end datatable

                $("#cetakTable").on("click", function(e) {
                    e.preventDefault();
                    table.button(0).trigger();
                });
            }
        });
    </script>
@endpush
