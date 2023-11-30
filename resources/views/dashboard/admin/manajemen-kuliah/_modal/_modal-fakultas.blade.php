{{-- Modal create --}}
<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="replayTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-centered">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2" id="replayTitle">Form - Buat Fakultas</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formFakultasAdd" action="{{ route('manajemen.kuliah.fakultas.store') }}" autocomplete="off"
                method="POST">
                @csrf
                @method('POST')

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nama">Nama Fakultas</label>
                        <input type="text" class="form-control" id="add_nama" name="nama"
                            placeholder="Masukkan nama fakultas!" autofocus>
                        <span class="invalid-feedback d-block error-text nama_error"></span>
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
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-centered">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2" id="modalEdit">Form - Fakultas Edit</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formFakultasEdit" action="#" autocomplete="off" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id">

                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nama">Nama Fakultas</label>
                        <input type="text" class="form-control" id="edit_nama" name="nama"
                            placeholder="Masukkan nama fakultas!" autofocus>
                        <span class="invalid-feedback d-block error-text edit_nama_error"></span>
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
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="deleteTitle" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">

            <form action="" method="DELETE" id="formFakultasDelete">
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
