@extends('layouts.dashboard')

@section('title', 'Ujian Pilihan Ganda ('.strtoupper($jadwal->ujian->tipe_ujian).') - ' . $jadwal->matkul->nama)

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
                        <li>A, B, C, D, E : Pilih jawaban</li>
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-8 no-copy" id="soal" oncopy="return false" onselectstart="return false">

                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="m-0 p-0 p-2" id="judulSoal"></h6>
                            <h6 id="timerCont" class="m-0 px-2 py-1 bg-primary" style="border-radius: 4px;">
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

                            <div id="pilihanSoal" class=""></div>
                        </div>
                    </div> {{-- End card-body --}}

                    <div class="card-footer p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <button id="prevSoal" class="btn btn-sm btn-primary mr-2">
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

                            <button disabled id="nextSoal" class="btn btn-sm btn-primary ml-2">
                                Selanjutnya <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div> {{-- End card --}}
            </div> {{-- End col --}}

            <div class="col-lg-4">

                {{-- Button kirim ujian --}}
                <button disabled id="btnEndUjian" class="btn btn-block absen bg-primary mb-3 disabled">
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

        const ujianId = "{{ encrypt($jadwal->ujian->id) }}";
        const jadwalIdHash = "{{ encrypt($jadwal->id) }}";
        const currentSoal = "{{ $ujianMahasiswa->id }}";
        const ujianMhsId = "{{ encrypt($ujianMahasiswa->id) }}";
        const endedAt = new Date("{{ $ujianMahasiswa->ended_at }}").getTime();
        const btnPrevSoal = $("#prevSoal");
        const btnNextSoal = $("#nextSoal");
        const btnRagu = $("#btnRagu");
        const btnEndUjian = $("#btnEndUjian");
        const hasEnded_at = "{{ $jadwal->ended_at }}";
        let lsSoal = `currentPageSoal_${currentSoal}`;
        let lsDsScroll = `daftarSoalScroll_${currentSoal}`;
        let currentPageSoal = localStorage.getItem(lsSoal) || 1;
        let soal, data, bgColor;

        // ================ TIMER UJIAN ================== //

        let timerInterval = setInterval(() => {
            const timer = document.getElementById('timer');

            let now = new Date().getTime();
            let distance = endedAt - now;
            let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            (hours == 0 && minutes < 5) ? $("#timerCont").addClass("bg-warning")
                : $("#timerCont").removeClass("bg-warning");
            (hours < 10) ? hours = "0" + hours : hours;
            (minutes < 10) ? minutes = "0" + minutes : minutes;
            (seconds < 10) ? seconds = "0" + seconds : seconds;

            timer.innerHTML = hours + ":" + minutes + ":" + seconds;

            if (distance < 0) { // jika waktu habis
                clearInterval(timerInterval);

                $("#timerCont").addClass("bg-danger");
                $("#timerCont").find("i").remove();
                $("#timer").html("Waktu Habis");

                $("#pilihanSoal").css({"pointer-events": "none","opacity": "0.5"});
                $(".custom-checkbox").css({"pointer-events": "none","opacity": "0.5",});

                $("#btnEndUjian").addClass("bg-secondary");
                $("#btnEndUjian h6").html("Mengalihkan...");

                kirimUjian(); // menjalankan fungsi kirimUjian
            }
        }, 1000);

        // ================ EVENT LISTENER ================== //

        fetchSoal(); // menjalankan fungsi fetchSoal
        fetchDaftarSoal(); // menjalankan fungsi fetchDaftarSoal

        btnPrevSoal.on("click", handlerPaginateSoal);
        btnNextSoal.on("click", handlerPaginateSoal);

        $(document).on("click", ".pilihanSoal", handlerPaginateSoal);
        $(document).on("change", "input[name='pilihan']", simpanJawaban);

        btnRagu.on("change", simpanRagu);
        btnEndUjian.on("click", clickBtnEndUjian);

        $(document).ready(readyFunction);
        $(window).bind('resize', resizeWindow).trigger('resize');

        // Mencegah pengguna kembali ke halaman sebelumnya
        history.pushState(null, null, document.URL);
        $(window).on('popstate', function () {
            alert('Anda tidak diizinkan untuk kembali ke halaman sebelumnya selama ujian.');
        });

        // ================ FUNCTION SECTION ================== //

        // menampilkan soal sesuai dengan nomor soal
        function fetchSoal(page = currentPageSoal) {
            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.pg.fetchSoal', 'jadwal_id') }}"
                    .replace('jadwal_id', jadwalIdHash),
                data: {
                    ujian_mahasiswa_id: ujianMhsId,
                    soal: page,
                },
                dataType: "json",
                success: function(res) {
                    data = res.data[0];
                    soal = res.data[0].soal_ujian_pg;

                    // random soal
                    $("#judulSoal").html("<b>Soal No. " + res.current_page +
                        "</b> <span class='text-muted'>dari " + res.last_page + " Soal</span>");

                    $("#pertanyaanSoal").html(soal.pertanyaan);

                    $("#pilihanSoal").html(`
                        <div class="p-0 pilihan_a d-flex mb-2">
                            <span class="mr-2 font-weight-bold">A. </span>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pilihan_a" name="pilihan" class="custom-control-input" value="a">
                                <label class="custom-control-label" for="pilihan_a">${soal.pilihan_a}</label>
                            </div>
                        </div>
                        <div class="p-0 pilihan_b d-flex mb-2">
                            <span class="mr-2 font-weight-bold">B. </span>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pilihan_b" name="pilihan" class="custom-control-input" value="b">
                                <label class="custom-control-label" for="pilihan_b">${soal.pilihan_b}</label>
                            </div>
                        </div>
                        <div class="p-0 pilihan_c d-flex mb-2">
                            <span class="mr-2 font-weight-bold">C. </span>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pilihan_c" name="pilihan" class="custom-control-input" value="c">
                                <label class="custom-control-label" for="pilihan_c">${soal.pilihan_c}</label>
                            </div>
                        </div>
                        <div class="p-0 pilihan_d d-flex mb-2">
                            <span class="mr-2 font-weight-bold">D. </span>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pilihan_d" name="pilihan" class="custom-control-input" value="d">
                                <label class="custom-control-label" for="pilihan_d">${soal.pilihan_d}</label>
                            </div>
                        </div>
                        <div class="p-0 pilihan_e d-flex mb-2">
                            <span class="mr-2 font-weight-bold">E. </span>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pilihan_e" name="pilihan" class="custom-control-input" value="e">
                                <label class="custom-control-label" for="pilihan_e">${soal.pilihan_e}</label>
                            </div>
                        </div>
                    `);

                    // scrollToSoal();

                    $("#ujianHasilId").val(data.id); // Set id ujian hasil

                    // ================ CHECK JAWABAN & RAGY ================== //
                    (data.jawaban) ? $(`#pilihan_${data.jawaban}`).prop('checked', true) : null;

                    btnRagu.val(data.id);
                    (data.ragu != 1) ? btnRagu.prop("checked", false)
                        : btnRagu.prop("checked", true);

                    // ================ DISABLE BUTTON PREV SOAL ================== //
                    btnPrevSoal.removeAttr("disabled"); // Enable button
                    btnPrevSoal.data("page", res.current_page - 1); // Set data page
                    if (res.prev_page_url == null) { // Jika tidak ada halaman sebelumnya
                        btnPrevSoal.attr("disabled", true); // Disable button
                    }

                    btnNextSoal.data("page", res.current_page + 1); // Set data page
                    btnNextSoal.removeAttr("disabled"); // Enable button
                    if (res.next_page_url == null) { // Jika tidak ada halaman selanjutnya
                        btnNextSoal.attr("disabled", true); // Disable button
                    }
                }
            });
        }; // End of fetchSoal

        // menampilkan daftar soal
        function fetchDaftarSoal() {
            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.pg.fetchDaftarSoal', ':id') }}"
                    .replace(':id', jadwalIdHash),
                data: {
                    ujian_mahasiswa_id: ujianMhsId,
                },
                dataType: "JSON",
                success: function(res) {

                    let html = "",
                        jawabanTerisi = 0,
                        currentScroll = localStorage.getItem(lsDsScroll);

                    html += `<div class="" id="listSoal">`;

                    res.forEach((data, index) => { // Looping data soal

                        (data.jawaban != null) ? jawabanTerisi++ : jawabanTerisi;

                        bgColor = (data.ragu == 1) ? "bg-warning"
                            : (data.jawaban != null) ? "bg-success"
                            : "bg-secondary";

                        html += `
                            <button type="button" class="btn btn-secondary ${bgColor} pilihanSoal"
                                id="pilihanSoal_${index + 1}" data-page="${index + 1}">
                                ${index + 1}
                            </button>
                        `;
                    });

                    html += `</div>`;

                    $("#daftarSoal").html(html);

                    (currentScroll) ?
                        $("#listSoal").scrollTop(currentScroll) :
                        $("#listSoal").scrollTop(0);

                    if (jawabanTerisi == res.length) {
                        btnEndUjian.removeClass("disabled");
                        btnEndUjian.removeAttr("disabled");
                    } else {
                        btnEndUjian.attr("disabled", true);
                    }


                },
            }); // End ajax
        } // End fetchDaftarSoal

        // Fungsi untuk menyimpan jawaban
        function simpanJawaban() {
            const jawaban = $(this).val();
            const ujianHasilId = $("#ujianHasilId").val();

            $.ajax({
                type: "POST",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.pg.simpanJawaban') }}",
                data: {
                    jawaban: jawaban,
                    id: ujianHasilId,
                },
                dataType: "JSON",
                success: function(res) {
                    jadwalSudahHabis(res);
                },
            });
        }

        // Fungsi untuk menyimpan ragu ragu
        function simpanRagu() {
            const value = $(this).val();
            const checked = $('#btnRagu:checked').length > 0;

            $.ajax({
                type: "POST",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.pg.raguRagu') }}",
                data: {
                    id: value,
                    ragu: checked ? 1 : 0,
                },
                success: function(res) {
                    jadwalSudahHabis(res);
                },
            });
        }

        // Fungsi untuk mengecek apakah jadwal sudah habis atau belum
        function jadwalSudahHabis(res) {
            if (res.status == "jadwal_habis") {
                fetchSoal(); // reload soal
            } else {
                // scroll daftar soal
                fetchDaftarSoal();
                localStorage.setItem(lsDsScroll, $("#listSoal").scrollTop());
            }
        }

        // Fungsi untuk menampilkan modal konfirmasi selesai ujian
        function clickBtnEndUjian() {
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
        }

        // Fungsi untuk mengirim ujian
        function kirimUjian() {
            $.ajax({
                type: "POST",
                url: "{{ route('manajemen.kuliah.ujian.mahasiswa.pg.selesaiUjian') }}",
                data: {
                    ujian_mahasiswa_id: ujianMhsId,
                },
                success: function(res) {
                    localStorage.removeItem(lsSoal);
                    localStorage.removeItem(lsDsScroll);

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

        // Fungsi untuk menghandle pagination soal (next, prev, pilihan soal)
        function handlerPaginateSoal() {
            const page = $(this).data("page"); // mengambil data page dari button
            fetchSoal(page); // menjalankan fungsi fetchSoal dengan parameter page
            localStorage.setItem(lsSoal, page); // menyimpan data page ke local storage
        }

        // Fungsi untuk menghandle resize window
        function resizeWindow() {
            var sizeWindow = $(window).width();

            if (sizeWindow > 991) {
                scrollToSoal();
                removeFunction();
            } else {
                removeFunction();
            }
        }

        // Fungsi untuk scroll ke soal
        function scrollToSoal() {
            $("html, body").animate({
                scrollTop: $("#containerSoal").offset().top
            }, 0);
        }

        function removeFunction() { // Fungsi untuk menghapus class sidebar-collapse
            $("body").removeClass("sidebar-mini").addClass("sidebar-collapse");
            $(".navbar-nav i.fas.fa-bars").remove();
            $("a.nav-link[role='button']").removeAttr("data-widget").addClass("cursor_default");
            $("#logoutButton").addClass("d-none");
            $("#logoMiddle").removeClass("d-none");
        }

        // Fungsi yang dijalankan ketika halaman sudah siap
        function readyFunction() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // menambahkan event listener ketika tombol keyboard ditekan
            $(document).on("keydown", function(e) {
                if (e.keyCode == 37) { // left arrow
                    btnPrevSoal.trigger("click");
                } else if (e.keyCode == 39) { // right arrow
                    btnNextSoal.trigger("click");
                }

                // jika mau pilih jawaban dengan keyboard
                if (e.keyCode == 65) { // a
                    $("input[name='pilihan'][id='pilihan_a']").trigger("click");
                } else if (e.keyCode == 66) { // b
                    $("input[name='pilihan'][id='pilihan_b']").trigger("click");
                } else if (e.keyCode == 67) { // c
                    $("input[name='pilihan'][id='pilihan_c']").trigger("click");
                } else if (e.keyCode == 68) { // d
                    $("input[name='pilihan'][id='pilihan_d']").trigger("click");
                } else if (e.keyCode == 69) { // e
                    $("input[name='pilihan'][id='pilihan_e']").trigger("click");
                }

                // jika mau ragu-ragu dengan keyboard
                if (e.keyCode == 16) { // shift
                    btnRagu.trigger("click");
                }

                // jika mau mengirim ujian dengan keyboard
                if (!btnEndUjian.hasClass("disabled")) { // jika tombol btnEndUjian tidak disabled
                    if (e.keyCode == 27) { // esc
                        btnEndUjian.trigger("click");
                    }
                }
            });


            // ============== CHEATING DISABLED ================= //

            // $(document).on("contextmenu", function(e) {
            //     e.preventDefault();
            // });

            // $(document).keydown(function(e) {
            //     if (e.which === 123) {
            //         return false;
            //     }
            // });

            // $(document).keydown(function(e) {
            //     if (e.ctrlKey && e.shiftKey && e.which === 73) {
            //         return false;
            //     }
            // });

            // $('.no-copy').bind('cut copy paste', function(e) {
            //     e.preventDefault();
            // });
        }
    </script>
@endpush
