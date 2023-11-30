@extends('layouts.dashboard')

@section('title', 'Edit Ujian Essay, ' . $jadwal->matkul->nama . ' - ' . $jadwal->kelas->kode)

@section('content')
    @if (Session::has('success') || Session::has('error'))
        <div class="alert_success" data-flashdata="{{ Session::get('success') }}"></div>
        <div class="alert_error" data-flashdata="{{ Session::get('error') }}"></div>
    @endif

    <div class="container-fluid">

        <form id="formUjian"
            action="{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.essay.update', encrypt($jadwal->id)) }}" method="POST"
            enctype="multipart/form-data" autocomplete="off">
            @csrf
            @method('PUT')

            <input type="hidden" name="jadwal_id" value="{{ encrypt($jadwal->id) }}">

            <div class="row sticky">
                <div class="col-lg-12">
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
                                    @yield('title')
                                </h5>

                                <button disabled type="submit" class="btn btnUpdate btn-warning btn-sm">
                                    <i class="fas fa-sync mr-1"></i> Update Ujian
                                </button>
                            </div>
                        </div>
                    </div>
                </div> {{-- /.col-lg-12 --}}
            </div> {{-- /.row --}}

            <div class="row">
                <div class="col-lg-5">
                    @include('dashboard.dosen.ujian.soal._sub._form-edit-ujian')
                </div> {{-- end col --}}

                <div class="col-lg-7">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="font-weight-bold m-0 p-0 ml-2" id="soalUjian">
                                    Soal Ujian
                                </h5>
                            </div>
                        </div>
                    </div>

                    <div id="soal_ujian" class="fetch_soal">
                    </div>

                    <button type="button" id="tambahSoal" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Soal
                    </button>

                </div> {{-- End col --}}
            </div> {{-- End row --}}
        </form>
    </div> {{-- /.container-fluid --}}
@endsection

@push('js')
    <script>
        let nomorSoal = "{{ $soalEssays->count() }}" || 1,
            checkIfNotHasSoal = "{{ $soalEssays->isEmpty() }}";

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

            function initSelect2(id, placeholder, dropdownParent) { // fungsi untuk inisialisasi select2
                let dropdownParentVal = null;

                if (dropdownParent) { // jika ada dropdown parent
                    dropdownParentVal = $(dropdownParent);
                }

                $(id).select2({
                    placeholder: placeholder,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: dropdownParentVal,
                });
            }

            initSelect2('#random_soal', 'Silahkan Pilih');
            initSelect2('#tipe_ujian', 'Silahkan Pilih');
            initSelect2('#lihat_hasil', 'Silahkan Pilih');
            initSelect2('#status_ujian', 'Silahkan Pilih');

            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.essay.fetch', encrypt($jadwal->id)) }}",
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

                    $(".fetch_soal").html(res); // merender soal dari response ajax
                    summerNote();

                    $(".soalUjian .card-header .d-flex:gt(0)").append(`
                        <button type="button" class="btn btn-danger btn-sm ml-1 hapus_soal" data-id="${nomorSoal}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `);
                }
            }); // End ajax

            if (nomorSoal >= 50) { // Ini untuk menonaktifkan tombol tambah soal
                $("#tambahSoal").attr("disabled", true);
            }

            nomorSoal = parseInt(nomorSoal) + 1; // Ini untuk menambahkan nomor soal

            $("#tambahSoal").click(function(e) {
                $("#soal_ujian").append(containerSoal(nomorSoal)); // Ini untuk menambahkan soal
                summerNote(); // Ini untuk menginisialisasi summernote

                $(".soalUjian:last .card-header .d-flex").append(`
                    <button type="button" class="btn btn-danger btn-sm ml-1 hapus_soal"
                        data-id="${nomorSoal}">
                        <i class="fas fa-trash"></i>
                    </button>
                `);

                $(".soalUjian:last .card-header").addClass("p-2");
                $(".soalUjian:last .card-header h6").addClass("ml-2");

                $("html, body").animate({
                    scrollTop: $(".soalUjian:last").offset().top -
                        0 // Ini untuk scroll ke soal yang baru ditambahkan
                }, 0);

                if (nomorSoal == 50) { // Ini untuk menonaktifkan tombol tambah soal
                    $("#tambahSoal").attr("disabled", true);

                    Toast.fire({
                        icon: 'info',
                        html: '<span class="ml-2 font-weight-bold">Soal ujian sudah MAXIMAL</span>',
                    });
                }

                nomorSoal++;
            });

            // ==================== SECTION FUNCTION ==================== //

            function containerSoal(nomer) {
                return `<div class='card soalUjian' id='soal_${nomer}' data-id='${nomer}'><div class='card-header'><div class='d-flex justify-content-between align-items-center'><h6 class='font-weight-bold m-0 p-0'>Soal No. <span class="ml-1">${nomer}</span></h6></div></div><div class='card-body p-3'><div class='form-group m-0 p-0'><textarea data-id='${nomer}' required name='soal[]' id='soal' class='form-control soal_ujian' rows='5' placeholder='Masukkan pertanyaan soal.'></textarea></div></div></div>`;
            }

            let newNumber;

            function indexOfSoalUjian() {
                $('.soalUjian').each(function(index, element) { // Ini untuk mengubah nomor soal
                    newNumber = index + 1; // Ini untuk mengubah nomor soal
                    $(this).attr('id', 'soal_' + newNumber); // Ini untuk mengubah id soal
                    $(this).attr('data-id', newNumber); // Ini untuk mengubah data-id soal
                    $(element).find(".card-header h6").text("Soal No. " +
                    newNumber); // Ini untuk mengubah nomor soal
                });

                (nomorSoal == 1) ? nomorSoal = 1: nomorSoal--; // Ini untuk mengurangi nomor soal

                if (nomorSoal <= 50) { // Ini untuk mengaktifkan tombol tambah soal jika nomor soal kurang dari 50
                    $("#tambahSoal").attr("disabled", false);
                }
            }

            function summerNote() {
                $(".soal_ujian").summernote({
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['misc', ['fullscreen', 'codeview', 'help']],
                        ['insert', ['link', 'picture', 'video', 'audio']],
                    ],
                    height: 150,
                    placeholder: "Masukkan pertanyaan soal.",
                    maximumImageFileSize: 500 * 1024, // 500 KB
                    callbacks: {
                        onPaste: function(e) {
                            var bufferText = ((e.originalEvent || e).clipboardData || window
                                .clipboardData).getData('Text'); // get text/plain
                            e.preventDefault(); // prevent default paste action
                            bufferText = bufferText.replace(/\r?\n/g, '<br>'); // replace new line
                            document.execCommand('insertHtml', false,
                            bufferText); // insert text manually
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
            } // End function summerNote

            action();

            function action() {
                $(document).on("click", ".hapus_soal", function() {
                    const id = $(this).closest(".card").data('id');
                    const soalTidakKosong = !$(this).closest(".card").find(".soal_ujian").summernote(
                        'isEmpty');

                    if (soalTidakKosong) { // Ini untuk mengecek apakah soal sudah diisi atau belum
                        Swal.fire({
                            icon: 'warning',
                            html: 'Anda yakin ingin menghapus soal nomer ' + id +
                                ' ? <hr> <span class="text-danger text-uppercase font-weight-bold">Soal yang sudah dihapus tidak dapat dikembalikan lagi.</span>',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: 'gray',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal',
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {

                                $(this).closest(".card").remove(); // Ini untuk menghapus soal
                                indexOfSoalUjian(); // Ini untuk mengubah nomor soal

                                $.ajax({
                                    type: "delete",
                                    url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.essay.removeColumnSoal', $jadwal->id) }}",
                                    data: {
                                        ujian_id: "{{ $ujian->id }}",
                                        soal_id: id,
                                    },
                                    error: function(xhr, ajaxOptions, thrownError) {
                                        console.log(xhr.status + "\n" + xhr
                                            .responseText + "\n" + thrownError);
                                    }
                                });
                            }
                        });
                    } else { // Ini untuk menghapus soal jika soal belum diisi
                        $(this).closest(".card").remove();
                        indexOfSoalUjian();
                    }
                }); // end hapus soal

                $(".btnUpdate").click(function() { // btnUpdate
                    $('.soal_ujian').each(function(i) { // melooping soal_ujian
                        i = 1 + i; // mencari index soal_ujian

                        if ($(this).summernote('isEmpty')) { // jika soal_ujian kosong
                            alert("Soal No. " + i + " Tidak boleh kosong"); // alert soal kosong
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
                            $(".btnUpdate").html(
                                "<i class='fas fa-sync mr-1'></i> Update Ujian");
                        },
                        success: function(res) {
                            if (res.status == 200) {
                                Toast.fire({
                                    icon: "success",
                                    title: res.message,
                                });

                                let started_at = $("input[name='started_at']").val(),
                                    ended_at = $("input[name='ended_at']").val(),
                                    diff;

                                if (ended_at != "") { // Ini untuk menghitung durasi ujian
                                    let start = moment(started_at, "HH:mm:ss"),
                                        end = moment(ended_at, "HH:mm:ss");

                                    // Ini untuk menghitung selisih waktu
                                    (start.isBefore(end)) ? diff = end.diff(start, 'minutes'):
                                        diff = 0;

                                    $("input[name='durasi']").attr("readonly",
                                    true); // Ini untuk mengunci input durasi
                                    $("input[name='durasi']").val(
                                    diff); // Ini untuk mengisi durasi
                                } else {
                                    $("input[name='durasi']").removeAttr(
                                    "readonly"); // Ini untuk mengunci input durasi
                                }
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    html: res.message,
                                });
                            }
                        }, // end success
                    }); // end ajax

                }); // end formUjian
            } // End function action

        }); // End document ready
    </script>
@endpush
