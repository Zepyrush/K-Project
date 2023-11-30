{{-- Modal Create --}}
<div class="modal fade" id="modalCreate" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Buat Data Pengguna</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formAddUser" action="{{ route('manage.users.user.store') }}" method="POST" autocomplete="off"
                enctype="multipart/form-data">
                @csrf
                @method('POST')

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="no_induk">Nomer induk</label>
                                <input type="number" class="form-control" id="add_no_induk" name="no_induk"
                                    placeholder="Nomer Induk User (00001111)">
                                <span class="invalid-feedback d-block error-text no_induk_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" class="form-control nama" id="add_name" name="name"
                                    placeholder="Nama User">
                                <span class="invalid-feedback d-block error-text name_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="add_email" name="email"
                                    placeholder="Email User">
                                <span class="invalid-feedback d-block error-text email_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="foto">Foto</label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input">
                                    <label class="custom-file-label" id="add_foto" for="foto">
                                        Cari foto user
                                    </label>
                                </div>
                                <span class="invalid-feedback d-block error-text foto_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="role">Role Pengguna</label>
                                <select name="role" id="add_role" class="form-control">
                                    <option value=""></option>
                                    @foreach ($roles as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text role_error"></span>
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
<div class="modal fade" id="modalEdit" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Edit Data Pengguna</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formEditUser" action="#" method="POST" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="no_induk">Nomer induk</label>
                                <input type="number" class="form-control" id="edit_no_induk" name="no_induk"
                                    placeholder="Nomer Induk User (00001111)">
                                <span class="invalid-feedback d-block error-text edit_no_induk_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" class="form-control nama" id="edit_name" name="name"
                                    placeholder="Nama User">
                                <span class="invalid-feedback d-block error-text edit_name_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="text" class="form-control" id="edit_email" name="email"
                                    placeholder="Email User">
                                <span class="invalid-feedback d-block error-text edit_email_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="foto">Foto</label>
                                <div class="custom-file">
                                    <input type="file" name="foto" class="custom-file-input">
                                    <label class="custom-file-label" id="edit_foto" for="foto">
                                        Cari foto user
                                    </label>
                                </div>
                                <span class="invalid-feedback d-block error-text edit_foto_error"></span>
                            </div>
                            <div class="form-group">
                                <label for="role">Role Pengguna</label>
                                <select name="role" id="edit_role" class="form-control">
                                    @foreach ($roles as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text edit_role_error"></span>
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
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="deleteUser"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <form action="" method="DELETE" id="formHapusUser">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <input id="del_id" type="hidden" name="id">
                    <p id="text_del">Apakah anda yakin akan menghapus pengguna dengan Nomer Induk </p>
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

{{-- Modal Pilihan --}}
<div class="modal fade" id="modalPilihan" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-sm ">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Silahkan Pilih</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex align-items-center justify-content-center">
                            <a class="btn btn-primary mr-1 dosenPage" href="javascript:void(0)">
                                Dosen
                            </a>
                            <a class="btn btn-primary mr-1 mahasiswaPage" href="javascript:void(0)">
                                Mahasiswa
                            </a>
                            <a class="btn btn-primary" href="javascript:void(0)"
                                data-toggle="modal" data-target="#modalCreate">
                                Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
