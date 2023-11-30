{{-- Modal create --}}
<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-centered">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Form - Buat Jadwal Ujian</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formAddJadwalUjian" action="{{ route('manajemen.kuliah.jadwal.admin.ujian.store') }}"
                autocomplete="off" method="POST">
                @csrf
                @method('POST')

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="dosen">Dosen</label>
                                <select name="dosen" id="add_dosen" class="form-control">
                                    <option value="">Pilih Dosen</option>
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
                            <div class="form-group mb-3">
                                <label for="dosen_can_manage">Dosen Bisa Kelola Jadwal
                                    <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                                        title="Maksudnya, si dosen bisa mengubah jadwal ujian ini, Tanpa harus lewat admin."></i>
                                </label>
                                <select name="dosen_can_manage" id="add_dosen_can_manage" class="form-control">
                                    <option value="">Pilih status ujian</option>
                                    <option value="1">Ya</option>
                                    <option value="0">Tidak</option>
                                </select>
                                <span class="invalid-feedback d-block error-text dosen_can_manage_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="tanggal_ujian">Tanggal Ujian</label>
                                <input type="date" name="tanggal_ujian" id="add_tanggal_ujian" class="form-control dated">
                                <span class="invalid-feedback d-block error-text tanggal_ujian_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="d-flex align-items-center" for="started">
                                    Dimulai Jam
                                </label>
                                <input type="time" name="started" id="add_started" class="form-control timepicker">
                                <span class="invalid-feedback d-block error-text started_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="d-flex align-items-center" for="ended">
                                    Selesai Jam
                                    <small class="text-primary font-weight-bold ml-1">(Boleh kosong)</small>
                                </label>
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

{{-- Modal edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-centered">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2" id="modalEdit">Form - Edit Jadwal Kuliah</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formEditJadwalUjian" action="#" autocomplete="off" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="dosen">Dosen</label>
                                <select name="dosen" id="edit_dosen" class="form-control">
                                    <option value="">Pilih Dosen</option>
                                    @foreach ($data_dosen as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback d-block error-text edit_dosen_error"></span>
                            </div>

                            <div id="dropdownDinamis"></div>

                            <div class="form-group mb-3">
                                <label for="dosen_can_manage">Dosen Bisa Kelola Jadwal
                                    <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                                        title="Maksudnya, si dosen bisa mengubah jadwal ujian ini, Tanpa harus lewat admin."></i>
                                </label>
                                <select name="dosen_can_manage" id="edit_dosen_can_manage" class="form-control">
                                    <option value=""></option>
                                    <option value="1">Ya</option>
                                    <option value="0">Tidak</option>
                                </select>
                                <span class="invalid-feedback d-block error-text edit_dosen_can_manage_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="tanggal_ujian">Tanggal Ujian</label>
                                <input type="date" name="tanggal_ujian" id="edit_tanggal_ujian"
                                    class="form-control dated">
                                <span class="invalid-feedback d-block error-text edit_tanggal_ujian_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="d-flex align-items-center" for="started">
                                    Dimulai Jam
                                </label>
                                <input type="time" name="started_at" id="edit_started"
                                    class="form-control timepicker">
                                <span class="invalid-feedback d-block error-text edit_started_at_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="d-flex align-items-center" for="ended">
                                    Selesai Jam
                                    <small class="text-primary font-weight-bold ml-1">(Boleh kosong)</small>
                                </label>
                                <input type="time" name="ended_at" id="edit_ended"
                                    class="form-control timepicker">
                                <span class="invalid-feedback d-block error-text edit_ended_at_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="submitEdit btn btn-warning">
                        Update
                    </button>
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

            <form action="" method="DELETE" id="formHapusJadwalUjian">
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
