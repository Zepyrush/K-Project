@extends('layouts.dashboard')

@section('title', 'Tugas ' . $tugas->matkul->nama . ' - Pertemuan ke-' . $tugas->pertemuan)

@section('content')
    <div class="container-fluid">
        <div class="row sticky" style="z-index: 1037 !important;">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('manajemen.kuliah.tugas.dosen.index', encrypt($tugas->jadwal->id)) }}"
                                class="btn btn-primary btn-back btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <h5 class="font-weight-bold m-0 p-0 ml-2">
                                @yield('title')
                            </h5>

                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <h5 class="m-0 font-weight-bold ml-2">
                            Informasi Tugas
                        </h5>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Judul</b>
                                            </div>
                                            <div class="col-lg-8 col-8">
                                                {{ $tugas->judul }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Pertemuan</b>
                                            </div>
                                            <div class="col-lg-8 col-8">
                                                Ke-{{ $tugas->pertemuan }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-2">
                                        <div class="row">
                                            <div class="col-lg-4 col-4">
                                                <b>Deadline Pada</b>
                                            </div>
                                            <div class="col-lg-8 col-8">
                                                {{ $tugas->deadline }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-4 col-4">
                                                <b>Upload Pada</b>
                                            </div>
                                            <div class="col-lg-8 col-8">
                                                {{ $tugas->upload }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row ">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-2 col-12 mb-1">
                                                <b>Deskripsi</b>
                                            </div>
                                            <div class="col-lg-10 col-12">
                                                {{ $tugas->deskripsi }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> {{-- end col-lg-12 --}}
                        </div> {{-- end row --}}
                    </div> {{-- end card-body --}}
                </div> {{-- end card --}}
            </div> {{-- end col-lg-12 --}}
        </div> {{-- end row --}}

        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <h5 class="m-0 font-weight-bold ml-2">
                            Informasi & Daftar Mahasiswa
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-12 mb-3">
                <div class="list-group sticky sticky_sub">
                    <button type="button"
                        class="list-group-item list-group-item-action active cursor_default font-weight-bold">
                        Informasi Mahasiswa
                    </button>

                    <a href="javascript:void(0)" id="mhsTotal" class="list-group-item list-group-item-action">
                        <i class="fas fa-users text-primary mr-1"></i>
                        Total Mahasiswa

                        <span class="badge badge-primary badge-pill float-right position-relative" style="top: 2px;">
                            {{ $mahasiswa->count() }}
                        </span>
                    </a>
                    <a href="javascript:void(0)" class="list-group-item list-group-item-action cursor_default">
                        <i class="fas fa-user-check text-success mr-1"></i>
                        Mengumpulkan Tugas

                        <span class="badge badge-success badge-pill float-right position-relative" style="top: 2px;">
                            {{ $tugasMahasiswa->count() }}
                        </span>
                    </a>
                    <a href="javascript:void(0)" id="mhsTidak" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-times text-danger mr-1"></i>
                        Tidak Mengumpulkan

                        <span class="badge badge-danger badge-pill float-right position-relative" style="top: 2px;">
                            {{ $mahasiswa->count() - $tugasMahasiswa->count() }}
                        </span>
                    </a>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="m-0 font-weight-bold ml-2">
                                Daftar Mahasiswa <small class="text-muted">(Mengumpulkan Tugas)</small>
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
                    <div class="card-body table-responsive">
                        <table id="tableMahasiswa" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Nilai</th>
                                    <th>Dikumpulkan</th>
                                    <th>Tugas</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Reply --}}
    <div class="modal fade" id="modalReply" tabindex="-1" role="dialog" aria-labelledby="replayTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-centered">
                <div class="modal-header p-2">
                    <h5 class="modal-title font-weight-bold ml-2" id="replayTitle">
                        Form Reply - Tugas {{ $tugas->matkul->kode }}, Pertemuan
                        {{ $tugas->pertemuan }}
                    </h5>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="formTugasReply" action="#" autocomplete="off" method="POST">
                    @csrf
                    @method('POST')

                    <input type="hidden" name="parent" value="{{ $tugas->pertemuan }}">
                    <input type="hidden" id="tugas_id">

                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="nilai">Nilai Tugas</label>
                            <input type="number" class="form-control" id="reply_nilai" name="nilai"
                                placeholder="Masukkan Nilai tugas">
                            <span class="invalid-feedback d-block error-text nilai_error"></span>
                        </div>
                        <div class="form-group mb-3">
                            <label for="komentar">Komentar Anda</label>
                            <textarea name="komentar" id="reply_komentar" class="form-control" rows="3"
                                placeholder="Masukkan komentar anda jika anda mau berkomentar."></textarea>
                            <span class="invalid-feedback d-block error-text komentar_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer p-2">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="submitReply btn btn-primary">
                            Beri Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Detail --}}
    <div class="modal fade" id="modalDetail" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-2">
                    <div class="ml-2" style="line-height: 1.2;">
                        <h5 class="modal-title font-weight-bold" id="judulModalDetail"></h5>
                        <div class="">
                            <span class="text-muted" id="infoMhsModal"></span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        <div class="col-lg-12">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-4">
                                    <b>Nilai</b>
                                </div>
                                <div id="nilaiDetail" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-4">
                                    <b>Dikumpulkan</b>
                                </div>
                                <div id="diKumpulkanDetail" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row mb-3">
                                <div class="col-lg-4 col-4">
                                    <b>Diubah</b>
                                </div>
                                <div id="diUbahDetail" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row mb">
                                <div class="col-lg-4 col-4">
                                    <b>Komentar Dosen</b>
                                </div>
                                <div id="komentarDetail" class="col-lg-8 col-8"></div>
                            </div>
                        </div>

                    </div> {{-- End Row --}}
                </div> {{-- End Modal Body --}}

                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>

            </div> {{-- End Modal Content --}}
        </div> {{-- End Modal Dialog --}}
    </div>

    @include('dashboard.dosen._tugas._modal-table-mahasiswa')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Datatable
            var table = $('#tableMahasiswa').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('manajemen.kuliah.tugas.dosen.show', encrypt($tugas->id)) }}",
                columns: [
                    { data: 'mahasiswa', name: 'mahasiswa', orderable: false, searchable: false },
                    {
                        data: 'nilai',
                        name: 'nilai',
                        render: function(data, type, row) {
                            return data == 'belum_dinilai' ? "<span class='badge badge-danger'>Belum Dinilai</span>" : data;
                        }
                    },
                    { data: 'upload', name: 'upload' },
                    {
                        className: 'noPrint',
                        data: 'download_tugas',
                        name: 'download_tugas',
                        render: function(data, type, row) {
                            if (data === null) return "<a href='javascript:void(0)' class='btn btn-sm btn-secondary disabled' data-toggle='tooltip' title='Download Tugas'><i class='fas fa-download'></i></a>";

                            return `<a download href='${data}' class='btn btn-sm btn-primary' data-toggle='tooltip' title='Download Tugas'><i class='fas fa-download'></i></a>`;
                        }
                    },
                    { className: 'noPrint', data: 'action', name: 'action', orderable: false, searchable: false }
                ],
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            // refresh table
            $("#refreshTable").on("click", function(e) {
                e.preventDefault();
                table.ajax.reload(null, false);
            });

            // jika dari dashboard
            if (localStorage.getItem(`${noIndukUser}_fromDashboard`) == "true") {
                $(".btn-back").attr("href", "{{ route('dosen.dashboard') }}");
                $(".btn-back").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
                $("a").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
            }

            // show modal nilai
            $(document).on('click', '.nilai_btn', function(e) {
                e.preventDefault();

                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.tugas.dosen.showNilai', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $("#modalReply").modal('show');

                        let nilai_tugas = res.tugas.nilai_tugas;
                        $("#tugas_id").val(id);

                        if (nilai_tugas != null) {
                            $("#reply_nilai").val(nilai_tugas.nilai);
                            $("#reply_komentar").val(nilai_tugas.komentar);
                        } else {
                            $("#reply_nilai").val('');
                            $("#reply_komentar").val('');
                        }
                    }
                });
            });

            // insert nilai
            $("#formTugasReply").on('submit', function(e) {
                e.preventDefault();

                let id = $("#tugas_id").val();

                $.ajax({
                    type: $(this).attr("method"),
                    url: "{{ route('manajemen.kuliah.tugas.dosen.storeNilai', ':id') }}".replace(':id', id),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitReply').attr('disabled', true);
                        $('.submitReply').html(
                            '<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('input.form-control').removeClass(
                            'is-invalid');
                    },
                    complete: function() {
                        $('.submitReply').removeAttr('disabled');
                        $('.submitReply').html('Beri Nilai');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            if (res.validation == true) {
                                $.each(res.errors, function(key, val) {
                                    $('span.' + key + '_error').text(val[0]);
                                    $("#reply_" + key).addClass('is-invalid');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.message
                                });
                            }
                        } else {
                            $("#modalReply").modal('hide');

                            if (res.changed == false) {
                                Toast.fire({
                                    icon: 'warning',
                                    title: res.message
                                });
                            } else {
                                table.ajax.reload(null, false);

                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
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
                            alert(xhr.status + "\n" + xhr.responseText + "\n" +
                                thrownError);
                        }
                    }
                });
            }); // End Reply Tugas

            // show modal detail
            $(document).on("click", '.detail_btn', function (e) {
                e.preventDefault();

                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.tugas.dosen.showNilai', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $("#modalDetail").modal('show');

                        let tugas = res.tugas;
                        let mahasiswa = tugas.mahasiswa;
                        let nilaiTugas = tugas.nilai_tugas;

                        $("#judulModalDetail").html("Detail Nilai Tugas Mahasiswa");
                        $("#infoMhsModal").html(mahasiswa.nama + " (" + mahasiswa.nim + ")");
                        $("#linkTugasDetail").html(`
                            <a href="${tugas.file_or_link}" target="_blank" class="btn btn-sm btn-primary"
                                data-toggle="tooltip" title="Lihat Tugas">
                                Lihat Tugas
                                <i class="fas fa-external-link-alt ml-1"></i>
                            </a>
                        `);
                        $("#nilaiDetail").html(nilaiTugas.nilai);
                        $("#diKumpulkanDetail").html(moment(tugas.created_at).format('DD MMMM YYYY, HH:mm') + " WIB");
                        $("#diUbahDetail").html(moment(tugas.updated_at).format('DD MMMM YYYY, HH:mm') + " WIB");
                        $("#komentarDetail").html(nilaiTugas.komentar);
                    }
                });
            });
        });
    </script>
@endpush
