<div id="modalImport" class="modal fade modal-import" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" enctype="multipart/form-data" method="POST"
            class="modal-content">
            @csrf
            @method('POST')

            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">Import Data Mahasiswa</h5>

                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Tutup">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                {{-- Alert Info --}}
                <div class="alert alert-default-warning" style="border-radius: 4px;">
                    <p class="mb-1 font-weight-bold">Perhatian!</p>
                    <ol class="mb-0 p-0 pl-3">
                        <li>
                            Untuk format file harus berupa <strong>Excel</strong> (.xls, .xlsx, .csv).
                        </li>
                        <li>
                            Ukuran file maksimal 5MB.
                        </li>
                        <li>
                            Silahkan download template excel yang telah disediakan.
                            <a download href="{{ asset('assets/file/template-mahasiswa.xlsx') }}"
                                class="text-primary">
                                <i class="fas fa-download"></i>
                                Download Template
                            </a>
                        </li>
                        <li>
                            Mohon untuk tidak mengubah baris pertama pada file excel.
                        </li>
                        <li>
                            Data NIM/No Induk harus unik (tidak boleh sama dengan data yang sudah ada)
                        </li>
                        <li>
                            Untuk memasukkan kelas dan fakultas, silahkan masukkan kode kelas dan kode fakultas yang sudah ada.
                        </li>
                    </ol>
                </div>

                <div class="form-group mt-3">
                    <label class="d-flex align-items-center" for="import-file_excel">
                        File Excel
                        <span class="text-danger ml-1">*</span>
                    </label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input change-file" id="import-file_excel" name="file_excel">
                        <label class="custom-file-label" for="import-file_excel"
                            data-text="Pilih File">
                            Pilih File
                        </label>
                        <span class="invalid-feedback d-block error-text" id="error-file_excel"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer p-1">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Import</button>
            </div>

        </form>
    </div>
</div>
