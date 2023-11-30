<div class="modal fade" id="modalInfo" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title ml-2 font-weight-bold" id="judul_ujian"></h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="#" id="formMulaiUjian" method="POST">
                @csrf

                <input type="hidden" name="jadwal_id" id="jadwal_id">

                <div class="modal-body">
                    <div class="alert alert-danger">
                        <div>
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong style="font-size: 19px">Perhatian!</strong>
                            <p class="m-0 p-0 mt-2">
                                Sebelum memulai ujian, pastikan internet anda stabil dan tidak terputus.<br>
                                Dan mengikuti peraturan ujian yang berlaku. <b>Semoga Berhasil !</b>
                            </p>
                        </div>
                    </div>

                    <div class="divider" style="margin-top: 22px;"></div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-4 col-4">
                                            <b>Matakuliah</b>
                                        </div>
                                        <div id="matkul" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-4 col-4">
                                            <b>Kelas</b>
                                        </div>
                                        <div id="kelas" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-4 col-4">
                                            <b>Waktu Mulai</b>
                                        </div>
                                        <div id="waktu_mulai" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-4 col-4">
                                            <b>Waktu Selesai</b>
                                        </div>
                                        <div id="waktu_selesai" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-4 col-4">
                                            <b>Tipe Soal</b>
                                        </div>
                                        <div id="tipe_soal" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-4 col-4">
                                            <b>Jumlah Soal</b>
                                        </div>
                                        <div id="jumlah_soal" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-4 col-4">
                                            <b>Durasi Ujian</b>
                                        </div>
                                        <div id="durasi_ujian" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-4 col-4">
                                            <b>Tanggal Ujian</b>
                                        </div>
                                        <div id="tanggal_ujian" class="col-lg-8 col-8"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row mb-2">
                                        <div class="col-lg-4 col-4">
                                            <b>Semester</b>
                                        </div>
                                        <div id="semester" class="col-lg-8 col-8"></div>
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

                            <div class="divider" style="margin-top: 20px;"></div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-2 col-12">
                                            <b>Deskripsi Ujian</b>
                                        </div>
                                        <div id="deskripsi" class="col-lg-10 col-12"></div>
                                    </div>
                                </div>
                            </div>
                        </div> {{-- End Col --}}
                    </div> {{-- End Row --}}
                </div> {{-- End Modal Body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" id="mulaiUjianBtn" class="btn btn-primary"><i
                            class="fas fa-pen mr-1"></i>
                        Mulai Kerjakan</button>
                </div>
            </form>

        </div> {{-- End Modal Content --}}
    </div> {{-- End Modal Dialog --}}
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on("click", ".btnMulai", function(e) {
                e.preventDefault();

                var id = $(this).attr('id');

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.ujian.mahasiswa.informasi', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $("#modalInfo").modal("show");

                        for (let key in res) {

                            (res.judul_ujian) ? $("#judul_ujian").text("Informasi Ujian - " +  res.judul_ujian)
                                : $("#judul_ujian").text("");
                            (res.deskripsi) ? $("#deskripsi").html(res.deskripsi) : $("#deskripsi").html("");
                            (res.jadwal_id) ? $("#jadwal_id").val(res.jadwal_id) : $("#jadwal_id").val("");

                            $("#" + key).html(": " + res[key]);
                        }

                        if (res.ujianDimulai != null) {
                            $("#mulaiUjianBtn").removeClass("btn-primary").addClass("btn-warning")
                                .html("<i class='fas fa-sign-in-alt mr-1'></i> Lanjutkan Ujian");
                        }

                        if (res.tipe_soal == 'Pilihan Ganda') {
                            $("#formMulaiUjian").attr('action',
                                "{{ route('manajemen.kuliah.ujian.mahasiswa.pg.mulaiUjian') }}");
                        } else {
                            $("#formMulaiUjian").attr('action',
                                "{{ route('manajemen.kuliah.ujian.mahasiswa.essay.mulaiUjian') }}");
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            });
        });
    </script>
@endpush
