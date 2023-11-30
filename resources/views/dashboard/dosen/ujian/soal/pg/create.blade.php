@extends('layouts.dashboard')

@section('title', 'Buat Ujian PG | ' . $jadwal->matkul->nama . ' - ' . $jadwal->kelas->kode)

@section('content')
    <div class="container-fluid">

        <form id="formUjian" action="{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.store') }}" method="POST"
            enctype="multipart/form-data" autocomplete="off">
            @csrf

            <input type="hidden" name="jadwal_id" value="{{ encrypt($jadwal->id) }}">

            <div class="row sticky">
                <div class="col-lg-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header p-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <a href="{{ route('manajemen.kuliah.jadwal.dosen.ujian.index') }}"
                                        class="btn btn-primary btn-back btn-sm mr-1">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                    <a href="#" class="btn btn-info mr-1 btnImport btn-sm">
                                        <i class="fas fa-file-import mr-1"></i> Import
                                    </a>
                                </div>

                                <h5 class="m-0 p-0 font-weight-bold ml-1">
                                    Buat Ujian PG, {{ $jadwal->matkul->nama }} - {{ $jadwal->kelas->kode }}
                                </h5>
                                <button type="submit" class="btnSimpan btn btn-success btn-sm">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('dashboard.dosen.ujian.soal._sub._information-create')

            <div class="row">
                <div class="col-lg-5">
                    @include('dashboard.dosen.ujian.soal._sub._form-create-ujian')
                </div>
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

                    <div id="soal_ujian">
                    </div>

                    <button type="button" id="tambahSoal" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Tambah Soal
                    </button>
                </div>
            </div> {{-- Row --}}
        </form> {{-- Form --}}
    </div> {{-- Container Fluid --}}

    @include('dashboard.dosen.ujian.soal.pg._modal-import')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (localStorage.getItem(`${noIndukUser}_fromDashboard`) == "true") {
                $(".btn-back").attr("href", "{{ route('dosen.dashboard') }}");
                $(".btn-back").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
                $("a").click(function() {
                    localStorage.removeItem(`${noIndukUser}_fromDashboard`);
                });
            }

            $(".btnImport").click(function(e) {
                e.preventDefault();

                $("#modalImport").modal("show");
            });

            // ==================== Global Variable ==================== //

            let nomorSoal = 1;

            // ==================== Event Section ==================== //

            $("#downloadTemplate").click(templateExcel);

            dropdownSelect();

            $("#soal_ujian").html(containerSoal(nomorSoal));
            summerNote();

            action(); // End action

            nomorSoal += 1; // Nomer soal 2 dan seterusnya

            $("#tambahSoal").click(function(e) {
                $("#soal_ujian").append(containerSoal(nomorSoal));
                summerNote();

                $(".soalUjian:last .card-header .d-flex").append(`
                    <button type="button" class="btn btn-danger btn-sm ml-1 hapus_soal"
                        data-id="${nomorSoal}">
                        <i class="fas fa-trash"></i>
                    </button>
                `);

                $(".soalUjian:last .card-header").addClass("p-2");
                $(".soalUjian:last .card-header h6").addClass("ml-2");

                $("html, body").animate({
                    scrollTop: $(".soalUjian:last").offset().top - 135
                }, 0);

                if (nomorSoal == 50) {
                    $("#tambahSoal").attr("disabled", true);

                    Toast.fire({
                        icon: 'info',
                        html: '<span class="ml-2 font-weight-bold">Soal ujian sudah MAXIMAL</span>',
                    });
                }

                nomorSoal++;
            }); // End tambahSoal

            // ==================== Function Section ==================== //

            function containerSoal(nomer) {
                return `<div class="card soalUjian" id="soal_${nomer}"><div class="card-header"><div class="d-flex justify-content-between align-items-center"><h6 class="font-weight-bold m-0 p-0">Soal No. \t ${nomer}</h6></div></div><div class="card-body p-3"><div class="form-group m-0 p-0"><textarea required name="soal[]" id="soal" class="form-control soal_ujian" rows="5" placeholder="Masukkan soal ujian."></textarea></div><div class="form-group m-0 p-0 row"><div class="col-lg-6 mb-3"><label for="pilihan_a" class="font-weight-bold">Pilihan A</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">A</div></div><input required type="text" name="pilihan_a[]" id="pilihan_a" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan A."></div></div><div class="col-lg-6 mb-3"><label for="pilihan_d" class="font-weight-bold">Pilihan D</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">D</div></div><input required type="text" name="pilihan_d[]" id="pilihan_d" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan D."></div></div></div><div class="form-group m-0 p-0 row"><div class="col-lg-6 mb-3"><label for="pilihan_b" class="font-weight-bold">Pilihan B</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">B</div></div><input required type="text" name="pilihan_b[]" id="pilihan_b" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan B."></div></div><div class="col-lg-6 mb-3"><label for="pilihan_e" class="font-weight-bold">Pilihan E</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">E</div></div><input required type="text" name="pilihan_e[]" id="pilihan_e" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan E."></div></div></div><div class="form-group m-0 p-0 row"><div class="col-lg-6 mb-3"><label for="pilihan_c" class="font-weight-bold">Pilihan C</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text">C</div></div><input required type="text" name="pilihan_c[]" id="pilihan_c" class="form-control" placeholder="Masukkan Jawaban untuk Pilihan C."></div></div><div class="col-lg-6"><label for="jawaban_benar" class="font-weight-bold">Jawaban Benar</label><div class="input-group"><div class="input-group-prepend"><div class="input-group-text"><i class="fas fa-check-circle text-success"></i></div></div><select required name="jawaban_benar[]" id="jawaban_benar" class="form-control jawaban_benar"><option value="" selected disabled >-- \t Silahkan Pilih \t --</option><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option><option value="e">E</option></select></div></div></div></div></div>`;
            }

            function templateExcel() {
                $(this).attr('download', true);
                window.location.href = "{{ asset('assets/file/ujian/template/template_soal_ujian_pg.xlsx') }}";
            }

            function dropdownSelect() {
                initSelect2("#random_soal", "Silahkan Pilih");
                initSelect2("#tipe_ujian", "Silahkan Pilih");
                initSelect2("#lihat_hasil", "Silahkan Pilih");
                initSelect2("#random_soal_import", "Silahkan Pilih", "#modalImport");
                initSelect2("#tipe_ujian_import", "Silahkan Pilih","#modalImport");
                initSelect2("#lihat_hasil_import", "Silahkan Pilih","#modalImport");
            }

            function summerNote() { // summernote
                $(".soal_ujian").summernote({
                    toolbar:[
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['misc', ['fullscreen', 'codeview', 'help']],
                        ['insert', ['link', 'picture', 'video', 'audio']],
                    ],
                    height: 150,
                    placeholder: "Masukkan pertanyaan soal.",
                    maximumImageFileSize: 500 * 1024, // 500 KB
                    callbacks: {
                        // callback for pasting text only (no formatting)
                        onPaste: function(e) {
                            var bufferText = ((e.originalEvent || e).clipboardData || window
                                .clipboardData).getData('Text');
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

            function indexOfSoalUjian() {
                $('.soalUjian').each(function(index, element) {
                    var newNum = index + 1; // 1-based index
                    $(this).attr('id', `soal_${newNum}`); // Update id
                    $(element).find(".card-header h6").text(`Soal No. \t ${newNum}`); // Update nomor
                });

                // jika nomer soal habis maka data nomer soal akan di reset
                (nomorSoal == 1) ? nomorSoal = 1: nomorSoal--;

                // jika nomer soal kurang dari 50 maka tombol tambah soal akan di enable
                if (nomorSoal <= 50) {
                    $("#tambahSoal").attr("disabled", false);
                }
            }

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

            function action() {
                $(document).on("click", ".hapus_soal", function() {
                    $(this).closest(".card").remove();

                    indexOfSoalUjian();
                });

                $(".btnSimpan").click(function() {
                    $('.soal_ujian').each(function(i) {
                        i = 1 + i; // 1-based index

                        if ($(this).summernote('isEmpty')) {
                            alert("Soal No. "+i+" Tidak boleh kosong");
                        }
                    });
                }); // end btnSimpan

                $(document).on('change', 'input[type="file"]', function(event) {
                    let fileName = $(this).val();

                    if (fileName == undefined || fileName == "") {
                        $(this).next('.custom-file-label').html('Tidak ada gambar yang dipilih..')
                    } else {
                        $(this).next('.custom-file-label').html(event.target.files[0].name);
                    }
                }); // end change file
            }

            $("#formImportUjian").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitImport').attr('disabled', true);
                        $('.submitImport').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.submitImport').removeAttr('disabled');
                        $('.submitImport').html(
                            'Import <i class="fas fa-file-import ml-1"></i>');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            if (res.tipe == 'validation') {
                                $.each(res.errors, function(prefix, val) {
                                    $('span.' + prefix + '_error').text(val[0]);
                                    $('#' + prefix + "_import").addClass('is-invalid');
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.message,
                                })
                            }
                        } else {
                            Swal.fire({
                                icon: 'success',
                                html: res.message,
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        "{{ route('manajemen.kuliah.jadwal.dosen.ujian.show', encrypt($jadwal->id)) }}"
                                }
                            });
                        }
                    }
                }); // end ajax
            }); // end formImportUjian
        });
    </script>
@endpush
