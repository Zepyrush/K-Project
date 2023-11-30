<div class="modal fade" id="modalImport" tabindex="-1" role="dialog"
        aria-labelledby="modalImport" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content modal-centered">
                <div class="modal-header p-2">
                    <h5 class="modal-title ml-2 font-weight-bold" id="modalImport">Form - Import Ujian {{ $jadwal->matkul->nama }} (PG)</h5>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="formImportUjian" action="{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.import') }}"
                    autocomplete="off" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <input type="hidden" name="jadwal_id" value="{{ encrypt($jadwal->id) }}">

                    <div class="modal-body">
                        <div class="row">
                            @include('dashboard.dosen.ujian.soal._sub._form-import-ujian')
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between p-2">
                        <button type="button" id="downloadTemplate" class="btn btn-primary" data-toggle="tooltip"
                            title="Download Template Excel">
                            <i class="fas fa-file-download mr-1"></i> Template
                        </button>
                        <div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="submitImport btn btn-success ml-1">
                                Import <i class="fas fa-file-import ml-1"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div> {{-- Modal Content --}}
        </div> {{-- Modal Dialog --}}
    </div> {{-- Modal --}}
