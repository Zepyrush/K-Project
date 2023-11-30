@extends('layouts.dashboard')

@section('title', 'Materi ' . $jadwal->matkul->nama)

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12 sticky">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex flex-row align-items-center justify-content-between">
                            <a href="javascript:void(0)" class="btn btn-primary btn_back btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <h5 class="m-0 font-weight-bold">
                                @yield('title')
                            </h5>

                            <div></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card card-primary card-outline mb-2">
                    <div class="card-header p-1">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#materiTambahan" data-toggle="tab">
                                    Materi Tambahan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#vidio" data-toggle="tab">
                                    Vidio Pembelajaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#slide" data-toggle="tab">
                                    Slide Pembelajaran
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header p-2 print-materi" style="display: none">
                        <button id="cetakTable" class="btn btn-primary btn-sm">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                        <button id="refreshTable" class="btn btn-warning btn-sm ml-1"
                            data-toggle="tooltip" title="Refresh Table">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                    <div class="card-body table-responsive">
                        <div class="tab-content">
                            <div class="active tab-pane fade show" id="materiTambahan">
                                <table class="table table-hover" id="tableMateri">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Judul</th>
                                            <th>Pertemuan</th>
                                            <th>Deskripsi</th>
                                            <th>Upload</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade show" id="vidio">
                                @if ($materis->where('tipe', 'youtube')->count() > 0)
                                    <div class="row">
                                        @foreach ($materis as $materi)
                                            @if ($materi->tipe == 'youtube')
                                                <div class="col-lg-4 col-12">
                                                    <div class="card card-primary card-outline materi">
                                                        <div class="card-header d-flex align-items-center">
                                                            <h5 class="m-0 font-weight-bold">{{ $materi->judul }}</h5>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            {{-- iframe --}}
                                                            <div class="embed-responsive embed-responsive-16by9">
                                                                <iframe class="embed-responsive-item"
                                                                    src="https://www.youtube.com/embed/{{ $materi->file_or_link }}"
                                                                    allowfullscreen>
                                                                </iframe>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-white"
                                                            style="padding: 0.75rem 0.75rem !important; border-radius: 0 0 8px 8px;">
                                                            <div class="mt-1 text-muted d-flex justify-content-between">
                                                                <small>Pertemuan {{ $materi->pertemuan }}</small>
                                                                <small>
                                                                    {{ Carbon\Carbon::parse($materi->created_at)->translatedFormat('d F Y - H:i') . " WIB" }}</small>
                                                            </div>

                                                            <div class="divider2"></div>

                                                            <p class="p-0 m-0">
                                                                {{ $materi->deskripsi }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle mr-1"></i> Tidak ada vidio pembelajaran
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade show" id="slide">
                                @if ($materis->where('tipe', 'slide')->count() > 0)
                                    <div class="row">
                                        @foreach ($materis as $materi)
                                            @if ($materi->tipe == 'slide')
                                                <div class="col-lg-4 col-12">
                                                    <div class="card card-primary card-outline materi">
                                                        <div
                                                            class="card-body text-center d-flex justify-content-center p-5">

                                                            <div class="d-flex flex-column align-items-center">
                                                                <div class="bg-icon-slide">
                                                                    <i class="far fa-file-archive"></i>
                                                                </div>

                                                                <p class="slide-text my-3">{{ $materi->judul }}</p>

                                                                <div class="flex-row">
                                                                    <a download
                                                                        href="{{ asset('assets/file/slide/' . $materi->file_or_link) }}"
                                                                        class="btn btn-primary" data-toggle="tooltip"
                                                                        title="Download file">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info text-center">
                                        <i class="fas fa-info-circle mr-1"></i> Tidak ada slide pembelajaran
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // cek apakah tab materi aktif
            if ($("a[href='#materiTambahan'].nav-link").hasClass('active')) {
                $(".print-materi").show('fade');
            }

            $("a.nav-link").click(function() {  // ketika tab di klik
                if ($(this).attr('href') === '#materiTambahan') { // jika tab materi aktif
                    $(".print-materi").show('fade');
                } else {
                    $(".print-materi").hide('fade');
                }
            });

            // jika tombol back diklik
            $(".btn_back").click(function(e) {
                e.preventDefault();

                // jika masuk ke materinya lewat halaman jadwal maka akan kembali ke halaman jadwal
                if (localStorage.getItem(`${noIndukUser}_jadwal`) == 'true') {
                    localStorage.removeItem(`${noIndukUser}_jadwal`);
                    window.location.href = "{{ route('manajemen.kuliah.jadwal.mahasiswa.index') }}";
                } else {
                    window.location.href =
                        "{{ route('manajemen.kuliah.kelas.mahasiswa.index', encrypt($jadwal->id)) }}";
                }
            });

            var table = $("#tableMateri").DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manajemen.kuliah.kelas.mahasiswa.materi', encrypt($jadwal->id)) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'judul', name: 'judul' },
                    { data: 'pertemuan', name: 'pertemuan' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'upload', name: 'upload' },
                    { className: 'noPrint', data: 'action', name: 'action', orderable: false, searchable: false },
                ],
            }); // end datatable

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            $("#refreshTable").on("click", function(e) {
                e.preventDefault();
                table.ajax.reload(null, false);
            });
        });
    </script>
@endpush
