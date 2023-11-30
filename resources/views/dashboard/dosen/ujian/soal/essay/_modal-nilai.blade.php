{{-- Modal --}}
<div class="modal fade" id="modalNilai">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header p-2">
                <div class="ml-2" style="line-height: 1.4;">
                    <h5 class="modal-title font-weight-bold" id="judulModalNilai"></h5>
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
                    <div class="col-lg-9 " id="soal">
                        <div class="card card-primary card-outline">
                            <div class="card-header p-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6 class="m-0 p-0 ml-2" id="judulSoalModal"></h6>
                                    <div>
                                        <button class="btn btn-sm btn-primary mr-1 prevSoalModal">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>

                                        <button disabled class="btn btn-sm btn-primary nextSoalModal">
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <input type="hidden" name="id" id="ujianHasilIdModal">

                                <div class="card-text soal mb-2" id="pertanyaanSoalModal">
                                </div>

                                <div id="containerSoalModal">
                                </div>
                            </div> {{-- End card-body --}}
                        </div> {{-- End card --}}
                    </div> {{-- End col --}}

                    <div class="col-lg-3">

                        <div class="card card-primary card-outline">

                            <div class="card-header ">
                                <h6 class="m-0 p-0 font-weight-bold">Daftar Soal</h6>
                            </div>

                            <div class="card-body">
                                <div id="daftarSoalModal"></div>

                                <hr>

                                <div class="d-flex align-items-center">
                                    <div class="mr-2 bg-success" style="width: 20px; height: 20px;"></div>
                                    <p class="m-0 p-0 font-weight-bold">Sudah dinilai</p>
                                </div>

                                <div class="d-flex align-items-center">
                                    <div class="mr-2 bg-secondary" style="width: 20px; height: 20px;"></div>
                                    <p class="m-0 p-0 font-weight-bold">Belum dinilai</p>
                                </div>

                            </div>
                        </div> {{-- End card --}}
                    </div> {{-- End col --}}
                </div> {{-- End row --}}
            </div> {{-- End modal-body --}}

            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>

        </div> {{-- End modal-content --}}
    </div> {{-- End modal-dialog --}}
</div> {{-- End modal --}}

@push('js')
    <script>
        $(document).on("click", ".btnNilaiHasil", function (e) {
            e.preventDefault(); // prevent form submit

            // ================= Global Variable ================= //

            const ujianMhsId = $(this).data("id");
            const jadwalId = "{{ $jadwal->id }}";
            const thisURL = "{{ route('manajemen.kuliah.jadwal.dosen.ujian.getNilaiEssayMahasiswa', encrypt($jadwal->id)) }}";
            const btnPrevSoalModal = $(".prevSoalModal");
            const btnNextSoalModal = $(".nextSoalModal");
            const itemLsSoal = `currentPageSoal_${ujianMhsId}`;
            const itemLsDaftarSoal = `currentPageDaftarSoal_${ujianMhsId}`;
            const itemLsDsScroll = `dsScroll_${ujianMhsId}`;
            let currentPageSoalModal = localStorage.getItem(itemLsSoal) || 1;
            let currentPageDaftarSoal = localStorage.getItem(itemLsDaftarSoal) || 1;

            // ================= Menjalankan function ================= //
            $('[data-toggle="tooltip"]').tooltip("hide");
            $("#modalNilai").modal("show");
            btnPrevSoalModal.on("click", handlerSoalPageModal);
            btnNextSoalModal.on("click", handlerSoalPageModal);
            $(document).on("click", ".pilihanSoalModal", handlerSoalPageModal);
            $(document).on("change", "input[name='skor']", simpanNilai); // Simpan nilai ketika skor berubah
            $(document).on("change", "#komentarDosen", simpanKomentar);
            fetchSoal();
            fetchDaftarSoal();

            $(document).on("keydown", function(e) {
                if (e.keyCode == 37) { // left arrow
                    btnPrevSoalModal.trigger("click");
                } else if (e.keyCode == 39) { // right arrow
                    btnNextSoalModal.trigger("click");
                }

                // shift focus textarea komentar ketika tombol shift ditekan dan textarea komentar tidak memiliki fokus
                if (!$("#komentarDosen").is(":focus")) {
                    if (e.keyCode == 16) {
                        $("#komentarDosen").focus();
                    }
                }

                // click esc close modal
                if (e.keyCode == 27) {
                    $("#modalNilai").modal("hide");
                }
            });

            // ================= Section Function ================= //

            function fetchSoal(page = currentPageSoalModal) {
                $.ajax({
                    type: "GET",
                    url: thisURL,
                    data: {
                        ujianMhsId: ujianMhsId,
                        essayMhs: page
                    },
                    success: function (res) {
                        let mahasiswa = res.mahasiswa;
                        let data_soal = res.soal.data[0];
                        let soal = res.soal.data[0].soal_ujian_essay;
                        let soal_page = res.soal;
                        let data_ujian = res.ujian;
                        let ujian_mhs = res.ujianMhs;
                        let checked;

                        $("#containerSoalModal").html(`
                            <hr>

                            <h6 class="font-weight-bold text-uppercase position-relative text-muted title_form_nilai">
                                Jawaban Mahasiswa
                            </h6>
                            <div class="form-group">
                                <div id="jawabanModal">
                                </div>
                            </div>

                            <hr>

                            <h6 class="font-weight-bold text-uppercase position-relative text-muted title_form_nilai">
                                Form Penilaian
                            </h6>

                            <div class="form-group">
                                <input type="number" name="skor" min="0" max="20" class="form-control"
                                    placeholder="Masukkan skor">
                            </div>

                            <div class="form-group m-0 p-0">
                                <textarea id="komentarDosen" class="form-control" name="komenter"
                                    placeholder="Silahkan masukkan komentar jika anda mau berkomentar" rows="5"></textarea>
                            </div>
                        `);
                        $("#judulModalNilai").html("Form - Penilaian Ujian Essay");
                        $("#infoMhsModal").html(mahasiswa.nama + " (" + mahasiswa.nim + ")");
                        $("#judulSoalModal").html("<b>Soal No. " + soal_page.current_page +
                            "</b> <span class='text-muted'>dari " + soal_page.last_page + " Soal</span>");
                        $("#pertanyaanSoalModal").html(soal.pertanyaan);
                        $("#ujianHasilIdModal").val(data_soal.id);

                        (data_soal.jawaban == null) ? // jika jawaban kosong
                            data_soal.jawaban = "<span class='badge badge-secondary'>Jawaban Kosong</span>" :
                            data_soal.jawaban = data_soal.jawaban;

                        $("#jawabanModal").html(data_soal.jawaban); // tampilkan jawaban

                        if (data_soal.komentar_dosen) { // jika komentar dosen ada
                            $("#komentarDosen").val(data_soal.komentar_dosen);
                        }

                        $("input[name='skor']").val(data_soal.skor).trigger("change");

                        btnPrevSoalModal.removeAttr("disabled"); // Enable button
                        btnPrevSoalModal.attr("id", soal_page.current_page - 1); // Set data page
                        if (soal_page.prev_page_url == null) { // Jika tidak ada halaman sebelumnya
                            btnPrevSoalModal.attr("disabled", true); // Disable button
                        }

                        btnNextSoalModal.attr("id", soal_page.current_page + 1); // Set data page
                        btnNextSoalModal.removeAttr("disabled"); // Enable button
                        if (soal_page.next_page_url == null) { // Jika tidak ada halaman selanjutnya
                            btnNextSoalModal.attr("disabled", true); // Disable button
                        }
                    } // End success
                }); // End ajax
            } // End function fetchSoal

            function fetchDaftarSoal(page = currentPageDaftarSoal) { // Fetch daftar soal
                $.ajax({
                    type: "GET",
                    url: thisURL,
                    data: {
                        ujianMhsId: ujianMhsId,
                    },
                    success: function (res) {
                        let output = "";
                        let currentScroll = localStorage.getItem(itemLsDsScroll);
                        let daftarSoal = res.daftarSoal;

                        output += `<div class="" id="ModalModal">`;
                        daftarSoal.forEach((data, i) => {

                            (data.status != 2) ?
                                bgColor = "bg-success" :
                                bgColor = "bg-secondary";

                            output += `
                                <button type="button" class="btn btn-secondary ${bgColor} pilihanSoalModal"
                                    id="${i + 1}">
                                    ${i + 1}
                                </button>
                            `;
                        });
                        output += `</div>`;

                        $("#daftarSoalModal").html(output);

                        if (currentScroll) {
                            $("#ModalModal").scrollTop(currentScroll);
                        }
                    }
                });
            }

            function handlerSoalPageModal() { // Handler button prev dan next
                const page = $(this).attr("id");
                fetchSoal(page);
                localStorage.setItem(itemLsSoal, page);
            }

            function simpanNilai() // Simpan nilai essay
            {
                const $this = $(this);

                // jika skor kurang 0 maka 0 jika lebih 20 maka 20
                $this.val() < 0 ? $this.val(0) : $this.val();
                $this.val() > 20 ? $this.val(20) : $this.val();

                const skor = $this.val();
                const ujianHasilIdModal = $("#ujianHasilIdModal").val();

                $.ajax({
                    type: "PUT",
                    url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.simpanNilaiEssayMahasiswa') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        skor: skor,
                        ujianHasilId: ujianHasilIdModal,
                        ujianMhsId: ujianMhsId,
                        updateStatus: skor.length > 1 ? 1 : 2
                    },
                    dataType: "JSON",
                    success: function(res) {
                        fetchDaftarSoal();
                        localStorage.setItem(itemLsDsScroll, $("#listSoalModal").scrollTop());
                    },
                });
            }

            function simpanKomentar()
            { // Simpan komentar essay
                const komentar = $(this).val();
                const ujianHasilIdModal = $("#ujianHasilIdModal").val();

                $.ajax({
                    type: "PUT",
                    url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.simpanNilaiEssayMahasiswa') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        komentar: komentar,
                        ujianHasilId: ujianHasilIdModal,
                        ujianMhsId: ujianMhsId
                    },
                    dataType: "JSON",
                    success: function(res) {
                        fetchDaftarSoal();
                        localStorage.setItem(itemLsDsScroll, $("#listSoalModal").scrollTop());
                    },
                });
            } // End function simpanKomentar

            $("#modalNilai").on("hidden.bs.modal", function () { // Ketika modal di close
                btnPrevSoalModal.off("click", handlerSoalPageModal);
                btnNextSoalModal.off("click", handlerSoalPageModal);
                $(document).off("click", ".pilihanSoalModal", handlerSoalPageModal);
                $(document).off("change", "input[name='skor']", simpanNilai);
                $(document).off("change", "#komentarDosen", simpanKomentar);
                localStorage.removeItem(itemLsSoal);
                localStorage.removeItem(itemLsDaftarSoal);
                localStorage.removeItem(itemLsDsScroll);
            });

        }); // End click btnNilaiHasil

    </script>
@endpush
