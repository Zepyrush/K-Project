@extends('layouts.dashboard')

@section('title', 'Ujian Essay ('.strtoupper($jadwal->ujian->tipe_ujian).') - ' . $jadwal->matkul->nama)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h5 class="m-0 p-0 font-weight-bold">
                            <i class="fas fa-file-alt text-primary mr-2"></i>
                            @yield('title')
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="alert card card-primary card-outline alert-dismissible in-table fade show"
                    role="alert">
                    <strong>Keyboard Shortcut:</strong>
                    <ul class="info-ul">
                        <li>Left arrow: Soal sebelumnya</li>
                        <li>Right arrow: Soal selanjutnya</li>
                        <li>Shift: Ragu-ragu</li>
                        <li>Esc: Kirim ujian (Jika sudah mengerjakan semua)</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8" id="soal">

                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="m-0 p-0 p-2" id="judulSoal"></h6>
                            <h6 id="timerCont" class="m-0 px-2 py-1" style="border-radius: 4px;">
                                <i class="fas fa-clock mr-1"></i>
                                <span id="timer">00:00:00</span>
                            </h6>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="card-text soal" id="pertanyaanSoal"></div>

                        <hr>

                        <div id="containerSoal">
                            <input type="hidden" name="id" id="ujianHasilId">

                            <div id="jawabContainer" class=""></div>
                        </div>
                    </div> {{-- End card-body --}}

                    <div class="card-footer p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <button id="prevSoal" class="btn btn-sm btn-primary mr-1">
                                <i class="fas fa-arrow-left mr-1"></i> Sebelumnya
                            </button>

                            <div class="bg-warning px-2 py-1" style="border-radius: 5px;">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="btnRagu">
                                    <label class="custom-control-label cursor_pointer text-white" for="btnRagu">
                                        Ragu-ragu
                                    </label>
                                </div>
                            </div>

                            <button disabled id="nextSoal" class="btn btn-sm btn-primary ml-1">
                                Selanjutnya <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div> {{-- End card --}}
            </div> {{-- End col --}}

            <div class="col-lg-4">

                {{-- Button kirim ujian --}}
                <button disabled id="btnEndUjian" class="btn btn-block absen disabled bg-primary mb-3">
                    <h6 class="m-0 p-0 text-white font-weight-bold text-center">
                        Kirim Ujian
                    </h6>
                </button>

                <div class="card card-primary card-outline sticky">

                    <div class="card-header">
                        <h6 class="m-0 p-0 font-weight-bold">Daftar Soal</h6>
                    </div>

                    <div class="card-body">
                        <div id="daftarSoal"></div>

                        <hr>

                        {{-- Informasi jika biru sudah dikerjakan --}}
                        <div class="d-flex align-items-center">
                            <div class="mr-2 bg-success" style="width: 20px; height: 20px;"></div>
                            <p class="m-0 p-0 font-weight-bold">Sudah dikerjakan</p>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="mr-2 bg-warning" style="width: 20px; height: 20px;"></div>
                            <p class="m-0 p-0 font-weight-bold">Ragu-ragu</p>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="mr-2 bg-secondary" style="width: 20px; height: 20px;"></div>
                            <p class="m-0 p-0 font-weight-bold">Belum Dikerjakan</p>
                        </div>

                    </div>
                </div> {{-- End card --}}
            </div> {{-- End col --}}
        </div> {{-- End row --}}
    </div> {{-- container-fluid --}}
@endsection

@push('js')
    <script>
        // ================ GLOBAL VARIABLE ================== //
        const ujianIdHash = "{{ encrypt($jadwal->ujian->id) }}";
        const jadwalIdHash = "{{ encrypt($jadwal->id) }}";
        const ujianMhsIdHash = "{{ encrypt($ujianMahasiswa->id) }}";
        const ujianMhsId = "{{ $ujianMahasiswa->id }}";
        const ujianId = "{{ $ujian->id }}";
        const endedAt = new Date("{{ $ujianMahasiswa->ended_at }}").getTime();
        const btnPrevSoal = $("#prevSoal");
        const btnNextSoal = $("#nextSoal");
        const btnRagu = $("#btnRagu");
        const btnEndUjian = $("#btnEndUjian");
        const jadwalEndedAt = "{{ $jadwal->ended_at }}";
        const itemLsSoal = `currentPageSoal_${ujianMhsId}_${ujianId}`;
        const itemLsDaftarSoal = `currentPageDaftarSoal_${ujianMhsId}_${ujianId}`;
        const itemLsDaftarSoalScroll = `daftarSoalScroll_${ujianMhsId}_${ujianId}`;

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on("keydown", function(e) {
                if (e.keyCode == 37) { // left arrow
                    btnPrevSoal.trigger("click");
                } else if (e.keyCode == 39) { // right arrow
                    btnNextSoal.trigger("click");
                }

                // jika mau mengirim ujian dengan keyboard
                if (!btnEndUjian.hasClass("disabled")) { // jika tombol btnEndUjian tidak disabled
                    if (e.keyCode == 27) { // esc
                        btnEndUjian.trigger("click");
                    }
                }

                // rgu-ragu
                if (e.keyCode == 16) { // shift
                    btnRagu.trigger("click");
                }
            });
        });

        // ================ MAIN UJIAN ================== //
        let timerInterval = setInterval(() => { // Timer
            const timer = document.getElementById('timer');
            let now = new Date().getTime();
            let distance = endedAt - now;

            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            $("#timerCont").addClass("bg-primary");

            if (hours == 0 && minutes < 5) {
                $("#timerCont").removeClass("bg-primary").addClass("bg-warning");
            }

            if (hours < 10) {
                hours = "0" + hours
            }
            if (minutes < 10) {
                minutes = "0" + minutes
            }
            if (seconds < 10) {
                seconds = "0" + seconds
            }

            timer.innerHTML = hours + ":" + minutes + ":" + seconds;

            if (distance < 0) {
                clearInterval(timerInterval);

                $("#timerCont").removeClass("bg-warning").addClass("bg-danger");
                $("#timerCont").find("i").remove();
                $("#timer").html("Waktu Habis");

                $("#jawabContainer").css({
                    "pointer-events": "none",
                    "opacity": "0.5",
                });

                $(".custom-checkbox").css({
                    "pointer-events": "none",
                    "opacity": "0.5",
                });

                $("#btnEndUjian").removeClass("bg-primary").addClass("bg-secondary");
                $("#btnEndUjian h6").html("Mengalihkan...");

                kirimUjian();
            }
        }, 1000); // end timer

        btnPrevSoal.on("click", handlerPaginateSoal);
        btnNextSoal.on("click", handlerPaginateSoal);

        let currentPageSoal = localStorage.getItem(itemLsSoal) || 1;
        fetchSoal(); // menjalankan fungsi fetchSoal

        function fetchSoal(page = currentPageSoal) {
            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.essay.fetchSoal', encrypt($jadwal->id)) }}",
                data: {
                    ujian_mahasiswa_id: ujianMhsIdHash,
                    soal: page,
                },
                dataType: "json",
                success: function(res) {

                    data = res.data[0];
                    soal = res.data[0].soal_ujian_essay;

                    // random soal
                    $("#judulSoal").html("<b>Soal No. " + res.current_page +
                        "</b> <span class='text-muted'>dari " + res.last_page + " Soal</span>");

                    $("#pertanyaanSoal").html(soal.pertanyaan);

                    $("#jawabContainer").html(`
                        <textarea required name="jawab" id="${data.id}" class="form-control soal_ujian"
                            rows="5" placeholder="Masukkan Jawaban Anda."></textarea>
                    `);

                    summerNote();

                    $("#ujianHasilId").val(data.id);

                    btnRagu.val(data.id);
                    (data.ragu != 1) ? btnRagu.prop("checked", false) : btnRagu.prop("checked", true);

                    // ================ CHECK JAWABAN ================== //
                    if (data.jawaban) {
                        $(`textarea[name='jawab']`).summernote("code", data.jawaban);
                    };

                    // ================ DISABLE BUTTON PREV SOAL ================== //
                    btnPrevSoal.removeAttr("disabled");
                    btnPrevSoal.data("page", res.current_page - 1);
                    if (res.prev_page_url == null) {
                        btnPrevSoal.attr("disabled", true);
                    }

                    btnNextSoal.data("page", res.current_page + 1);
                    btnNextSoal.removeAttr("disabled");
                    if (res.next_page_url == null) {
                        btnNextSoal.attr("disabled", true);
                    }
                } // end success
            }); // end ajax
        }; // end fetchSoal

        function handlerPaginateSoal() {
            const page = $(this).data("page");
            fetchSoal(page);
            localStorage.setItem(itemLsSoal, page);
        }

        function summerNote() {
            $("textarea[name='jawab']").summernote({
                toolbar:[
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['misc', ['fullscreen']],
                ],
                focus: true,
                height: 120,
                placeholder: "Masukkan jawaban anda disini...",
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
                    onKeyup: simpanJawaban,
                }
            });
        } // end summernote function

        $(document).on("click", ".pilihanSoal", handlerPaginateSoal);

        fetchDaftarSoal();
        function fetchDaftarSoal() {
            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.essay.fetchDaftarSoal', encrypt($jadwal->id)) }}",
                data: {
                    ujian_mahasiswa_id: ujianMhsIdHash,
                },
                dataType: "JSON",
                success: function(res) {

                    let html = "", jawabanTerisi = 0,
                        currentScroll = localStorage.getItem(itemLsDaftarSoalScroll);

                    html += `<div class="" id="listSoal">`;

                    res.forEach((data, index) => {
                        if (data.jawaban != null) {jawabanTerisi++}

                        if (data.ragu == 1) {
                            bgColor = "bg-warning";
                        } else if (data.jawaban != null) {
                            bgColor = "bg-success";
                        } else {
                            bgColor = "bg-secondary";
                        }

                        html += `
                            <button type="button" class="btn btn-secondary ${bgColor} pilihanSoal"
                                id="pilihanSoal_${index + 1}" data-page="${index + 1}">
                                ${index + 1}
                            </button>
                        `;
                    });

                    html += `</div>`; // end listSoal

                    $("#daftarSoal").html(html); // menampilkan daftar soal

                    if (currentScroll) { // scroll to last position
                        $("#listSoal").scrollTop(currentScroll)
                    }

                    if (jawabanTerisi == res.length) { // jika semua soal sudah terjawab
                        btnEndUjian.removeClass("disabled");
                        btnEndUjian.removeAttr("disabled");
                    }
                },
            }); // End ajax
        } // End fetchDaftarSoal

        // ================== SIMPAN JAWABAN OTOMATIS =============== //
        function simpanJawaban() {
            let jawaban = $("textarea[name='jawab']").summernote('code');
            let ujianHasilId = $("#ujianHasilId").val();
            let tempEl = $('<div />').html(jawaban); // create a div element
            let isNotEmpty = tempEl.text().trim().replace(/ \r\n\t/g, '') !== ''; // check if the div is empty

            if (isNotEmpty) { // jika jawaban tidak kosong
                $.ajax({
                    type: "POST",
                    url: "{{ route('manajemen.kuliah.ujian.mahasiswa.essay.simpanJawaban') }}",
                    data: {
                        jawaban: jawaban,
                        id: ujianHasilId
                    },
                    dataType: "JSON",
                    success: function(res) {
                        if (res.status == "jadwal_habis") {
                            fetchSoal(); // reload soal
                        } else {
                            // scroll daftar soal
                            fetchDaftarSoal();
                            localStorage.setItem(itemLsDaftarSoalScroll, $("#listSoal").scrollTop());
                        }
                    },
                });
            }
        }

        btnRagu.on("change", function() {
            const value = $(this).val();
            const checked = $('#btnRagu:checked').length > 0; // true or false

            $.ajax({
                type: "POST",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.essay.raguRagu') }}",
                data: {
                    id: value,
                    ragu: checked ? 1 : 0, // 1 = ragu, 0 = tidak ragu
                },
                success: function(res) {
                    if (res.status == "jadwal_habis") { // jika jadwal sudah habis
                        fetchSoal();
                    } else { // jika jadwal masih berjalan
                        fetchDaftarSoal();
                        localStorage.setItem(itemLsDaftarSoalScroll, $("#listSoal").scrollTop());
                    }
                },
            });
        });

        btnEndUjian.on("click", function() { // end ujian
            Swal.fire({
                html: "<span class='text-uppercase font-weight-bold'>Apakah anda yakin ingin kirim ujian ini?</span> <hr><span class=''>Jika di kirim ujian maka ujian ini akan selesai.</span>",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonColor: '#D3D3D3s',
                confirmButtonText: 'Ya, Kirim Ujian!',
                cancelButtonText: 'Tidak',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    kirimUjian();
                }
            });
        });

        function kirimUjian() { // kirim ujian
            $.ajax({
                type: "POST",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.essay.selesaiUjian') }}",
                data: {
                    ujian_mahasiswa_id: ujianMhsIdHash,
                },
                success: function(res) {
                    localStorage.removeItem(itemLsSoal);
                    localStorage.removeItem(itemLsDaftarSoalScroll);

                    if (res.status == "nilai_kosong") {
                        let text = "<b>@yield('title') (Semester {{ $jadwal->ujian->semester }})</b>";

                        Swal.fire({
                            icon: "error",
                            html: res.message.replace(":ujian", text),
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('manajemen.kuliah.ujian.mahasiswa.index') }}";
                            }
                        });
                    } else {
                        window.location.href = "{{ route('manajemen.kuliah.ujian.mahasiswa.riwayatUjian') }}";
                    }
                },
            });
        }

        function resizeWindow() {
            var sizeWindow = $(window).width();

            if (sizeWindow > 991) {
                removeFunction();
            } else {
                removeFunction();
            }
        }

        removeFunction(); // remove function
        function removeFunction() {
            $("body").removeClass("sidebar-mini").addClass("sidebar-collapse");
            $(".navbar-nav i.fas.fa-bars").remove();
            $("a.nav-link[role='button']").removeAttr("data-widget").addClass("cursor_default");
            $("#logoutButton").addClass("d-none");
            $("#logoMiddle").removeClass("d-none");
        }
    </script>
@endpush
