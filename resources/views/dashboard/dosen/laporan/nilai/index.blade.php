@extends('layouts.dashboard')

@section('title', 'Laporan Nilai Tugas & Ujian')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @if ($jadwals->isNotEmpty())
                <div class="col-lg-3 col-12">
                    <div class="card card-primary card-outline sticky">
                        <div class="card-header">
                            <h5 class="font-weight-bold p-0 m-0">
                                <i class="fas fa-school text-primary mr-1"></i>
                                Daftar Kelas
                            </h5>
                        </div>
                        <div class="card-body p-2">
                            <div class="nav flex-column nav-pills" id="daftarKelas">
                                @foreach ($jadwals as $jadwal)
                                    @php
                                        $key = $jadwal->kelas_id . '_' . $jadwal->matkul_id . '_' . $jadwal->dosen_id;
                                        $kode_mtkl = Auth::user()->dosen->matkuls->find($jadwal->matkul_id)->kode ?? $jadwal->matkul->kode;
                                    @endphp

                                    <a id="{{ $key }}" class="nav-link btn_kelas {{ $loop->index == 0 ? 'active' : '' }}" data-toggle="pill"
                                        href="#tab-{{ $key }}">

                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="m-0 p-0">
                                                {{ $jadwal->kelas->kode }} ({{ $kode_mtkl }})
                                            </span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div id="dataNilai" class="col-lg-9 col-12"></div>
            @else
                <div class="col-12">
                    <div class="alert card card-primary card-outline">
                        <h5 class="font-weight-bold">
                            Perhatian!
                        </h5>
                        <p class="m-0 p-0">
                            Anda belum mengajar di kelas manapun.
                            Atau mungkin anda memiliki kelas namun belum membuat tugas.<br>
                            Jika iya, silahkan buat tugas terlebih dahulu untuk dapat melihat laporan nilai tugas.
                        </p>
                    </div>
                </div>
            @endif
        </div> {{-- end row --}}
    </div> {{-- end container --}}

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            let id = $(".btn_kelas.active").attr("id");
            loadData(id);

            $(".btn_kelas").click(function(e) {
                e.preventDefault();

                let id = $(this).attr("id");
                loadData(id);
            });

            function loadData(id) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.laporan.dosen.fetch.data.nilai') }}",
                    data: {
                        key_id: id
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $("#dataNilai").html(
                            '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>'
                        );
                    },
                    success: function(res) {
                        $("#dataNilai").html(res);
                        loadTableData(id);
                    } // end success
                }); // end ajax
            } // end function loadData

            function loadTableData(id)
            {
                let table = $("#tableLaporanNilai").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('manajemen.kuliah.laporan.dosen.fetch.table.nilai') }}",
                        type: "GET",
                        data: {
                            key_id: id
                        }
                    },
                    columns: [
                        {data: 'DT_RowIndex',name: 'DT_RowIndex', orderable: false,searchable: false},
                        {data: 'mahasiswa',name: 'mahasiswa'},
                        {data: 'p1',name: 'p1'},
                        {data: 'p2',name: 'p2'},
                        {data: 'p3',name: 'p3'},
                        {data: 'p4',name: 'p4'},
                        {data: 'p5',name: 'p5'},
                        {data: 'p6',name: 'p6'},
                        {data: 'p7',name: 'p7'},
                        {data: 'p8',name: 'p8'},
                        {data: 'p9',name: 'p9'},
                        {data: 'p10',name: 'p10'},
                        {data: 'p11',name: 'p11'},
                        {data: 'p12',name: 'p12'},
                        {data: 'p13',name: 'p13'},
                        {data: 'p14',name: 'p14'},
                        {data: 'rata_rata',name: 'rata_rata'},
                        {data: 'nilai_uts',name: 'nilai_uts'},
                        {data: 'nilai_uas',name: 'nilai_uas'},
                        {data: 'total',name: 'total'}
                    ],
                    buttons: [
                        {
                            extend: 'print',
                            exportOptions: {columns: ':not(.noPrint)'},
                            title: $(".title-laporan").data("title"),
                        }
                    ],
                }); // end datatable

                $("#cetakTable").on("click", function(e) {
                    e.preventDefault();
                    table.button(0).trigger();
                });
            }
        });
    </script>
@endpush
