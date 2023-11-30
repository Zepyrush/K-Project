{{-- Modal Edit --}}
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-labelledby="modalEdit" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content modal-centered">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2"></h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formAbsenEdit" action="#" autocomplete="off" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id" name="absen">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="kelas">Kelas</label>
                                <input type="text" name="kelas" id="edit_kelas" class="form-control" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="matkul">Matakuliah</label>
                                <input type="text" name="matkul" id="edit_matkul" class="form-control" readonly>
                            </div>
                            <div class="form-group mb-3">
                                <label for="pertemuan">Pertemuan</label>
                                <input type="number" name="pertemuan" id="edit_pertemuan" class="form-control"
                                    placeholder="Pertemuan ke-" readonly>
                                <span class="invalid-feedback d-block error-text edit_pertemuan_error"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="berita_acara">Berita Acara</label>
                                <textarea name="berita_acara" id="edit_berita_acara" rows="3" class="form-control" placeholder="Boleh Kosong"></textarea>
                                <span class="invalid-feedback d-block error-text edit_berita_acara_error"></span>
                            </div>
                            <div class="form-group mb-3">
                                <label for="rangkuman">Rangkuman</label>
                                <textarea name="rangkuman" id="edit_rangkuman" class="form-control" rows="3" placeholder="Boleh Kosong"></textarea>
                                <span class="invalid-feedback d-block error-text edit_rangkuman_error"></span>
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
<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="deleteTitle" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">

            <form action="#" method="POST" id="formAbsenDelete">
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

@push('js')
    <script>
        $(document).ready(function() {
            // show modal edit
            $(document).on('click', '.edit_btn', function(e) {
                e.preventDefault();

                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.absen.dosen.edit', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $("#modalEdit").modal('show');

                        $("#modalEdit .modal-title").html(`Form Edit \t-\t Absensi Kelas ${res.absen_kelas}`);
                        $("#edit_id").val(id);
                        $("#edit_kelas").val(res.absen_kelas);
                        $("#edit_matkul").val(res.absen_matkul);
                        $("#edit_pertemuan").val(res.pertemuan);
                        $("#edit_rangkuman").val(res.rangkuman);
                        $("#edit_berita_acara").val(res.berita_acara);
                    }
                });
            });

            // show modal delete
            $(document).on('click', '.del_btn', function(e) {
                e.preventDefault();

                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.absen.dosen.edit', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $("#modalDelete").modal('show');

                        $("#del_id").val(id);
                        $("#text_del")
                            .html("Apakah anda yakin ingin menghapus absensi Kelas "+res.absen_kelas+" Pertemuan ke-"+res.pertemuan+" ?");
                    }
                });
            });
        });
    </script>
@endpush
