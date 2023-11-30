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
                                        : {{ $ujian->soalUjianEssay->count() }} Soal
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
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h5 class="m-0 p-0 font-weight-bold">
                    Informasi Soal Ujian
                </h5>
            </div>
        </div>
    </div>

    @if ($ujian->soalUjianEssay->isNotEmpty())
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
                    <div class="card-text" id="pertanyaanSoal"></div>
                </div>
            </div> {{-- End card --}}
        </div> {{-- End col --}}

        <div class="col-lg-3" id="daftarSoal">
            <div class="card">
                <div class="card-header p-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="m-0 p-0 font-weight-bold p-2">
                            List Soal
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
                    </div> {{-- End d-flex --}}
                </div> {{-- End card-header --}}

                <div class="card-body p-2">
                    <table id="tableDaftarSoal" class="table table-hover text-center">
                        <thead>
                            <tr>
                                <th>No. Soal</th>
                            </tr>
                        </thead>
                        <tbody style="cursor: pointer;" id="listJawaban"></tbody>
                    </table>
                </div>
            </div> {{-- End card --}}
        </div> {{-- End col --}}
    @else
        <div class="col-lg-12 mb-3">
            <div class="alert alert-danger">
                <h5>Alert!</h5>
                Soal Ujian Tidak ada.
            </div>
        </div>
    @endif {{-- End if soal ujian essay --}}
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
            <button type="button" class="list-group-item list-group-item-action active cursor_default">
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
    </div> {{-- End col --}}

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
                            <th>Nilai</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div> {{-- End col --}}

</div> {{-- End row --}}

@include('dashboard.dosen.ujian.soal.essay._modal-nilai')

@include('dashboard.dosen.ujian.soal.essay._modal-hasil')

@push('js')
    <script>
        // ====================== GLOBAL VARIABLE ====================== //

        const idUjian = "{{ encrypt($ujian->id ?? '') }}";
        const idJadwalEncrypt = "{{ encrypt($jadwal->id) }}";
        const jadwalId = "{{ $jadwal->id }}";
        const ujianId = parseInt("{{ $ujian->id }}");
        const csrfToken = "{{ csrf_token() }}";
        const btnPrevSoal = $("#prevSoal");
        const btnNextSoal = $("#nextSoal");
        const btnPrevDaftarSoal = $("#prevDaftarSoal");
        const btnNextDaftarSoal = $("#nextDaftarSoal");
        const itemLsSoal = `currentPageSoal_${ujianId}_${jadwalId}`;
        const itemLsDaftarSoal = `currentPageDaftarSoal_${ujianId}_${jadwalId}`;
        const itemLsDsOffSet = `daftarSoalOffSet_${ujianId}_${jadwalId}`;

        // ====================== EVENT ====================== //

        let currentPageSoal = localStorage.getItem(itemLsSoal) || 1; // Current page soal

        loadSoal(); // Load soal
        function loadSoal(page = currentPageSoal) { // Load soal
            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.essay.list', encrypt($jadwal->id)) }}",
                data: {
                    soal: page
                },
                dataType: 'json',
                success: function(res) {
                    let soal = res.soal;
                    let soalData = soal.data[0];

                    if (soalData == undefined) { // Jika soal tidak ada
                        loadSoal(soal.last_page); // Load soal terakhir
                        localStorage.setItem(itemLsSoal, soal.last_page);
                    }

                    // Set data
                    $("#judulSoal").html("<b>Soal No. " + soal.current_page +
                        "</b> <span class='text-muted'>dari " + soal.last_page + " Soal</span>");
                    $("#pertanyaanSoal").html(soalData.pertanyaan);
                    $("#pertanyaanSoal p").addClass("m-0 p-0");

                    btnPrevSoal.removeAttr("disabled");
                    btnPrevSoal.attr("id", soal.current_page - 1);
                    if (soal.prev_page_url == null) {
                        btnPrevSoal.attr("disabled", true);
                    }

                    btnNextSoal.attr("id", soal.current_page + 1);
                    btnNextSoal.removeAttr("disabled");
                    if (soal.next_page_url == null) {
                        btnNextSoal.attr("disabled", true);
                    }
                },
            }); // End ajax
        } // End loadSoal

        btnPrevSoal.on("click", handlerSoalPaginate);
        btnNextSoal.on("click", handlerSoalPaginate);

        function handlerSoalPaginate() { // Handler soal paginate
            const page = $(this).attr("id");
            loadSoal(page);
            localStorage.setItem(itemLsSoal, page);
        }

        // =========== DAFTAR SOAL =========== //

        let daftarSoalData, daftarSoal,
            perPageDaftarSoal = localStorage.getItem(`${noIndukUser}_perPageDaftarSoal`),
            daftarSoalOffset = localStorage.getItem(itemLsDsOffSet) || 0,
            currentPageDaftarSoal = localStorage.getItem(itemLsDaftarSoal) || 1;

        daftarSoalOffset = currentPageDaftarSoal * perPageDaftarSoal - perPageDaftarSoal; // Set jawaban offset

        loadDaftarSoal(); // Load

        function loadDaftarSoal(page = currentPageDaftarSoal) {
            $.ajax({
                type: "GET",
                url: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.soal.essay.list', encrypt($jadwal->id)) }}",
                data: {
                    daftar_soal: page,
                },
                dataType: 'json',
                success: function(res) {
                    let html = "";

                    daftarSoal = res.daftar_soal;
                    daftarSoalData = res.daftar_soal.data;

                    daftarSoalData.forEach((item, index) => {
                        html += `
                            <tr class="paginate-soal" id="${index + 1 + daftarSoalOffset}">
                                <td>${index + 1 + daftarSoalOffset}</td>
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
                    daftarSoalOffset += daftarSoal.length;
                    perPageDaftarSoal = daftarSoal.per_page;
                }
            }).then(function(res) {
                localStorage.setItem(`${noIndukUser}_perPageDaftarSoal`, perPageDaftarSoal);

                if (daftarSoal.from == null && daftarSoal.to == null) {
                    daftarSoalOffset = daftarSoal.last_page * perPageDaftarSoal - perPageDaftarSoal;
                    loadDaftarSoal(daftarSoal.last_page);
                    localStorage.setItem(itemLsDaftarSoal, daftarSoal.last_page);
                    localStorage.setItem(itemLsDsOffSet, daftarSoalOffset);
                }
            });
        }

        $("#tableDaftarSoal").on("click", ".paginate-soal", handlerSoalPaginate);
        btnPrevDaftarSoal.on("click", handlerDaftarSoalPaginate);
        btnNextDaftarSoal.on("click", handlerDaftarSoalPaginate);

        function handlerDaftarSoalPaginate() {
            const page = $(this).attr('id');
            daftarSoalOffset = page * perPageDaftarSoal - perPageDaftarSoal;
            loadDaftarSoal(page);
            localStorage.setItem(itemLsDaftarSoal, page);
            localStorage.setItem(itemLsDsOffSet, daftarSoalOffset);
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on("click", ".btnNilaiDulu", function (e) {
                e.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    html: '<span class="font-weight-bold text-uppercase">Oops..</span><hr>Sepertinya anda belum menilai ujian essay untuk mahasiswa ini. Silahkan dinilai dulu semuanya, agar bisa melihat hasilnya.',
                });
            });

            let tableMhs = $("table.mahasiswa").DataTable({
                serverSide: true,
                processing: true,
                ajax: "{{ route('manajemen.kuliah.jadwal.dosen.ujian.show', encrypt($jadwal->id)) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'mahasiswa', name: 'mahasiswa'},
                    {data: 'nilai_ujian', name: 'nilai_ujian'},
                    {
                        className: 'noPrint',
                        data: 'action',
                        name: 'action',
                        createdCell: function (td, cellData, rowData, row, col) {
                            $(td).css('width', '15%');
                        }
                    },
                ],
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                tableMhs.button(0).trigger();
            });

            // refreshTable add animation loading
            $("#refreshTable").on("click", function(e) {
                e.preventDefault();
                $("#refreshTable").find("i").addClass("fa-spin");
                tableMhs.ajax.reload(function () {
                    $("#refreshTable").find("i").removeClass("fa-spin");
                });
            });

            $(".btn-back").click(function () {
                localStorage.removeItem(itemLsSoal);
                localStorage.removeItem(itemLsDaftarSoal);
                localStorage.removeItem(itemLsDsOffSet);
                localStorage.removeItem(`${noIndukUser}_perPageDaftarSoal`);
            });

        }); // End document ready
    </script>
@endpush
