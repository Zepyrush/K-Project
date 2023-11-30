@extends('layouts.dashboard')

@section('title', 'Materi ' . $jadwal->matkul->nama . ' - Kelas ' . $jadwal->kelas->kode)

@section('content')
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12 sticky">
                <div class="card card-primary card-outline">
                    <div class="card-header p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="dropdown">
                                @if ($jadwalDiBuka)
                                    <a href="{{ route('manajemen.kuliah.kelas.dosen.index', encrypt($jadwal->id)) }}" class="btn btn-primary btn-back btn-sm">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                @else
                                    <a href="{{ route('manajemen.kuliah.jadwal.dosen.kuliah.index') }}" class="btn btn-primary btn-sm btn-back">
                                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                                    </a>
                                @endif

                                <button class="btn btn-success btn-sm dropdown-toggle ml-1" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class="fa fa-plus mr-1"></i> Tambah
                                </button>

                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                    @if ($jadwalDiBuka)
                                        <a data-id="{{ encrypt($jadwal->id) }}" class="add_btn dropdown-item"
                                            href="javascript:void(0)">
                                            Materi
                                        </a>
                                    @endif

                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                                        data-target="#modalSlide">
                                        Slide Pembelajaran
                                    </a>
                                </div>
                            </div>

                            <h5 class="font-weight-bold m-0 p-0 ml-2">
                                @yield('title')
                            </h5>

                            <div></div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card card-primary card-outline mb-2">
                    <div class="card-header p-1">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link materi-tab active" href="#materiTambahan" data-toggle="tab">
                                    Materi Tambahan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link materi-tab" href="#vidio" data-toggle="tab">
                                    Vidio Pembelajaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link materi-tab" href="#slide" data-toggle="tab">
                                    Slide Pembelajaran
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header p-2 print-materi">
                        <button id="cetakTable" class="btn btn-primary btn-sm">
                            <i class="fas fa-print mr-1"></i> Cetak
                        </button>
                        <button id="refreshTable" class="btn btn-warning btn-sm ml-1"
                            data-toggle="tooltip" title="Refresh Table">
                            <i class="fas fa-sync"></i>
                        </button>
                    </div>
                    <div class="card-body table-responsive">
                        <div class="tab-content">
                            <div class="active tab-pane fade show" id="materiTambahan">

                                <table class="table table-hover" id="tableMateri">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Judul</th>
                                            <th>Pertemuan</th>
                                            <th>Deskripsi</th>
                                            <th>Upload</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            @include('dashboard.dosen._materi._video')

                            @include('dashboard.dosen._materi._slide')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.dosen._materi._modal-materi')

    @include('dashboard.dosen._materi._modal-slide')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("a.materi-tab").click(function() {  // ketika tab di klik
                ($(this).attr('href') === '#materiTambahan') ?
                    $(".print-materi").show('fade') :
                    $(".print-materi").hide('fade');
            });

            // cek apakah dari jadwal masuknya ke materinya
            if (localStorage.getItem(`${noIndukUser}_jadwal`) == 'true') {
                $("a.btn-back").attr("href", "{{ route('manajemen.kuliah.jadwal.dosen.kuliah.index') }}");
                $("a.btn-back").click(function() {
                    localStorage.removeItem(`${noIndukUser}_jadwal`);
                });
                $("a.nav-link").click(function () {
                    localStorage.removeItem(`${noIndukUser}_jadwal`);
                });
            }

            // ketika ada file yang dipilih
            $(document).on('change', 'input[type="file"]#pdfMateri', function(event) {
                let fileName = $(this).val();

                if (fileName == undefined || fileName == "") {
                    $(this).next('.custom-file-label').html('Tidak ada gambar yang dipilih..');
                } else {
                    $(this).next('.custom-file-label').html(event.target.files[0].name);
                }
            });

            // jika insert itu tipe vidio maka akan menampilkan tab vidio
            if (localStorage.getItem("tab") == "vidio") {
                $("a[href='#vidio'].nav-link").tab('show');
                localStorage.removeItem("tab");
            } else if (localStorage.getItem("tab") == "slide") {
                $("a[href='#slide'].nav-link").tab('show');
                localStorage.removeItem("tab");
            }

            // preview file zip multiple slideMateri
            $(document).on('change', '#slideMateri', function(event) {

                let fileName = $(this).val(); // mengambil nama file

                if (fileName == undefined || fileName == "") { // jika tidak ada file yang dipilih
                    $(this).next('.custom-file-label').html('Tidak ada file yang dipilih..');
                    $(".Edit").hide('fade');
                } else {
                    $(this).next('.custom-file-label').html(event.target.files.length + ' File Dipilih');
                }

                let files = event.target.files; // mengambil file yang dipilih
                let output = document.getElementById('previewFile'); // id div preview file
                output.innerHTML = ''; // kosongkan div preview file

                for (let i = 0; i < files.length; i++) { // looping file yang dipilih
                    let file = files[i]; // mengambil file

                    const fileTypes = ['application/x-zip-compressed', 'application/zip']; // format file yang diizinkan

                    if (!fileTypes.includes(file['type'])) { // jika file tidak sesuai dengan format yang diizinkan
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'File harus berformat .zip',
                            allowOutsideClick: false,
                        });

                        $("#slideMateri").val('');
                        $("#slideMateri").next('.custom-file-label').html('Tidak ada file yang dipilih..');

                        $(".Edit").hide('fade');
                    } else { // jika file sesuai dengan format yang diizinkan
                        $(".Edit").show('fade');

                        let picReader = new FileReader(); // membuat objek FileReader

                        picReader.addEventListener('load', function(event) { // menambahkan event load pada objek FileReader

                            let picFile = event.target; // mengambil file yang dipilih

                            // covert file size to KB
                            let fileSize = (file.size / 1024).toFixed(2);

                            let div = document.createElement('li');
                            div.style.borderRadius = '10px';
                            div.innerHTML = '<span class="mailbox-attachment-icon">' +
                                '<i class="far fa-file-archive"></i>' +
                                '</span>' +
                                '<div class="mailbox-attachment-info">' +
                                '<a href="javascript:void(0)" class="mailbox-attachment-name cursor_default">' +
                                '<p class="m-0">' + file.name + '</p>' +
                                '</a>' +
                                '<span class="mailbox-attachment-size clearfix mt-1 d-none">' +
                                '<span>' + fileSize + ' KB</span>' +
                                '</span>' +
                                '</div>';

                            output.insertBefore(div, null); // menambahkan div preview file ke dalam div preview
                        });

                        picReader.readAsDataURL(file); // mengambil file yang dipilih
                    }
                }
            });

            // datatable materi
            var table = $("#tableMateri").DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('manajemen.kuliah.materi.dosen.index', encrypt($jadwal->id)) }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'judul', name: 'judul' },
                    { data: 'pertemuan', name: 'pertemuan' },
                    { data: 'deskripsi', name: 'deskripsi' },
                    { data: 'diupload_pada', name: 'diupload_pada' },
                    { className: 'noPrint', data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

            $("#cetakTable").on("click", function(e) {
                e.preventDefault();
                table.button(0).trigger();
            });

            // menyesuaikan lebar tabel
            $("#tableMateri").css('width', '100%');
            table.columns.adjust().draw();

            // refresh table
            $("#refreshTable").on("click", function() {
                table.ajax.reload(null, false);
            });

            // reset modal create
            $('#modalCreate').on('hidden.bs.modal', function() {
                $("#add_tipe").select2("val", ' ');
                $("#typeLink").hide("fade");
                $("#typeFile").hide("fade");
                $(".submitAdd").attr('disabled', true);
                $(".custom-file-label").html("Cari file materinya..");
                var formAddMateri = $("#formAddMateri");
                formAddMateri.trigger("reset");
                formAddMateri.find('.custom-file-input, .form-control').removeClass('is-invalid');
                formAddMateri.find('.error-text').text('');
            });

            // reset modal edit
            $("#modalEdit").on('hidden.bs.modal', function() {
                $("#formEditMateri")[0].reset();
                $("#edit_tipe").val(null).trigger('change');
                $("#edit_pdf").hide('fade');
                $("#edit_youtube").hide('fade');
                $(".submitEdit").attr('disabled', true);
                $(document).find('.form-control').removeClass('is-invalid');
            });

            // show modal add materi
            $(".add_btn").on("click", function() {

                let id = $(this).data("id");

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.materi.dosen.create', ':id') }}".replace(':id',
                        id),
                    success: function(res) {
                        if (res.status == 500) {
                            if (res.error == 'absensi') {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.message,
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('manajemen.kuliah.kelas.dosen.index', encrypt($jadwal->id)) }}";
                                    }
                                })
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    html: res.message,
                                    allowOutsideClick: false,
                                });
                            }

                        } else {
                            $("#modalCreate").modal("show");

                            $("#add_pertemuan").val(res.pertemuan.pertemuan);

                            function select2Create() {
                                $("#add_tipe").select2({
                                    placeholder: "Pilih Tipe Materi",
                                    allowClear: true,
                                    width: '100%',
                                    dropdownParent: $('#modalCreate'),
                                });
                            }

                            select2Create();

                            $("#add_tipe").on("change", function() {
                                let tipe = $(this).val();

                                if (tipe == "pdf") {
                                    $("#typeFile").show("fade");
                                    $("#typeLink").hide("fade");
                                    $(".submitAdd").removeAttr('disabled');
                                } else if (tipe == "youtube") {
                                    $("#typeLink").show("fade");
                                    $("#typeFile").hide("fade");
                                    $(".submitAdd").removeAttr('disabled');
                                } else {
                                    select2Create();
                                    $("#typeLink").hide("fade");
                                    $("#typeFile").hide("fade");
                                    $(".submitAdd").attr('disabled', true);
                                }
                            });
                        }

                    }
                });
            });

            // insert materi
            $("#formAddMateri").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.submitAdd').attr('disabled', true);
                        $('.submitAdd').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.submitAdd').removeAttr('disabled');
                        $('.submitAdd').html('Tambah');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            $.each(res.errors, function(key, val) {
                                $('span.' + key + '_error').text(val[0]);
                                $("#add_" + key).addClass('is-invalid');
                                $("#pdfMateri").addClass('is-invalid');
                            });
                        } else if (res.status == 401) {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            })
                        } else {
                            $("#modalCreate").modal("hide");

                            if ($("#add_tipe").val() == "pdf") {
                                table.ajax.reload(null, false);

                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                            } else if ($("#add_tipe").val() == "youtube") {
                                Swal.fire({
                                    icon: 'success',
                                    html: res.message,
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        localStorage.setItem("tab", "vidio");
                                        window.location.reload();
                                    }
                                });
                            }
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // show modal edit materi
            $(document).on("click", ".edit_btn", function(e) {
                e.preventDefault();

                let id = $(this).val();

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.materi.dosen.edit', ':id') }}".replace(':id', id),
                    success: function(res) {
                        $("#modalEdit").modal('show');

                        $.each(res, function(key, val) {
                            if (key != 'file_or_link') {
                                $("#edit_" + key).val(val);

                                if (key == 'id') {
                                    $("#edit_id").val(id);
                                }
                            }
                        });

                        function select2Edit() {
                            $("#edit_tipe").select2({
                                placeholder: "Pilih Tipe Materi",
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#modalEdit'),
                            });
                        }

                        select2Edit();

                        if ($('#edit_tipe option:selected').val() == 'pdf') {
                            $("#edit_pdf").show('fade');
                            $("#edit_pdf label.custom-file-label").html(res.file_or_link);
                            $(".submitEdit").removeAttr('disabled');
                        } else {
                            $("#edit_youtube").show('fade');
                            $("#edit_youtube input").val(res.file_or_link);
                            $(".submitEdit").removeAttr('disabled');
                        }

                        $('#edit_tipe').on('change', function() {
                            if ($('#edit_tipe option:selected').val() == 'pdf') {
                                $("#edit_pdf").show('fade');
                                $("#edit_youtube").hide('fade');
                                $(".submitEdit").removeAttr('disabled');
                            } else if ($('#edit_tipe option:selected').val() ==
                                'youtube') {
                                $("#edit_pdf").hide('fade');
                                $("#edit_youtube").show('fade');
                                $(".submitEdit").removeAttr('disabled');
                            } else {
                                select2Edit();
                                $("#edit_pdf").hide('fade');
                                $("#edit_youtube").hide('fade');
                                $(".submitEdit").attr('disabled', true);
                            }
                        });
                    }
                });
            });

            // update materi
            $("#formEditMateri").on("submit", function(e) {
                e.preventDefault();

                let id = $("#edit_id").val();

                $.ajax({
                    url: "{{ route('manajemen.kuliah.materi.dosen.update', ':id') }}".replace(':id',
                        id),
                    type: $(this).attr("method"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('.submitEdit').attr('disabled', true);
                        $('.submitEdit').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.submitEdit').removeAttr('disabled');
                        $('.submitEdit').html('Update');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            $.each(res.errors, function(key, val) {
                                $('span.' + key + '_error').text(val[0]);
                                $("#edit_" + key).addClass('is-invalid');
                            });
                        } else if (res.status == 401) {
                            $("#modalEdit").modal("hide");

                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            })
                        } else {

                            $("#modalEdit").modal("hide");

                            if (res.nothing == 1) {
                                icon = "info";
                                message = res.message;
                                action = false;
                            } else {
                                icon = "success";
                                message = res.message;
                                action = true;
                            }

                            if ($("#edit_tipe").val() == "pdf") {
                                if ($("a[href='#vidio'].nav-link").hasClass("active")) {
                                    Swal.fire({
                                        icon: icon,
                                        html: message,
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            if (action == true) {
                                                location.reload();
                                            }
                                        }
                                    });
                                } else {
                                    table.ajax.reload(null, false);

                                    Toast.fire({
                                        icon: icon,
                                        title: message
                                    });
                                }
                            } else if ($("#edit_tipe").val() == "youtube") {

                                Swal.fire({
                                    icon: icon,
                                    html: message,
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        if (action == true) {
                                            localStorage.setItem("tab", "vidio");
                                            location.reload();
                                        }
                                    }
                                });
                            }
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });

            // show delete modal materi
            $(document).on("click", ".del_btn", function(e) {
                e.preventDefault();

                $("#modalDelete").modal("show");

                let id = $(this).val();
                let judul = $(this).data("judul");
                let tipe = $(this).data("tipe");

                $("#del_id").val(id);

                if (tipe == 'slide') {
                    $("#text_del")
                        .text('Apakah anda yakin ingin menghapus SLIDE dengan nama ' + judul + ' ?');
                } else if (tipe == 'youtube') {
                    $("#text_del")
                        .text('Apakah anda yakin ingin menghapus VIDIO dengan judul ' + judul + ' ?');
                } else {
                    $("#text_del")
                        .text('Apakah anda yakin ingin menghapus MATERI dengan judul ' + judul + ' ?');
                }

                $("#tipe_del").val(tipe);
            });

            // delete materi
            $("#formHapusMateri").on("submit", function(e) {
                e.preventDefault();

                id = $("#del_id").val();

                $.ajax({
                    type: $(this).attr("method"),
                    url: "{{ route('manajemen.kuliah.materi.dosen.delete', ':id') }}".replace(':id',
                        id),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.btnDelete').attr('disabled', true);
                        $('.btnDelete').html('<i class="fas fa-spin fa-spinner"></i>');
                    },
                    complete: function() {
                        $('.btnDelete').removeAttr('disabled');
                        $('.btnDelete').html('Hapus');
                    },
                    success: function(res) {

                        $("#modalDelete").modal("hide");

                        if (res.status == 401) {
                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            })
                        } else {

                            if ($("#tipe_del").val() == "youtube") {
                                Swal.fire({
                                    icon: 'success',
                                    html: res.message,
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        localStorage.setItem("tab", "vidio");
                                        window.location.reload();
                                    }
                                });
                            } else if ($("#tipe_del").val() == "slide") {
                                Swal.fire({
                                    icon: 'success',
                                    html: "Berhasil menghapus slide!",
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        localStorage.setItem("tab", "slide");
                                        window.location.reload();
                                    }
                                });
                            } else {
                                table.ajax.reload(null, false);

                                Toast.fire({
                                    icon: 'success',
                                    title: res.message
                                });
                            }
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (xhr.status == 403) {
                            Swal.fire({
                                icon: 'error',
                                html: "Anda tidak memiliki akses untuk melakukan ini!",
                                allowOutsideClick: false,
                            });
                        } else {
                            alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                        }
                    }
                });
            });
        });
    </script>
@endpush
