@extends('layouts.dashboard')

@section('title', 'Edit Ujian PG | ' . $jadwal->matkul->nama . ' - ' . $jadwal->kelas->kode)

@section('content')
    @if (Session::has('success') || Session::has('error'))
        <div class="alert_success" data-flashdata="{{ Session::get('success') }}"></div>
        <div class="alert_error" data-flashdata="{{ Session::get('error') }}"></div>
    @endif

    <div class="container-fluid">

        <form id="formUjian"
            action="{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.update', encrypt($jadwal->id)) }}"
            method="POST"
            enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PUT')

            <input type="hidden" name="jadwal_id" value="{{ encrypt($jadwal->id) }}">

            <div class="row sticky">
                <div class="col-lg-12 ">
                    <div class="card card-primary card-outline">
                        <div class="card-header p-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <a href="{{ route('manajemen.kuliah.jadwal.dosen.ujian.index') }}"
                                        class="btn btn-primary btn-sm mr-1">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                    <a href="{{ route('manajemen.kuliah.jadwal.dosen.ujian.show', encrypt($jadwal->id)) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i> Detail Ujian
                                    </a>
                                </div>
                                <h5 class="m-0 p-0 font-weight-bold ml-1">
                                    Edit Ujian PG, {{ $jadwal->matkul->nama }} - {{ $jadwal->kelas->kode }}
                                </h5>
                                <button disabled type="submit" class="btn btnUpdate btn-warning btn-sm">
                                    <i class="fas fa-sync mr-1"></i> Update Ujian
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{-- /.row --}}

            <div class="row">
                <div class="col-lg-5">
                   @include('dashboard.dosen.ujian.soal._sub._form-edit-ujian')
                </div> {{-- end col --}}

                <div class="col-lg-7">
                    <div class="card card-primary card-outline">
                        <div class="card-header ">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="font-weight-bold m-0 p-0" id="soalUjian">
                                    Soal Ujian
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div id="soal_ujian" class="fetch_soal">
                    </div> {{-- End #soal_ujian --}}

                    <button type="button" id="tambahSoal" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Soal
                    </button>

                </div> {{-- End col --}}
            </div> {{-- End row --}}
        </form> {{-- End form --}}
    </div> {{-- End container --}}

@endsection

@push('js')
    <script>
        // =============== Global Variable =============== //

        let nomorSoal = "{{ $soalPgs->count() }}" || 1;
        let checkIfNotHasSoal = "{{ $soalPgs->isEmpty() }}";
        let newNumber;

        // =============== Event Section =============== //

        alertNotif();

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
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

            initSelect2("#random_soal", "Silahkan Pilih");
            initSelect2("#tipe_ujian", "Silahkan Pilih");
            initSelect2("#lihat_hasil", "Silahkan Pilih");
            initSelect2("#status_ujian", "Silahkan Pilih");

            $.ajax({ // Ini untuk mengambil soal dari backend
                type: "GET",
                url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.fetch', encrypt($jadwal->id)) }}",
                dataType: "json",
                beforeSend: function() {
                    $(".fetch_soal").html(`
                        <div class="d-flex justify-content-center align-items-center mb-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    `);
                },
                complete: function() {
                    $(".btnUpdate").attr("disabled", false);
                },
                success: function(res) {
                    $(".fetch_soal").html(res);

                    $(".soalUjian .card-header .d-flex:gt(0)").append(`
                        <button type="button" class="btn btn-danger btn-sm ml-1 hapus_soal" data-id="${nomorSoal}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `);

                    summerNote(); // panggil function summernote
                }
            });

            if (nomorSoal >= 50) { // Ini untuk menonaktifkan tombol tambah soal
                $("#tambahSoal").attr("disabled", true);
            }

            nomorSoal = parseInt(nomorSoal) + 1; // Ini untuk menambah nomor soal

            $("#tambahSoal").click(function(e) {
                $("#soal_ujian").append(containerSoal(nomorSoal));
                summerNote(); // panggil function summernote

                $(".soalUjian:last .card-header .d-flex").append(`
                    <button type="button" class="btn btn-danger btn-sm ml-1 hapus_soal"
                        data-id="${nomorSoal}">
                        <i class="fas fa-trash"></i>
                    </button>
                `);

                $(".soalUjian:last .card-header").addClass("p-2");
                $(".soalUjian:last .card-header h6").addClass("ml-2");

                $("html, body").animate({ // scroll ke bawah
                    scrollTop: $(".soalUjian:last").offset().top - 73
                }, 0);

                if (nomorSoal >= 50) { // jika sudah 50 soal maka tombol tambah soal akan di disable
                    $("#tambahSoal").attr("disabled", true);

                    Toast.fire({
                        icon: 'info',
                        html: '<span class="ml-2 font-weight-bold">Soal ujian sudah MAXIMAL</span>',
                    });
                }

                nomorSoal++;
            });
        });

        // =============== Function Section =============== //

        function alertNotif() {
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
        }

        function summerNote() {
            $(".soal_ujian").summernote({
                toolbar:[
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['misc', ['fullscreen', 'codeview', 'help']],
                    ['insert', ['link', 'picture', 'video', 'audio']],
                ],
                height: 150,
                placeholder: "Masukkan soal ujian.",
                maximumImageFileSize: 500 * 1024, // 500 KB
                callbacks: {
                    // callback for pasting text only (no formatting)
                    onPaste: function(e) {
                        var bufferText = ((e.originalEvent || e).clipboardData || window
                                .clipboardData)
                            .getData('Text');
                        e.preventDefault();
                        bufferText = bufferText.replace(/\r?\n/g, '<br>');
                        document.execCommand('insertHtml', false, bufferText);
                    },
                    onImageUploadError: function(msg) {
                        Swal.fire({
                            icon: 'error',
                            html: 'Ukuran gambar tidak boleh lebih dari 500 KB.',
                            allowOutsideClick: false,
                        });
                    },
                }
            });
        }

        function containerSoal(nomer) {
            return `<div class="card soalUjian" id="soal_${nomer}" data-id="${nomer}"><div class="card-header p-2"><div class="d-flex justify-content-between align-items-center"><h6 class="font-weight-bold m-0 p-0">Soal No. \t ${nomer}</h6></div></div><div class="card-body p-3"><div class="form-group m-0 p-0"><textarea required name="pertanyaan[]" id="soal" class="form-control soal_ujian" rows="5" placeholder="Masukkan soal ujian."></textarea></div><div class="form-group m-0 p-0 row"><div class="col-lg-6 mb-3"><label for="pilihan_a" class="font-weight-bold">Pilihan A</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">A</div></div><input required type="text" name="pilihan_a[]" id="pilihan_a" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan A."></div></div><div class="col-lg-6 mb-3"><label for="pilihan_d" class="font-weight-bold">Pilihan D</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">D</div></div><input required type="text" name="pilihan_d[]" id="pilihan_d" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan D."></div></div></div><div class="form-group m-0 p-0 row"><div class="col-lg-6 mb-3"><label for="pilihan_b" class="font-weight-bold">Pilihan B</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">B</div></div><input required type="text" name="pilihan_b[]" id="pilihan_b" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan B."></div></div><div class="col-lg-6 mb-3"><label for="pilihan_e" class="font-weight-bold">Pilihan E</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">E</div></div><input required type="text" name="pilihan_e[]" id="pilihan_e" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan E."></div></div></div><div class="form-group m-0 p-0 row"><div class="col-lg-6 mb-3"><label for="pilihan_c" class="font-weight-bold">Pilihan C</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">C</div></div><input required type="text" name="pilihan_c[]" id="pilihan_c" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan C."></div></div><div class="col-lg-6"><label for="jawaban_benar" class="font-weight-bold">Jawaban Benar</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="fas fa-check-circle text-success"></i></div></div><select required name="jawaban_benar[]" id="jawaban_benar" class="form-control jawaban_benar"><option value="" selected disabled >-- \t Silahkan Pilih \t --</option><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="e">E</option></select></div></div></div></div></div>`;
        }

        function indexOfSoalUjian() {
            $('.soalUjian').each(function(index, element) {
                newNumber = index + 1; // 1-based index
                $(this).attr('id', 'soal_' + newNumber); // Update id
                $(this).attr('data-id', newNumber); // Update data-id
                $(element).find(".card-header h6").text(`Soal No.\t${newNumber}`); // Update nomor
            });

            // jika nomer soal habis maka data nomer soal akan di reset
            (nomorSoal == 1) ? nomorSoal = 1 : nomorSoal--;

            // jika nomer soal kurang dari 50 maka tombol tambah soal akan di enable
            if (nomorSoal <= 50) {
                $("#tambahSoal").attr("disabled", false);
            }
        }

        action(); //
        function action() {
            $(document).on("click", ".hapus_soal", function() {
                const id = $(this).closest(".card").data('id');
                const soalTidakKosong = !$(this).closest(".card").find(".soal_ujian").summernote('isEmpty');

                if (soalTidakKosong) {
                    Swal.fire({
                        icon: 'warning',
                        html: 'Anda yakin ingin menghapus soal nomer ' + id + ' ? <hr> <span class="text-danger text-uppercase font-weight-bold">Soal yang sudah dihapus tidak dapat dikembalikan lagi.</span>',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: 'gray',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $(this).closest(".card").remove();
                            indexOfSoalUjian();

                            $.ajax({
                                type: "delete",
                                url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.removeColumn', $jadwal->id) }}",
                                data: {
                                    ujian_id: "{{ $ujian->id }}",
                                    soal_id: id,
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    console.log(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                                }
                            });
                        }
                    });
                } else {
                    $(this).closest(".card").remove();
                    indexOfSoalUjian();
                }
            });

            $(".btnUpdate").click(function() {
                $('.soal_ujian').each(function(i) {
                    i = 1 + i; // 1-based index

                    if ($(this).summernote('isEmpty')) {
                        alert("Soal No. " + i + " Tidak boleh kosong");
                    }
                });
            }); // end btnUpdate

            $("#formUjian").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $(".btnUpdate").attr("disabled", true);
                        $(".btnUpdate").html(
                            '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span> Loading...'
                        );
                    },
                    complete: function() {
                        $(".btnUpdate").attr("disabled", false);
                        $(".btnUpdate").html("<i class='fas fa-sync mr-1'></i> Update Ujian");
                    },
                    success: function(res) {
                        if (res.status == 200) {
                            Toast.fire({
                                icon: "success",
                                title: res.message,
                            });

                            // membuat variabel global
                            let started_at = $("input[name='started_at']").val(),
                                ended_at = $("input[name='ended_at']").val(),
                                diff;

                            if (ended_at != "") { // jika ended_at tidak kosong
                                let start = moment(started_at, "HH:mm:ss");
                                let end = moment(ended_at, "HH:mm:ss");

                                (start.isBefore(end)) ? diff = end.diff(start, 'minutes') : diff = 0;

                                $("input[name='durasi']").attr("readonly", true);
                                $("input[name='durasi']").val(diff);
                            } else {
                                $("input[name='durasi']").removeAttr("readonly");
                            }

                        } else {
                            Swal.fire({
                                icon: "error",
                                html: res.message,
                            });
                        }
                    },
                }); // end ajax
            });
        }
    </script>
@endpush
