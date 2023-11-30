{{-- Modal --}}
<div class="modal fade" id="modalLihatHasil">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">
                    Hasil Ujian Mahasiswa - {{ $ujian->judul }}
                </h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Nama</b>
                                            </div>
                                            <div id="namaMhs" class="col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>NIM</b>
                                            </div>
                                            <div id="nimMhs" class="col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Dimulai</b>
                                            </div>
                                            <div id="startedAtMhs" class="col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Selesai</b>
                                            </div>
                                            <div id="endedAtMhs" class="col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>Durasi</b>
                                                <i class="fas fa-info-circle ml-1 text-primary"
                                                    data-toggle="tooltip" title="*Durasi mahasiswa dalam mengerjakan soal. (Berapa lama mengerjakan ujian)">
                                                </i>
                                            </div>
                                            <div id="durationMhs" class="col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>IP Address</b>
                                            </div>
                                            <div id="ipAddressMhs" class="col-lg-8 col-8"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row mb-2">
                                            <div class="col-lg-4 col-4">
                                                <b>User Agent</b>
                                            </div>
                                            <div id="userAgentMhs" class="col-lg-8 col-8"></div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div style="overflow: auto;max-height: 320px;">
                            <table id="tableHasilMhs" class="table table-hover">
                                <thead style="position: sticky; top:0;  z-index: 1;">
                                    <tr style="background: #e1e1e1">
                                        <th class="text-center">#</th>
                                        <th style="width: 46%">Pertanyaan Soal</th>
                                        <th style="width: 20">Jawaban Mahasiswa</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Ragu</th>
                                        <th style="width: 15%;">Komentar Dosen</th>
                                    </tr>
                                </thead>
                                <tbody id="detailNilai"></tbody>
                            </table>
                        </div>
                    </div> {{-- col-lg-12 --}}
                </div> {{-- row --}}
            </div> {{-- modal-body --}}

            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>

        </div> {{-- modal-content --}}
    </div> {{-- modal-content --}}
</div> {{-- modalLihatHasil --}}

@push('js')
    <script>
        $(document).on("click", ".btnLihatHasil", function(e) {
            e.preventDefault();

            const ujianMhsId = $(this).attr("id");
            $("#modalLihatHasil").modal("show");

            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.essay.lihatHasilMahasiswa', encrypt($jadwal->id)) }}",
                data: {
                    ujianMhsId: ujianMhsId
                },
                dataType: "json",
                success: function(res) {
                    let data = res.data;
                    let ujianMhs = res.ujianMhs;
                    let ujianHasil = ujianMhs.ujian_hasil;
                    let html;

                    for (let key in data) {
                        $("#" + key).html(data[key]);
                    }

                    ujianHasil.forEach(function(item, index) {
                        statusUjian(item);

                        html += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${item.soal_ujian_essay.pertanyaan}</td>
                                <td>${item.jawaban}</td>
                                <td class="text-center">${status}</td>
                                <td class="text-center">${ragu}</td>
                                <td>${item.komentar_dosen}</td>
                            </tr>
                        `;
                    });

                    $("#detailNilai").html(html);

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

                        (item.ragu == 1) ? ragu = "<span class='badge badge-warning'>Ya</span>":
                            ragu = "Tidak";

                        (item.jawaban == null) ? item.jawaban = "-":
                            item.jawaban = item.jawaban.toUpperCase();

                        (item.komentar_dosen == null) ? item.komentar_dosen = "-":
                            item.komentar_dosen = item.komentar_dosen;
                    } // end statusUjian
                }
            });
        });

        $("#modalLihatHasil").on("hidden.bs.modal", function() { // reset modal
            $("#audio").trigger("pause");
        });
    </script>
@endpush
