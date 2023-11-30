{{-- Modal create --}}
<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-centered">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Buat Jadwal Kuliah</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formAddJadwal" action="{{ route('manajemen.kuliah.jadwal.admin.kuliah.store') }}"
                autocomplete="off" method="POST">
                @csrf
                @method('POST')

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="dosen">Dosen</label>
                                <select name="dosen" id="add_dosen" class="form-control">
                                    <option value="">Pilih Dosen Mengajar</option>
                                    @foreach ($data_dosen as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text dosen_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="kelas">Kelas</label>
                                <select name="kelas" id="add_kelas" class="form-control" disabled>
                                </select>
                                <span class="invalid-feedback d-block error-text kelas_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="matkul">Matakuliah</label>
                                <select name="matkul" id="add_matkul" class="form-control" disabled>
                                </select>
                                <span class="invalid-feedback d-block error-text matkul_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="hari">Hari</label>
                                <select name="hari" id="add_hari" class="form-control">
                                    <option value="">Pilih Hari</option>
                                    @foreach ($data_hari as $key => $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text hari_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="started">Dimulai Jam</label>
                                <input type="time" name="started" id="add_started" class="form-control timepicker">
                                <span class="invalid-feedback d-block error-text started_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="ended">Berakhir Jam</label>
                                <input type="time" name="ended" id="add_ended" class="form-control timepicker">
                                <span class="invalid-feedback d-block error-text ended_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="submitAdd btn btn-success">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-centered">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Edit Jadwal Kuliah</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formEditJadwal" action="" autocomplete="off" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="dosen">Dosen</label>
                                <select name="dosen" id="edit_dosen" class="form-control">
                                    <option value="">Pilih Dosen Mengajar</option>
                                    @foreach ($data_dosen as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text edit_dosen_error"></span>
                            </div>
                            <div id="dropdownDinamis">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="hari">Hari</label>
                                <select name="hari" id="edit_hari" class="form-control">
                                    <option value="">Pilih Hari</option>
                                    @foreach ($data_hari as $key => $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text edit_hari_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="started">Dimulai Jam</label>
                                <input type="time" name="started" id="edit_started"
                                    class="form-control timepicker">
                                <span class="invalid-feedback d-block error-text edit_started_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="ended">Berakhir Jam</label>
                                <input type="time" name="ended" id="edit_ended"
                                    class="form-control timepicker">
                                <span class="invalid-feedback d-block error-text edit_ended_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" id="resetData" class="btn btn-danger" style="display: none;">
                        <i class="fas fa-sync-alt mr-1"></i> Reset
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">
                            Tutup
                        </button>
                        <button type="submit" class="submitEdit btn btn-warning">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal delete --}}
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="deleteTitle"
    aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">

            <form action="" method="DELETE" id="formHapusJadwal">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <input id="del_id" type="hidden" name="id">
                    <p id="text_del"></p>

                    <input type="hidden" id="kelas_id">
                    <input type="hidden" id="matkul_id">
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
