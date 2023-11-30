@extends('layouts.dashboard')

@section('title')
    Profile
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">

                <div class="card card-primary card-outline sticky">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            @php
                                $fileName = Auth::user()->foto;

                                if (file_exists('assets/image/users/' . $fileName)) {
                                    $avatar = asset('assets/image/users/' . $fileName);
                                } else {
                                    $avatar = asset('assets/image/avatar.png');
                                }
                            @endphp

                            <img id="pictureProfile" src="{{ $avatar }}" class="profile-user-img img-fluid img-circle">
                        </div>

                        <h3 id="userName" class="profile-username text-center mt-3 text-uppercase font-weight-bold">
                            {{ Auth::user()->name }}
                        </h3>

                        <p class="text-muted text-center my-1">
                            {{ ucfirst(Auth::user()->getRole()) }}
                        </p>

                        <hr>

                        <div class="row">
                            @if (file_exists('assets/image/users/' . $fileName)) {{-- Jika ada foto profile --}}
                                <div id="uploadPhoto" class="col-lg-9 col-9">
                                    <form id="formUploadPhoto" enctype="multipart/form-data"
                                        action="{{ route('profile.update.photo') }}" method="POST">
                                        @csrf
                                        <div id="buttonUpload">
                                            <input type="file" name="photo" id="photoUpload" class="avatar d-none">
                                            <input type="hidden" name="base64image" name="base64image" id="base64image">

                                            <input id="fotoLama" type="hidden"
                                                value="{{ asset('assets/image/users/' . Auth::user()->foto) }}">

                                            <button type="button" id="changePicture" class="btn btn-primary btn-block"
                                                data-toggle="tooltip" title="Ganti Foto">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div id="deletePhoto" class="col-lg-3 col-3">
                                    <form id="formDeletePicture" enctype="multipart/form-data" action="#"
                                        method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div id="buttonDelete">
                                            <input type="file" name="foto" id="photoDelete" class="avatar d-none">

                                            <button type="button" id="deletePicture" class="btn btn-danger btn-block"
                                                data-toggle="tooltip" title="Hapus Foto">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Jika close modal potong foto --}}
                                <div id="gantiPhoto2" class="col-lg-6 col-6 d-none">
                                    <button type="button" id="changePicture" class="btn btn-primary btn-block"
                                        data-toggle="tooltip" title="Ganti Foto">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <div id="batalUpload2" class="col-lg-6 col-6 d-none">
                                    <button type="button" id="batalUpload" class="btn btn-danger btn-block"
                                        data-toggle="tooltip" title="Batal Upload Foto">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @else {{-- Jika tidak ada foto profile --}}
                                <div class="col-lg-12 col-12 mb-1">
                                    <form id="formUploadPhoto" enctype="multipart/form-data"
                                        action="{{ route('profile.update.photo') }}" method="POST">
                                        @csrf
                                        <div id="buttonUpload">
                                            <input type="file" name="photo" id="photoUpload" class="avatar d-none">
                                            <input type="hidden" name="base64image" name="base64image" id="base64image">

                                            <button type="button" id="changePicture" class="btn btn-primary btn-block"
                                                data-toggle="tooltip" title="Ganti Foto">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div id="gantiPhoto" class="col-lg-6 col-6 d-none">
                                    <button type="button" id="changePicture" class="btn btn-primary btn-block"
                                        data-toggle="tooltip" title="Ganti Foto">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <div id="batalUpload1" class="col-lg-6 col-6 d-none">
                                    <button type="button" id="batalUpload" class="btn btn-danger btn-block"
                                        data-toggle="tooltip" title="Batal Upload Foto">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div> {{-- /.card-body --}}
                </div> {{-- /.card --}}

                {{-- Modal upload --}}
                @include('dashboard._profile._modal-upload-photo')
            </div>

            <div class="col-md-9">
                @include('dashboard._profile._tab-content')
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // =============== SECTION EVENT =============== //

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // global variable
            let $modal = $("#modalUpload");
            let sampleImg = document.getElementById('sampleImage');
            let cropper, cropped;

            $(document).on("click", '#changePicture', function () {
                $("#photoUpload").click();
            });

            $(document).on("change", "#photoUpload", function (e) {
                // Mengambil data file dari event target (input file).
                let files = e.target.files;

                //  Deklarasi fungsi done yang menerima parameter url.
                let done = function(url) {
                    sampleImg.src = url;
                    $modal.modal('show');
                };

                // Deklarasi variabel.
                let reader, file, url;

                if (files && files.length > 0) { // Jika file ada.
                    file = files[0]; // Ambil file pertama.

                    const fileSize = file.size / 1024 / 1024; // Convert ke MB.
                    const validImageTypes = ["image/jpeg", "image/jpg", "image/png",
                        "image/svg+xml"
                    ]; // Tipe file yang diizinkan.

                    if (validImageTypes.includes(file['type'])) { // Jika tipe file sesuai.
                        if (fileSize <= 1) { // Jika ukuran file kurang dari 1 MB.
                            if (URL) { // Jika URL tersedia.
                                done(URL.createObjectURL(file)); // Membuat URL dari file.
                            } else if (FileReader) { // Jika FileReader tersedia.
                                reader = new FileReader(); // Membuat objek FileReader.
                                reader.onload = function(e) { // Ketika file berhasil dibaca.
                                    done(reader.result); // Menampilkan hasil pembacaan file.
                                };
                                reader.readAsDataURL(file); // Membaca file.
                            }
                        } else { // Jika ukuran file lebih dari 1 MB.
                            Swal.fire({
                                icon: "error",
                                html: "Ukuran file harus kurang dari 1 MB!",
                                allowOutsideClick: false,
                            });
                        }
                    } else { // Jika tipe file tidak sesuai.
                        Swal.fire({
                            icon: "error",
                            html: "Tipe file harus berupa gambar (jpg, jpeg, png, svg)!",
                            allowOutsideClick: false,
                        });
                    }
                }
            }); // Photo upload

            $modal.on('shown.bs.modal', function () {
                cropper = new Cropper(sampleImg, {
                    aspectRatio: 1,
                    viewMode: 1,
                    preview: '.preview'
                });
            }).on('hidden.bs.modal', function () {
                cropper.destroy();
                cropper = null;

                showTooltip();
            }); // Cropper

            $(document).on('click', '#cropBtn', function (e) {
                canvas = cropper.getCroppedCanvas({ // Membuat canvas baru.
                    width: 1000,
                    height: 1000,
                });

                canvas.toBlob((blob) => { // Mengubah canvas menjadi blob.
                    url = URL.createObjectURL(blob); // Membuat URL dari blob.
                    let reader = new FileReader(); // Membuat objek FileReader.
                    reader.readAsDataURL(blob); // Membaca blob.
                    reader.onloadend = () => { // Ketika blob berhasil dibaca.
                        let base64data = reader.result; // Hasil pembacaan blob.
                        $modal.modal('hide'); // Menutup modal.

                        $("#base64image").val(base64data);
                        $("#pictureProfile").attr('src', base64data);
                        $("#sideProfile").attr('src', base64data);

                        $("#buttonUpload button").remove();
                        $("#buttonUpload").append(`
                            <button type='submit' class='btn btn-success btn-block mb-2 btn-upload' data-toggle='tooltip'
                                title='Upload Foto'><i class='fas fa-file-upload'></i>
                            </button>
                        `);

                        // jika masih belum diupload fotonya
                        $("#gantiPhoto").removeClass('d-none');
                        $('#batalUpload1').removeClass('d-none');
                        $('#batalUpload1').on('click', batalUpload_1);

                        // jika sudah ada fotonya
                        $("#deletePhoto").addClass('d-none');
                        $("#uploadPhoto").removeClass('col-lg-9 col-9')
                            .addClass('col-lg-12 col-12 mb-1');
                        $("#gantiPhoto2").removeClass('d-none');
                        $('#batalUpload2').removeClass('d-none');
                        $('#batalUpload2').on('click', batalUpload_2);

                        function batalUpload_1() { // jika belum ada fotonya

                            $("#gantiPhoto").addClass('d-none');
                            $('#batalUpload1').addClass('d-none');

                            $("#pictureProfile").attr('src', "{{ asset('assets/image/avatar.png') }}");
                            $("#sideProfile").attr('src', "{{ asset('assets/image/avatar.png') }}");

                            $("#buttonUpload button").remove();
                            $("#buttonUpload").append(`
                                <button id="changePicture" type="button" class="btn btn-primary btn-block" data-toggle="tooltip" title="Ganti Foto" >
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            `);

                            showTooltip();
                        }

                        function batalUpload_2() { // jika sudah ada fotonya

                            $("#uploadPhoto")
                                .removeClass('col-lg-12 col-12 mb-1')
                                .addClass('col-lg-9 col-9');
                            $("#deletePhoto").removeClass('d-none');
                            $("#gantiPhoto2").addClass('d-none');
                            $('#batalUpload2').addClass('d-none');

                            $("#pictureProfile").attr('src', $("#fotoLama").val());
                            $("#sideProfile").attr('src', $("#fotoLama").val());

                            $("#buttonUpload button").remove();
                            $("#buttonUpload").append(`
                                <button type="button" class="btn btn-primary btn-block" data-toggle="tooltip"
                                    title="Ganti Foto" id="changePicture">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            `);

                            showTooltip();
                        }
                    }
                });
            }); // Crop photo

            $("#formUploadPhoto").on("submit", function (e) { // Upload photo
                e.preventDefault();

                $.ajax({
                    type: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    beforeSend: function() {
                        $(".btn-upload").html('<i class="fas fa-spin fa-spinner"></i>');
                    },
                    complete: function() {
                        $(".btn-upload").html('<i class="fas fa-file-upload"></i>');
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            if (res.tipe == 'validation') {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.message.photo[0],
                                    allowOutsideClick: false,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    html: res.message,
                                    allowOutsideClick: false,
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'success',
                                html: res.message,
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr, thrownError) {
                        alert(status.xhr + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }); // Form upload photo

            $(document).on("click", "#deletePicture", function (e) { // Delete photo
                e.preventDefault();

                Swal.fire({
                    icon: 'warning',
                    html: 'Apakah anda yakin ingin menghapus foto profil?',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: 'gray',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "PUT",
                            url: "{{ route('profile.delete.photo') }}",
                            data: {
                                _method: "PUT",
                                _token: "{{ csrf_token() }}",
                            },
                            dataType: "json",
                            beforeSend: function() {
                                $("#deletePicture").html('<i class="fas fa-spin fa-spinner"></i>');
                            },
                            complete: function() {
                                $("#deletePicture").html('<i class="fas fa-trash"></i>');
                            },
                            success: function(res) {
                                if (res.status == 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        html: res.message,
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload();
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        html: res.message,
                                        allowOutsideClick: false,
                                    });
                                }
                            },
                            error: function(xhr, thrownError) {
                                alert(status.xhr + "\n" + xhr.responseText + "\n" +
                                thrownError);
                            }
                        });
                    } // end if result is confirmed
                });
            }); // Form delete picture

            function showTooltip() { // Show tooltip
                $('body').find('[data-toggle="tooltip"]').tooltip({
                    trigger: 'hover'
                });
            }
        });
    </script>
@endpush
