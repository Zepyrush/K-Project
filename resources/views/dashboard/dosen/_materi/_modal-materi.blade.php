{{-- Modal Create --}}
<div class="modal fade" id="modalCreate" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Buat Materi</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formAddMateri" action="{{ route('manajemen.kuliah.materi.dosen.store') }}" method="POST"
                autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <input type="hidden" name="jadwal" value="{{ encrypt($jadwal->id) }}">
                <input type="hidden" name="kelas_id" value="{{ encrypt($jadwal->kelas->id) }}">
                <input type="hidden" name="matkul_id" value="{{ encrypt($jadwal->matkul->id) }}">
                <input type="hidden" name="matkul_kode" value="{{ $jadwal->matkul->kode }}">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <input type="text" class="form-control" id="add_kelas" name="kelas"
                                    placeholder="kelas" value="{{ $jadwal->kelas->kode }}" readonly>
                                <span class="invalid-feedback d-block error-text kelas_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="matkul">Matakuliah</label>
                                <input type="text" class="form-control matkul" id="add_matkul" name="matkul"
                                    placeholder="matkul Dosen" value="{{ $jadwal->matkul->nama }}" readonly>
                                <span class="invalid-feedback d-block error-text matkul_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="tipe">Tipe Materi</label>
                                <select id="add_tipe" name="tipe" class="form-control">
                                    <option value=""></option>
                                    <option value="pdf">PDF</option>
                                    <option value="youtube">Youtube</option>
                                </select>
                                <span class="invalid-feedback d-block error-text tipe_error"></span>
                            </div>
                            <div class="form-group" id="typeLink" style="display: none;">
                                <label for="file_or_link">Link Materi</label>
                                <div class="row align-items-center">
                                    <div class="col-lg-7">
                                        <span class="text-muted" style="font-size: 15.5px;">https://www.youtube.com/watch?v=</span>
                                    </div>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="add_file_or_link"
                                            name="file_or_link" placeholder="Kode youtubenya" style="width: 190px">
                                        <span class="invalid-feedback d-block error-text file_or_link_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="typeFile" style="display: none;">
                                <label for="file_or_link">File Materi</label>
                                <div class="custom-file">
                                    <input id="pdfMateri" type="file" name="file_or_link"
                                        class="custom-file-input">
                                    <label class="custom-file-label" id="add_file_or_link" for="file_or_link">
                                        Cari file materinya..
                                    </label>
                                </div>
                                <span class="invalid-feedback d-block error-text file_or_link_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="judul">Judul Materi</label>
                                <input type="text" class="form-control judul" id="add_judul" name="judul"
                                    placeholder="Masukkan judul materi disini">
                                <span class="invalid-feedback d-block error-text judul_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="pertemuan">Pertemuan</label>
                                <input type="number" class="form-control pertemuan" id="add_pertemuan"
                                    name="pertemuan" placeholder="Masukkan pertemuan materi disini" readonly>
                                <span class="invalid-feedback d-block error-text pertemuan_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi Materi</label>
                                <textarea name="deskripsi" id="add_deskripsi" class="form-control" rows="5"
                                    placeholder="Masukkan deskripsi materi disini"></textarea>
                                <span class="invalid-feedback d-block error-text deskripsi_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success submitAdd" disabled>Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Edit Materi</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formEditMateri" action="#" method="POST" autocomplete="off"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id">
                <input type="hidden" name="jadwal" value="{{ encrypt($jadwal->id) }}">
                <input type="hidden" name="kelas_id" value="{{ encrypt($jadwal->kelas->id) }}">
                <input type="hidden" name="matkul_id" value="{{ encrypt($jadwal->matkul->id) }}">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <input type="text" class="form-control" id="edit_kelas" name="kelas"
                                    placeholder="kelas" value="{{ $jadwal->kelas->kode }}" readonly>
                                <span class="invalid-feedback d-block error-text edit_kelas_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="matkul">Matakuliah</label>
                                <input type="text" class="form-control matkul" id="edit_matkul" name="matkul"
                                    placeholder="matkul Dosen" value="{{ $jadwal->matkul->nama }}" readonly>
                                <span class="invalid-feedback d-block error-text edit_matkul_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="tipe">Tipe Materi</label>
                                <select id="edit_tipe" name="tipe" class="form-control">
                                    <option value=""></option>
                                    <option value="pdf">PDF</option>
                                    <option value="youtube">Youtube</option>
                                </select>
                                <span class="invalid-feedback d-block error-text edit_tipe_error"></span>
                            </div>
                            <div class="form-group" id="edit_youtube" style="display: none">
                                <label for="file_or_link">Link Materi</label>
                                <div class="row align-items-center">
                                    <div class="col-lg-7">
                                        <span class="text-muted" style="font-size: 15.5px;">https://www.youtube.com/watch?v=</span>
                                    </div>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" id="edit_file_or_link"
                                            name="file_or_link" placeholder="Kode youtubenya" style="width: 190px">
                                        <span
                                            class="invalid-feedback d-block error-text edit_file_or_link_error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="edit_pdf" style="display: none">
                                <label for="file_or_link">File Materi</label>
                                <div class="custom-file">
                                    <input id="pdfMateri" type="file" name="file_or_link"
                                        class="custom-file-input">
                                    <label class="custom-file-label" id="edit_file_or_link" for="file_or_link">
                                        Cari file materinya..
                                    </label>
                                </div>
                                <span class="invalid-feedback d-block error-text edit_file_or_link_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="judul">Judul Materi</label>
                                <input type="text" class="form-control judul" id="edit_judul" name="judul"
                                    placeholder="Masukkan judul materi disini">
                                <span class="invalid-feedback d-block error-text edit_judul_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="pertemuan">Pertemuan</label>
                                <input type="number" class="form-control pertemuan" id="edit_pertemuan"
                                    name="pertemuan" placeholder="Masukkan pertemuan materi disini" readonly>
                                <span class="invalid-feedback d-block error-text edit_pertemuan_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="deskripsi">Deskripsi Materi</label>
                                <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3"
                                    placeholder="Masukkan deskripsi materi disini"></textarea>
                                <span class="invalid-feedback d-block error-text edit_deskripsi_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning submitEdit" disabled>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal delete --}}
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="deleteMateri"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="#" method="POST" id="formHapusMateri">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <input id="del_id" type="hidden" name="id">
                    <p id="text_del"></p>
                    <input type="hidden" id="tipe_del">
                </div>

                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger btnDelete">
                        Hapus
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
