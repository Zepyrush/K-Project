<div class="modal fade" id="modalListMahasiswa">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2">

                </h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body table-responsive p-2">
                <div class="row">
                    <div class="col-lg-12">
                        <div style="overflow: auto;max-height: 350px;">
                            <table id="tableMhsShow" class="table table-hover">
                                <thead style="position: sticky; top:0;">
                                    <tr style="background: #e1e1e1">
                                        <th>#</th>
                                        <th>Daftar Mahasiswa</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div> {{-- col-lg-12 --}}
                </div> {{-- row --}}
            </div> {{-- modal-body --}}

            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>

        </div> {{-- modal-content --}}
    </div> {{-- modal-content --}}
</div>

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#mhsTotal").click(function(e) {
                e.preventDefault();
                showModalWithFilter(1);
            });

            $("#mhsTidak").click(function(e) {
                e.preventDefault();
                showModalWithFilter(0);
            });

            function showModalWithFilter(filter) {
                showTableInModal(filter);
            }

            // Show table in modal
            function showTableInModal(filter) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.tugas.dosen.table.mhs.show', encrypt($tugas->id)) }}",
                    data: {
                        filter: filter,
                        tugas_id: "{{ $tugas->id }}"
                    },
                    dataType: "json",
                    beforeSend: function() {
                        $("#tableMhsShow tbody").html("");
                    },
                    success: function(res) {
                        $("#modalListMahasiswa").modal("show");

                        let mahasiswa = res.mahasiswa;
                        let title = filter == 0 ?
                            "<i class='fas fa-user-times text-danger mr-1'></i> Tidak Mengumpulkan Tugas" :
                            "<i class='fas fa-users text-primary mr-1'></i> Total Mahasiswa";

                        $("#modalListMahasiswa .modal-title").html(title);

                        if (mahasiswa == "") {
                            $("#tableMhsShow tbody").append(`
                                <tr>
                                    <td colspan="2" class="text-center">Tidak ada mahasiswa</td>
                                </tr>
                            `);

                            return
                        }

                        mahasiswa.forEach((mhs, index) => {
                            $("#tableMhsShow tbody").append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <a href="javascript:void(0)" class="d-flex align-items-center"
                                            style="cursor: default">
                                            <img src="${mhs.foto}" width="40" class="avatar rounded-circle me-3">
                                            <div class="d-block ml-3">
                                                <span class="fw-bold name-user">${mhs.nama}</span>
                                                <div class="small text-secondary">${mhs.nim}</div>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                });
            }
        });
    </script>
@endpush
