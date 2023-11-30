{{-- Modal Slide --}}
<div class="modal fade" id="modalSlide" tabindex="-1" role="dialog" aria-labelledby="Slide" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form action="{{ route('manajemen.kuliah.materi.dosen.storeSlide') }}" method="POST" id="formSlide"
                enctype="multipart/form-data" autocomplete="off">
                @csrf
                @method('POST')

                <input type="hidden" name="jadwal" value="{{ encrypt($jadwal->id) }}">
                <input type="hidden" name="kelas_id" value="{{ encrypt($jadwal->kelas->id) }}">
                <input type="hidden" name="matkul_id" value="{{ encrypt($jadwal->matkul->id) }}">

                <div class="modal-header p-2">
                    <h5 class="modal-title font-weight-bold ml-2">Form - Slide Materi</h5>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body formCreateSlide">
                    <div class="form-group">
                        <label for="judul">Judul Slide</label>
                        <input type="text" class="form-control judul" id="slide_judul" name="judul"
                            placeholder="Masukkan judul Slide disini">
                        <span class="invalid-feedback d-block error-text slide_judul_error"></span>
                    </div>
                    <div class="form-group slide">
                        <label for="file_or_link">File Slide</label>
                        <div class="custom-file">
                            <input id="slideMateri" type="file" name="file_or_link" class="custom-file-input"
                                multiple>
                            <label class="custom-file-label" id="slide_file_or_link" for="file_or_link">
                                Tidak ada file yang dipilih..
                            </label>
                        </div>
                        <span class="invalid-feedback d-block error-text slide_file_or_link_error"></span>
                    </div>
                    <div class="row Edit" style="display: none">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <ul id="previewFile"
                                        class="mailbox-attachments d-flex align-items-stretch clearfix m-0">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn_slide">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Slide --}}
<div class="modal fade" id="modalEditSlide" tabindex="-1" role="dialog" aria-labelledby="Slide"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form action="#" method="POST" id="formEditSlide" enctype="multipart/form-data"
                autocomplete="off">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id">

                <div class="modal-header p-2">
                    <h5 class="modal-title font-weight-bold ml-2">Form - Edit Slide Materi</h5>
                    <button type="button" class="btn btn-primary btn_slide_close" data-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body formCreateSlide">

                    <div class="form-group">
                        <label for="judul">Judul Slide</label>
                        <input type="text" class="form-control judul" id="edit_slide_judul" name="judul"
                            placeholder="Masukkan judul Slide disini">
                        <span class="invalid-feedback d-block error-text edit_slide_judul_error"></span>
                    </div>

                    <div class="form-group slide">
                        <label for="file_or_link">File Slide</label>
                        <div class="custom-file">
                            <input id="edit_slideMateri" type="file" name="file_or_link"
                                class="custom-file-input">
                            <label class="custom-file-label" id="edit_slide_file_or_link" for="file_or_link">
                                Tidak ada file yang dipilih..
                            </label>
                        </div>
                        <span class="invalid-feedback d-block error-text edit_slide_file_or_link_error"></span>
                    </div>

                    <div class="row previewFileSlideEdit" style="display: none">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">

                                    <ul id="previewFileEdit"
                                        class="mailbox-attachments d-flex align-items-stretch clearfix m-0">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn_slide_close"
                        data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn_slide_edit">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            $("#modalSlide").on('hidden.bs.modal', function() {
                $("#slideMateri").val('');
                $("#slideMateri").next('.custom-file-label').html('Tidak ada file yang dipilih..');

                $(document).find('.form-control').removeClass('is-invalid');
                $(document).find('.custom-file-input').removeClass('is-invalid');
                $(document).find('.error-text').text('');

                $(".Edit").hide('fade');
            });

            // Create Slide
            $("#formSlide").on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('.btn_slide').attr('disabled', true);
                        $('.btn_slide').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass('is-invalid');
                        $(document).find('.custom-file-input').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.btn_slide').removeAttr('disabled');
                        $('.btn_slide').html('Simpan');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            $.each(res.errors, function(key, val) {
                                $('span.slide_' + key + '_error').text(val[0]);
                                $('input[name="' + key + '"]').addClass('is-invalid');
                            });
                        } else if (res.status == 401) {
                            $("#modalSlide").modal("hide");

                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                html: res.message,
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    localStorage.setItem("tab", "slide");
                                    window.location.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            });

            // show modal edit slide
            $(".edit_slide").on('click', function(e) {
                e.preventDefault();

                let id = $(this).val();
                $("#modalEditSlide").modal("show");

                $.ajax({
                    type: "GET",
                    url: "{{ route('manajemen.kuliah.materi.dosen.edit', ':id') }}".replace(':id', id),
                    success: function(res) {

                        if (res.status == 404) {
                            $("#modalEditSlide").modal("hide");

                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                                allowOutsideClick: false,
                            });
                        } else {
                            $("#edit_id").val(id);
                            $("#edit_slide_judul").val(res.judul);
                            $("#formEditSlide").find('.custom-file-label')
                                .html("1 File dipilih");
                            $(".previewFileSlideEdit").show('fade');

                            function previewFile(fileName, className = null) {
                                $("#previewFileEdit").append(
                                    '<li class="' + className +
                                    '" style="border-radius: 10px;">' +
                                    '<span class="mailbox-attachment-icon">' +
                                    '<i class="far fa-file-archive"></i>' +
                                    '</span>' +
                                    '<div class="mailbox-attachment-info">' +
                                    '<a href="javascript:void(0)" class="mailbox-attachment-name cursor_default">' +
                                    '<p class="m-0">' + fileName + '</p>' +
                                    '</a>' +
                                    '</div>' +
                                    '</li>');
                            }

                            previewFile(res.file_or_link);

                            $(document).on('change', '#edit_slideMateri', function(e) {

                                let fileName = $(this).val();

                                let files = e.target.files;
                                let file = files[0];

                                if (fileName == undefined || fileName == "") {
                                    $(this).next('.custom-file-label')
                                        .html('Tidak ada file yang dipilih..');

                                    $("#previewFileEdit").children().remove();

                                    previewFile(res.file_or_link);

                                    $(document).find('.custom-file-input')
                                        .removeClass('is-invalid');
                                    $(document).find('span.error-text').text('');

                                } else {
                                    setInterval(() => {
                                        if ($("#previewFileEdit .on_change").length > 1) {
                                            $("#previewFileEdit .on_change")
                                                .next().remove();
                                        }
                                    }, 0);

                                    $("#previewFileEdit").children().remove();
                                    $(this).next('.custom-file-label')
                                        .html(files.length + ' File Dipilih');
                                }

                                const fileTypes = [
                                    'application/x-zip-compressed',
                                    'application/zip'
                                ];

                                if (!fileTypes.includes(file['type'])) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'File harus berformat .zip',
                                        allowOutsideClick: false,
                                    });

                                    $(this).val('');
                                    $(this).next('.custom-file-label')
                                        .html('1 File Dipilih');

                                    $(document).find('.custom-file-input')
                                        .removeClass('is-invalid');
                                    $(document).find('span.error-text').text('');

                                    previewFile(res.file_or_link);

                                } else {

                                    let picReader = new FileReader();

                                    picReader.addEventListener('load', function(e) {
                                        let picFile = e.target;
                                        previewFile(file.name, 'on_change');
                                    });

                                    picReader.readAsDataURL(file);
                                }
                            });
                        }
                    }
                });
            });

            // reset form edit slide
            $("#modalEditSlide").on('hidden.bs.modal', function() {

                $('#edit_slideMateri').val('');
                $('#edit_slideMateri').next('.custom-file-label').html('1 File Dipilih');

                $("#previewFileEdit").children().remove();

                $(document).find('span.error-text').text('');
                $(document).find('.form-control').removeClass('is-invalid');
                $(document).find('.custom-file-input').removeClass('is-invalid');
            });

            // update slide
            $("#formEditSlide").on('submit', function(e) {
                e.preventDefault();

                let id = $("#edit_id").val();

                $.ajax({
                    type: $(this).attr('method'),
                    url: "{{ route('manajemen.kuliah.materi.dosen.updateSlide', ':id') }}"
                        .replace(':id', id),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        $('.btn_slide_edit').attr('disabled', true);
                        $('.btn_slide_edit').html('<i class="fas fa-spin fa-spinner"></i>');
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.btn_slide_edit').removeAttr('disabled');
                        $('.btn_slide_edit').html('Update');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            $.each(res.errors, function(key, val) {
                                $('span.edit_slide_' + key + '_error').text(val[0]);
                                $('input[name="' + key + '"]').addClass(
                                    'is-invalid');
                            });
                        } else if (res.status == 401) {
                            $("#modalEditSlide").modal("hide");

                            Swal.fire({
                                icon: 'error',
                                html: res.message,
                            });
                        } else {
                            $("#modalEditSlide").modal("hide");

                            if (res.nothing == 1) {
                                icon = "info";
                                message = "Tidak ada perubahan data";
                                action = false;
                            } else {
                                icon = "success";
                                message = "Slide berhasil diubah!";
                                action = true;
                            }

                            Swal.fire({
                                icon: icon,
                                html: message,
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    if (action == true) {
                                        localStorage.setItem("tab", "slide");
                                        location.reload();
                                    }
                                }
                            });
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
