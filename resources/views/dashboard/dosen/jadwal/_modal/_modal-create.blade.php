{{-- Modal option --}}
<div class="modal fade" id="modalCreateUjian" tabindex="-1" role="dialog" aria-labelledby="modalCreateUjian"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header p-2">
                <h5 class="modal-title ml-2 font-weight-bold">Tipe Soal</h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="alert alert-danger font-weight-bold">
                    Mohon Segera Buat Soal Ujian Sebelum Jadwal Ujian Dimulai.
                </div>
                <p class="text-center mt-3">Silahkan Pilih Tipe Soal Ujian : </p>
                <div class="d-flex justify-content-center mt-3">

                    <a class="btn btn-primary mr-1 essay">
                        Essay
                    </a>
                    <a class="btn btn-primary pilgan">
                        Pilihan Ganda
                    </a>
                </div>
            </div>

            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            // Create Ujian
            $(document).on("click", '.btnBuatUjian', function(e) {
                e.preventDefault();

                var id = $(this).attr('id');
                $("#modalCreateUjian").modal('show');

                $(".pilgan").attr('href',"{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.create', ':id') }}"
                    .replace(':id', id));
                $(".essay").attr('href',"{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.essay.create', ':id') }}"
                    .replace(':id', id));
            });
        });
    </script>
@endpush
