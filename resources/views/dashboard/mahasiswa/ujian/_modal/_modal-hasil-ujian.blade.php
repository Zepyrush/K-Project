<div class="modal fade" id="modalHasilUjian" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title ml-2 font-weight-bold judul_ujian" id="judul_ujian"></h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Matakuliah</b>
                                            </div>
                                            <div id="matkul" class="matkul col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Kelas</b>
                                            </div>
                                            <div id="kelas" class="kelas col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Waktu Mulai</b>
                                            </div>
                                            <div id="waktu_mulai" class="waktu_mulai col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Waktu Selesai</b>
                                            </div>
                                            <div id="waktu_selesai" class="waktu_selesai col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Tipe Soal</b>
                                            </div>
                                            <div id="tipe_soal" class="tipe_soal col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Jumlah Soal</b>
                                            </div>
                                            <div id="jumlah_soal" class="jumlah_soal col-lg-8 col-8"></div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Durasi Ujian</b>
                                            </div>
                                            <div id="durasi_ujian" class="durasi_ujian col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row">
                                            <div class="col-lg-4 col-4">
                                                <b>Tanggal Ujian</b>
                                            </div>
                                            <div id="tanggal_ujian" class="tanggal_ujian col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Semester</b>
                                            </div>
                                            <div id="semester" class="semester col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Tipe Ujian</b>
                                            </div>
                                            <div id="tipe_ujian" class="tipe_ujian col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="containerHasil">
                            <div class="alert alert-danger d-none">
                                <h5 class="font-weight-bold">Perhatian.</h5>
                                <p class="m-0 p-0">
                                    Halo {{ Auth::user()->name }}👋 Hasil ujian kamu sedang dalam proses oleh dosen
                                    <br />
                                    Silahkan cek kembali hasil ujian kamu setelah dosen selesai memprosesnya.
                                </p>
                            </div>

                            <div id="nilaiSection" class="row mb-3 text-center">
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-4 col-4">
                                            <b>Nilai </b>
                                        </div>
                                        <div id="hasil_nilai_ujian" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-4 col-4">
                                            <b class="text-success">Benar </b>
                                        </div>
                                        <div id="hasil_benar" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-4 col-4">
                                            <b class="text-danger">Salah </b>
                                        </div>
                                        <div id="hasil_salah" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="row">
                                        <div class="col-lg-4 col-4">
                                            <b class="text-secondary">Kosong </b>
                                        </div>
                                        <div id="hasil_kosong" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="containerTableHasil" style="overflow: auto;max-height: 300px;">
                                {{-- Ini table hasil untuk ujian pilihan ganda --}}
                                <table id="tableHasilPg" class="table table-hover d-none">
                                    <thead style="position: sticky; top: 0; z-index: 1;">
                                        <tr style="background: #e1e1e1;">
                                            <th>#</th>
                                            <th style="width: 46%">Pertanyaan</th>
                                            <th style="width: 20%">Kunci Jawaban</th>
                                            <th style="width: 20%">Jawaban Kamu</th>
                                            <th>Status</th>
                                            <th style="width: 10%;">Ragu Ragu</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                {{-- Ini table hasil untuk ujian essay --}}
                                <table id="tableHasilEssay" class="table table-hover d-none">
                                    <thead style="position: sticky; top:0; z-index: 1;">
                                        <tr style="background: #e1e1e1;">
                                            <th class="text-center">#</th>
                                            <th style="width: 46%">Pertanyaan</th>
                                            <th style="width: 20%">Jawaban Kamu</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Ragu</th>
                                            <th style="width: 15%">Komentar Dosen</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div> {{-- End Col --}}
                </div> {{-- End Row --}}
            </div> {{-- End Modal Body --}}

            <div class="modal-footer p-2 justify-content-between">
                <a class="btn" id="btnCetak" target="_blank">
                    <i class="fas fa-print mr-1"></i> Cetak Hasil
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>

        </div> {{-- End Modal Content --}}
    </div> {{-- End Modal Dialog --}}
</div>

@push('js')
    <script>
        $(document).ready(function() {

            const tableEs = $("#tableHasilEssay");
            const tablePg = $("#tableHasilPg");
            const contHasilAlrt = $("#containerHasil .alert");
            const nilSec = $("#nilaiSection");

            $("#modalHasilUjian").on("hidden.bs.modal", function () {
                $("audio").trigger("pause");
                $("#btnCetak").removeAttr("href style");
                (!tableEs.hasClass("d-none")) ? tableEs.addClass("d-none") : "";
                (!tablePg.hasClass("d-none")) ? tablePg.addClass("d-none") : "";
                (!contHasilAlrt.hasClass("d-none")) ? contHasilAlrt.addClass("d-none") : "";
                (!nilSec.hasClass("d-none")) ? nilSec.addClass("d-none") : "";
            });

            $(document).on("click", ".btn_lihat", function(e) {
                e.preventDefault();

                var id = $(this).attr("id");

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.ujian.mahasiswa.hasilUjian', ':id') }}"
                        .replace(':id', id),
                    dataType: "JSON",
                    success: function(res) {
                        $("#modalHasilUjian").modal("show");

                        let ujian = res.ujian;
                        let ujianHasil = res.ujian.ujian_hasil;
                        let mahasiswa = res.ujian.mahasiswa;
                        let data = res.data;
                        let html = "", status, ragu;

                        for (let key in data) { // set data ujian
                            (key == "judul_ujian") ?
                                $("." + key).html(`Hasil Ujian \t-\t ${data[key]}`) :
                                $("." + key).html(": " + data[key]);
                        }

                        // set data hasil ujian bertipe soal
                        (data.tipe_soal == 'Essay') ? essay() : pilihanGanda();

                        // function untuk menampilkan hasil ujian essay
                        function essay() {
                            if (data.belum_dinilai != 0) { // jika ada soal yang belum dinilai
                                nilSec.addClass("d-none"); // hilangkan nilai section
                                contHasilAlrt.removeClass("d-none"); // tampilkan alert

                                $("#btnCetak").css({
                                    "pointer-events": "none",
                                    "background-color": "#6c757d",
                                    "border-color": "#6c757d",
                                    "color": "#fff",
                                });
                            } else {
                                tableEs.removeClass("d-none"); // tampilkan table hasil
                                nilSec.removeClass("d-none"); // tampilkan nilai section

                                $("#btnCetak").css({ // add style
                                    "pointer-events": "auto",
                                    "background-color": "#1F7A8E",
                                    "color": "#fff",
                                });

                                // set link cetak
                                $("#btnCetak").attr("href",
                                    "{{ route('manajemen.kuliah.ujian.mahasiswa.cetakHasilUjian', ':id') }}"
                                    .replace(':id', data.jadwal_id));

                                for (let key in data) { // set nilai
                                    $("#hasil_" + key).html(": <b>" + data[key] + "</b>");
                                }

                                ujianHasil.forEach(function(item, index) { // loop data
                                    statusUjian(item);

                                    html += `
                                        <tr>
                                            <td class="text-center">${index + 1}</td>
                                            <td>${item.soal_ujian_essay.pertanyaan}</td>
                                            <td>${jawaban_mahasiswa}</td>
                                            <td class="text-center">${status}</td>
                                            <td class="text-center">${ragu}</td>
                                            <td>${item.komentar_dosen}</td>
                                        </tr>
                                    `;
                                });

                                tableEs.find("tbody").html(html); // set data
                            }
                        } // end function essay

                        // function untuk menampilkan hasil ujian pilihan ganda
                        function pilihanGanda() {
                            tablePg.removeClass("d-none"); // tampilkan table hasil
                            nilSec.removeClass("d-none"); // tampilkan nilai section
                            contHasilAlrt.addClass("d-none"); // hilangkan alert

                            $("#btnCetak").css({ // add style
                                "pointer-events": "auto",
                                "background-color": "#1F7A8E",
                                "color": "#fff",
                            });

                            $("#btnCetak").attr("href", // set link cetak
                                "{{ route('manajemen.kuliah.ujian.mahasiswa.cetakHasilUjian', ':id') }}"
                                .replace(':id', data.jadwal_id));

                            for (let key in data) { // set data ujian
                                $("#hasil_" + key).html(": <b>" + data[key] + "</b>");
                            }

                            ujianHasil.forEach((item, index) => { // set data hasil ujian
                                statusUjian(item);

                                let jawabanMhs;
                                let dataSoal = item.soal_ujian_pg;
                                let kunciJawaban = dataSoal.jawaban_benar + ". " +
                                    dataSoal['pilihan_' + dataSoal.jawaban_benar];

                                (dataSoal['pilihan_' + item.jawaban] === undefined) ? // jika jawaban kosong
                                    jawabanMhs = "-" :
                                    jawabanMhs = item.jawaban + ". " + dataSoal['pilihan_' + item.jawaban];

                                html += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${dataSoal.pertanyaan}</td>
                                        <td>${kunciJawaban}</td>
                                        <td>${jawabanMhs}</td>
                                        <td class="text-center">${status}</td>
                                        <td class="text-center">${ragu}</td>
                                    </tr>
                                `;
                            });

                            tablePg.find("tbody").html(html); // set data ke table
                        }

                        // function untuk menentukan status ujian
                        function statusUjian(item) {
                            if (item.status == 0) {
                                if (item.jawaban == null) {
                                    status =
                                        "<span class='badge badge-secondary'>Tidak Jawab</span>";
                                } else {
                                    status =
                                        "<span class='badge badge-danger'>Salah</span>";
                                }
                            } else {
                                status =
                                    "<span class='badge badge-success'>Benar</span>";
                            }

                            (item.ragu == 1) ? ragu =
                                "<span class='badge badge-warning'>Ya</span>":
                                ragu = "Tidak";

                            (item.jawaban == null) ? jawaban_mahasiswa = "-":
                                jawaban_mahasiswa = item.jawaban;

                            (item.komentar_dosen == null) ? item.komentar_dosen = "-":
                                item.komentar_dosen = item.komentar_dosen;
                        } // end statusUjian

                    } // end success
                }); // end ajax
            }); // end btnLihatHasil

            $(window).bind('resize', function() {
                var sizeWindow = $(window).width();
                if (sizeWindow < 991) {
                    $("#nilaiSection").removeClass("text-center");
                    $("#nilaiSection .row").addClass("mb-2");
                } else {
                    $("#nilaiSection").addClass("text-center");
                    $("#nilaiSection .row").removeClass("mb-2");
                }
            }).trigger('resize');

        }); // end document ready
    </script>
@endpush
