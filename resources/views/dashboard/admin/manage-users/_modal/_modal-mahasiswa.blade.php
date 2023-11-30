{{-- Modal Create --}}
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Buat Data Mahasiswa</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formAddMahasiswa" action="{{ route('manage.users.mahasiswa.store') }}" method="POST"
                autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="nim">NIM</label>
                                <input type="number" class="form-control" id="add_nim" name="nim"
                                    placeholder="NIM Mahasiswa">
                                <span class="invalid-feedback d-block error-text nim_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control nama" id="add_nama" name="nama"
                                    placeholder="Nama mahasiswa">
                                <span class="invalid-feedback d-block error-text nama_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="add_email" name="email"
                                    placeholder="Email mahasiswa">
                                <span class="invalid-feedback d-block error-text email_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="foto">Foto</label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input">
                                    <label class="custom-file-label" id="add_foto" for="foto">
                                        Cari foto ...
                                    </label>
                                </div>
                                <span class="invalid-feedback d-block error-text foto_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="fakultas">Fakultas</label>
                                <select class="custom-select w-100" id="add_fakultas" name="fakultas">
                                    <option value="" selected disabled>Pilih Fakultas</option>
                                    @foreach ($fakultas as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text fakultas_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select class="form-control w-100" id="add_kelas" name="kelas">
                                    <option value="" selected disabled>Pilih Kelas</option>
                                    @foreach ($kelas as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text kelas_error"></span>
                            </div>
                            {{-- password --}}
                            <div class="form-group">
                                <label for="title">Password</label>
                                <input type="password" class="form-control" id="add_password" name="password"
                                    placeholder="Masukkan password">
                                <span class="invalid-feedback d-block error-text password_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="title">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="add_password_confirmation"
                                    name="password_confirmation" placeholder="Masukkan konfirmasi password">
                                <span class="invalid-feedback d-block error-text password_confirmation_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success submitAdd">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Edit Data Mahasiswa</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formEditMahasiswa" action="#" method="POST" autocomplete="off"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="nim">NIM</label>
                                <input type="number" class="form-control" id="edit_nim" name="nim"
                                    placeholder="NIM Mahasiswa">
                                <span class="invalid-feedback d-block error-text edit_nim_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input type="text" class="form-control nama" id="edit_nama" name="nama"
                                    placeholder="Nama mahasiswa">
                                <span class="invalid-feedback d-block error-text edit_nama_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="edit_email" name="email"
                                    placeholder="Email mahasiswa">
                                <span class="invalid-feedback d-block error-text edit_email_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="foto">Foto</label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input">
                                    <label class="custom-file-label" id="edit_foto" for="foto">
                                        Cari foto ...
                                    </label>
                                </div>
                                <span class="invalid-feedback d-block error-text edit_foto_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="fakultas">Fakultas</label>
                                <select class="custom-select w-100" id="edit_fakultas" name="fakultas">
                                    <option value="" selected disabled>Pilih Fakultas</option>
                                    @foreach ($fakultas as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text edit_fakultas_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="kelas">Kelas</label>
                                <select class="custom-select w-100" id="edit_kelas" name="kelas">
                                    <option value="" selected disabled>Pilih Kelas</option>
                                    @foreach ($kelas as $item)
                                        <option value="{{ $item->id }}">{{ $item->kode }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text edit_kelas_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning submitEdit">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal delete --}}
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="hapusMahasiswa"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="" method="DELETE" id="formHapusMahasiswa">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <input id="del_id" type="hidden" name="id">
                    <p id="text_del"></p>
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
