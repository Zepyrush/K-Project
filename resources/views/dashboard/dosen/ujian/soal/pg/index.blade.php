<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0 p-0 font-weight-bold">
                    Informasi Ujian
                </h5>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">

                        @include('dashboard.dosen.ujian.soal._sub._information-index')

                        <div class="row">
                            <div class="col-lg-6 mb-2">
                                <div class="row">
                                    <div class="col-lg-4 col-4">
                                        <b>Jumlah Soal</b>
                                    </div>
                                    <div class="col-lg-8 col-8">
                                        : {{ $ujian->soalUjianPg->count() }} Soal
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row ">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-2 col-12 mb-1">
                                        <b>Deskripsi Ujian</b>
                                    </div>
                                    <div class="col-lg-10 col-12">
                                        {{ $ujian->deskripsi }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> {{-- End col --}}
                </div> {{-- End row --}}

            </div> {{-- End card-body --}}
        </div> {{-- End card --}}
    </div> {{-- End col --}}
</div> {{-- End row --}}

{{-- Informasi Soal dan jawabanya --}}
<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0 p-0 font-weight-bold">
                    Informasi Soal Ujian & Jawaban
                </h5>
            </div>
        </div>
    </div>

    @if ($ujian->soalUjianPg->isNotEmpty())
        <div class="col-lg-9" id="soal">
            <div class="card">
                <div class="card-header p-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="m-0 p-0 ml-2 p-2" id="judulSoal"></h6>
                        <div>
                            <button disabled id="prevSoal" class="btn btn-sm btn-primary mr-1">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <button disabled id="nextSoal" class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-text p-5" id="pertanyaanSoal"></div>

                    <hr>

                    <div id="pilihanSoal" class="p-5">
                        <p id="pilihan_a" class="pilihan_soal mb-1 p-0"></p>
                        <p id="pilihan_b" class="pilihan_soal mb-1 p-0"></p>
                        <p id="pilihan_c" class="pilihan_soal mb-1 p-0"></p>
                        <p id="pilihan_d" class="pilihan_soal mb-1 p-0"></p>
                        <p id="pilihan_e" class="pilihan_soal m-0 p-0 "></p>
                    </div>
                </div>

            </div>
        </div>

        {{-- Soal Jawaban --}}
        <div class="col-lg-3" id="soalJawaban">
            <div class="card sticky sticky_sub">
                <div class="card-header p-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="m-0 p-0 font-weight-bold p-2">
                            Daftar Soal
                        </h6>

                        <div class="d-flex align-items-center justify-content-between">
                            <button disabled id="prevDaftarSoal" class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-left"></i>
                            </button>

                            <span id="infoDaftarSoal" class="float-left font-weight-bold ml-2 mr-2"></span>

                            <button disabled id="nextDaftarSoal" class="btn btn-sm btn-primary">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-2">
                    <table id="tableDaftarSoal" class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>No. Soal</th>
                                <th>Jawaban</th>
                            </tr>
                        </thead>
                        <tbody style="cursor: pointer;" id="listJawaban">

                        </tbody>
                    </table>
                </div>

            </div> {{-- End card --}}
        </div> {{-- End col --}}
    @else
        <div class="col-lg-12 mb-3">
            <div class="alert alert-danger">
                <h5>Alert!</h5>
                Soal Ujian & Jawaban Tidak ada.
            </div>
        </div>
    @endif
</div> {{-- End row --}}

<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary card-outline ">
            <div class="card-header">
                <h5 class="m-0 p-0 font-weight-bold">
                    Informasi & Data Mahasiswa
                </h5>
            </div>
        </div>
    </div>

    <div class="col-lg-3 mb-3">
        <div class="list-group sticky sticky_sub">
            <button type="button"
                class="list-group-item list-group-item-action active cursor_default">
                <h6 class="font-weight-bold m-0 p-0">Informasi Mahasiswa</h6>
            </button>

            <a href="javascript:void(0)" class="list-group-item list-group-item-action cursor_default">
                <i class="fas fa-users text-primary mr-1"></i>
                Total Mahasiswa

                <span class="badge badge-primary badge-pill float-right position-relative" style="top: 2px;">
                    {{ $mahasiswa->count() }}
                </span>
            </a>
            <a href="javascript:void(0)" class="list-group-item list-group-item-action cursor_default">
                <i class="fas fa-user-check text-success mr-1"></i>
                Mengerjakan Ujian

                <span class="badge badge-success badge-pill float-right position-relative" style="top: 2px;">
                    {{ $mahasiswa->where('status', 1)->count() }}
                </span>
            </a>
            <a href="javascript:void(0)" class="list-group-item list-group-item-action cursor_default">
                <i class="fas fa-user-times text-danger mr-1"></i>
                Tidak Mengerjakan

                <span class="badge badge-danger badge-pill float-right position-relative" style="top: 2px;">
                    {{ $mahasiswa->where('status', 0)->count() }}
                </span>
            </a>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="card">
            <div class="card-header p-2">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="ml-2 m-0 p-0 font-weight-bold">
                        Data Mahasiswa
                    </h5>
                    <div>
                        <button id="cetakTable" class="btn btn-primary btn-sm">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                        <button id="refreshTable" class="btn btn-warning btn-sm ml-1"
                            data-toggle="tooltip" title="Refresh Table">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive">
                <table class="table mahasiswa table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mahasiswa</th>
                            <th>Benar</th>
                            <th>Salah</th>
                            <th>Kosong</th>
                            <th>Nilai</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('dashboard.dosen.ujian.soal.pg._modal-hasil')

@push('js')
    <script>
        // =============== Global Variable =============== //

        const jadwalIdHash = "{{ encrypt($jadwal->id) }}";
        const jadwalId = "{{ $jadwal->id }}";
        const ujianId = "{{ $ujian->id }}";
        const btnPrevSoal = $("#prevSoal");
        const btnNextSoal = $("#nextSoal");
        const btnPrevDaftarSoal = $("#prevDaftarSoal");
        const btnNextDaftarSoal = $("#nextDaftarSoal");
        const itemLsSoal = `currPageSoal_${ujianId}_${jadwalId}`;
        const itemLsDaftarSoal = `currdaftarSoal_${ujianId}_${jadwalId}`;
        const itemLsDsOffSet = `daftarSoalOffSet_${ujianId}_${jadwalId}`;

        // =============== Function Section =============== //

        let currentPageSoal = localStorage.getItem(itemLsSoal) || 1;

        loadSoal();
        function loadSoal(page = currentPageSoal) {
            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.list', encrypt($jadwal->id)) }}",
                data: {
                    soal: page
                },
                dataType: "JSON",
                beforeSend: function() {
                    $("#soal").addClass("loading-skeleton");
                    $(".pilihan_soal").removeClass("text-success");
                },
                complete: function() {
                    $("#soal").removeClass("loading-skeleton");
                    $("#pertanyaanSoal").removeClass("p-5");
                    $("#pilihanSoal").removeClass("p-5");
                    $("#judulSoal").removeClass("p-2");
                },
                success: function (res) {
                    let dataSoal = res.soal.data[0];
                    let soal = res.soal;
                    let options = ['a', 'b', 'c', 'd', 'e'];

                    if (dataSoal == undefined) {
                        loadSoal(soal.last_page);
                        localStorage.setItem(itemLsSoal, soal.last_page);
                    }

                    $("#judulSoal").html(`
                        <b>Soal No.\t${soal.current_page}</b>\t
                        <span class="text-muted">dari ${soal.last_page}\tSoal</span>
                    `);
                    $("#pertanyaanSoal").html(dataSoal.pertanyaan);

                    options.forEach(function(option, index) {
                        $("#pilihan_" + option)
                            .html(option.toUpperCase() + ". " + dataSoal['pilihan_' + option]);
                    });

                    $("#pilihan_" + dataSoal.jawaban_benar).addClass("text-success");

                    btnPrevSoal.removeAttr("disabled");
                    btnPrevSoal.data("page", soal.current_page - 1);
                    if (soal.prev_page_url == null) {
                        btnPrevSoal.attr("disabled", true);
                    }

                    btnNextSoal.data("page", soal.current_page + 1);
                    btnNextSoal.removeAttr("disabled");
                    if (soal.next_page_url == null) {
                        btnNextSoal.attr("disabled", true);
                    }
                }
            });
        }

        btnPrevSoal.on("click", handlerPageSoal);
        btnNextSoal.on("click", handlerPageSoal);

        function handlerPageSoal() {
            const page = $(this).data("page");
            loadSoal(page);
            localStorage.setItem(itemLsSoal, page);
        }

        let daftarSoalData, daftarSoal,
            daftarSoalOffSet = localStorage.getItem(itemLsDsOffSet) || 0,
            currentPageDaftarSoal = localStorage.getItem(itemLsDaftarSoal) || 1,
            perPageDaftarSoal = localStorage.getItem("perPageDaftarSoal");
        daftarSoalOffSet = currentPageDaftarSoal * perPageDaftarSoal - perPageDaftarSoal;

        loadDaftarSoal();
        function loadDaftarSoal(page = currentPageDaftarSoal) {
            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.list', encrypt($jadwal->id)) }}",
                data: {
                    daftar_soal: page,
                },
                dataType: 'json',
                success: function(res) {
                    let html = "";

                    daftarSoal = res.daftar_soal;
                    daftarSoalData = daftarSoal.data;
                    perPageDaftarSoal = daftarSoal.per_page;

                    daftarSoalData.forEach((item, index) => {
                        html += `
                            <tr class="paginate-soal" data-page="${index + 1 + daftarSoalOffSet}">
                                <td>${index + 1 + daftarSoalOffSet}</td>
                                <td>${item.jawaban_benar.toUpperCase()}</td>
                            </tr>
                        `;
                    });

                    $("#tableDaftarSoal tbody").html(html);
                    $("#infoDaftarSoal").html(daftarSoal.current_page + " / " + daftarSoal.last_page);

                    btnPrevDaftarSoal.removeAttr("disabled");
                    btnPrevDaftarSoal.attr("id", daftarSoal.current_page - 1);
                    if (daftarSoal.prev_page_url == null) {
                        btnPrevDaftarSoal.attr("disabled", true);
                    }

                    btnNextDaftarSoal.attr("id", daftarSoal.current_page + 1);
                    btnNextDaftarSoal.removeAttr("disabled");
                    if (daftarSoal.next_page_url == null) {
                        btnNextDaftarSoal.attr("disabled", true);
                    }
                },
                complete: function() {
                    daftarSoalOffSet += daftarSoalData.length;
                    perPageDaftarSoal = daftarSoal.per_page;
                }
            }).then(function(res) {
                localStorage.setItem("perPageDaftarSoal", perPageDaftarSoal);

                if (daftarSoal.from == null && daftarSoal.to == null) {
                    daftarSoalOffset = daftarSoal.last_page * perPageDaftarSoal - perPageDaftarSoal;
                    loadDaftarSoal(daftarSoal.last_page);
                    localStorage.setItem(itemLsDaftarSoal, daftarSoal.last_page);
                    localStorage.setItem(itemLsDsOffSet, daftarSoalOffset);
                }
            });
        }

        $("#tableDaftarSoal").on("click", ".paginate-soal", handlerPageSoal);
        btnPrevDaftarSoal.on("click", handlerPageDaftarSoal);
        btnNextDaftarSoal.on("click", handlerPageDaftarSoal);

        function handlerPageDaftarSoal() {
            const page = $(this).attr('id');
            daftarSoalOffSet = page * perPageDaftarSoal - perPageDaftarSoal;
            loadDaftarSoal(page);
            localStorage.setItem(itemLsDaftarSoal, page);
            localStorage.setItem(itemLsDsOffSet, daftarSoalOffSet);
        }

        $(document).ready(function() {
            let table = $("table.mahasiswa").DataTable({
                serverSide: true,
                processing: true,
                ajax: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.show', encrypt($jadwal->id)) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'mahasiswa', name: 'mahasiswa'},
                    {data: 'benar', name: 'benar'},
                    {data: 'salah',name: 'salah',},
                    {data: 'tidak_jawab',name: 'tidak_jawab'},
                    {data: 'nilai_ujian',name: 'nilai_ujian'},
                    {data: 'action',name: 'action', className: 'noPrint'},
                ],
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            // refresh table
            $("#refreshTable").on("click", function(e) {
                e.preventDefault();
                table.ajax.reload(null, false);
            });

            $(document).on("click", ".btnLihatHasil", function(e) {
                e.preventDefault();

                let id = $(this).attr("id");
                $("#modalDetailNilai").modal("show");

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.pg.detailNilai', encrypt($jadwal->id)) }}",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(res) {
                        let ujianHasil = res.ujianMhs.ujian_hasil;
                        let data = res.data;
                        let html = "";
                        let status, ragu, jawaban, jawabanMhs;

                        for (let key in data) {
                            $("#" + key).html(data[key]);
                        }

                        ujianHasil.forEach((item, index) => {
                            let dataSoal = item.soal_ujian_pg;
                            let pertanyaan = dataSoal.pertanyaan;
                            let kunciJawaban = dataSoal.jawaban_benar + ". " +
                                dataSoal['pilihan_' + dataSoal.jawaban_benar];

                            if (dataSoal['pilihan_' + item.jawaban] === undefined) {
                                jawabanMhs = "-";
                            } else {
                                jawabanMhs = item.jawaban + ". " + dataSoal['pilihan_' + item.jawaban];
                            }

                            if (item.status == 0) { // Jika jawaban salah
                                if (item.jawaban == null) { // Jika tidak menjawab
                                    status = "<span class='badge badge-secondary'>Tidak Jawab</span>";
                                } else {
                                    status = "<span class='badge badge-danger'>Salah</span>";
                                }
                            } else {
                                status = "<span class='badge badge-success'>Benar</span>";
                            }

                            (item.ragu == 1) ? ragu = "<span class='badge badge-warning'>Ya</span>"
                                : ragu = "Tidak";

                            html += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${pertanyaan}</td>
                                    <td>${kunciJawaban}</td>
                                    <td>${jawabanMhs}</td>
                                    <td class="text-center">${status}</td>
                                    <td class="text-center">${ragu}</td>
                                </tr>
                            `;
                        });

                        $("#detailNilai").html(html);
                    }
                });
            });

            $("#modalDetailNilai").on("hidden.bs.modal", function() { // reset modal
                $("audio").trigger("pause");
            });

            $(".btn-back").click(function () {
                localStorage.removeItem(itemLsSoal);
                localStorage.removeItem(itemLsDaftarSoal);
                localStorage.removeItem(itemLsDsOffSet);
                localStorage.removeItem("perPageDaftarSoal");
            });
        }); // End Document Ready
    </script>
@endpush
