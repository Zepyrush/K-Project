{{-- Modal Create --}}
<div class="modal fade" id="modalAbsensiCreate" tabindex="-1" role="dialog" aria-labelledby="modalAbsensiCreate"
aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-centered">
        <div class="modal-header p-2">
            <h5 class="modal-title font-weight-bold ml-2"></h5>
            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="formAbsenJadwal" action="{{ route('manajemen.kuliah.absen.dosen.store') }}" autocomplete="off"
            method="POST">
            @csrf

            <input type="hidden" id="add_jadwal" name="jadwal">
            <input type="hidden" id="add_kelas" name="kelas">

            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="matkul">Matakuliah</label>
                    <input type="text" name="matkul" id="add_matkul" class="form-control" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="pertemuan">Pertemuan
                        <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                            title="Pertemuan akan bertambah otomatis (dalam sehari). Tapi karena pertemuan dikuliah itu
                            seminggu sekali, maka dalam seminggu. Aslinya sehari kalo mau berubah pertemuannya.">
                        </i>
                    </label>
                    <input type="number" name="pertemuan" id="add_pertemuan" class="form-control"
                        placeholder="Pertemuan ke-" readonly>
                    <span class="invalid-feedback d-block error-text pertemuan_error"></span>
                </div>
                <div class="form-group mb-3">
                    <label for="rangkuman">Rangkuman</label>
                    <textarea name="rangkuman" id="add_rangkuman" class="form-control" rows="3" placeholder="Silahkan diisi jika anda mau mau diisi."></textarea>
                    <span class="invalid-feedback d-block error-text rangkuman_error"></span>
                </div>
                <div class="form-group mb-3">
                    <label for="berita_acara">Berita Acara</label>
                    <textarea name="berita_acara" id="add_berita_acara" rows="3" class="form-control" placeholder="Silahkan diisi jika anda mau mau diisi."></textarea>
                    <span class="invalid-feedback d-block error-text berita_acara_error"></span>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="submit" class="submitAdd btn btn-success">
                    Buat
                </button>
            </div>
        </form>
    </div>
</div>
</div>

{{-- Modal Create Tugas --}}
<div class="modal fade" id="modalCreateTugas" aria-hidden="true" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header p-2">
            <h5 class="modal-title font-weight-bold ml-2">Form - Buat Tugas</h5>
            <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="formAddTugas" action="{{ route('manajemen.kuliah.tugas.dosen.store') }}" method="POST"
            autocomplete="off" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <input type="hidden" name="jadwal" value="{{ encrypt($jadwal->id) }}">
            <input type="hidden" name="kelas_id" value="{{ encrypt($jadwal->kelas->id) }}">
            <input type="hidden" name="matkul_id" value="{{ encrypt($jadwal->matkul->id) }}">

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="pertemuan">Pertemuan</label>
                            <input type="number" class="form-control pertemuan" id="tugas_pertemuan"
                                name="pertemuan" placeholder="Masukkan pertemuan tugas disini" readonly>
                            <span class="invalid-feedback d-block error-text tugas_pertemuan_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="tipe">Tipe Tugas</label>
                            <select id="tugas_tipe" name="tipe" class="form-control">
                                <option value=""></option>
                                <option value="file">File</option>
                                <option value="link">Link</option>
                            </select>
                            <span class="invalid-feedback d-block error-text tugas_tipe_error"></span>
                        </div>

                        <div class="form-group" id="tugas_link" style="display: none;">
                            <label for="file_or_link">Link Tugas</label>
                            <input type="text" class="form-control" id="linkTugas" name="file_or_link"
                                placeholder="Masukkan link untuk soal tugas">
                            <span class="invalid-feedback d-block error-text tugas_file_or_link_error"></span>
                        </div>

                        <div class="form-group" id="tugas_file" style="display: none;">
                            <label for="file_or_link">File Tugas</label>
                            <div class="custom-file">
                                <input id="fileTugas" type="file" name="file_or_link"
                                    class="custom-file-input">
                                <label class="custom-file-label" for="file_or_link">
                                    Cari file soal tugas..
                                </label>
                            </div>
                            <span class="invalid-feedback d-block error-text tugas_file_or_link_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="pengumpulan">Dealine Tugas</label>
                            <input type="datetime-local" class="form-control dated" id="tugas_pengumpulan"
                                name="pengumpulan" placeholder="Masukkan deadline tugas">
                            <span class="invalid-feedback d-block error-text tugas_pengumpulan_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="judul">Judul Tugas</label>
                            <input type="text" class="form-control judul" id="tugas_judul" name="judul"
                                placeholder="Masukkan judul tugas disini">
                            <span class="invalid-feedback d-block error-text tugas_judul_error"></span>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi Tugas</label>
                            <textarea name="deskripsi" id="tugas_deskripsi" class="form-control" rows="5"
                                placeholder="Masukkan deskripsi tugas disini"></textarea>
                            <span class="invalid-feedback d-block error-text tugas_deskripsi_error"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success submitTugas" disabled>Buat Tugas</button>
            </div>
        </form>
    </div>
</div>
</div>
