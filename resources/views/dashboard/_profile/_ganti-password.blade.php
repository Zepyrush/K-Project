<div class="tab-pane fade show" id="password">
    <form id="formUpdatePassword" method="POST"
        action="{{ route('profile.update.password') }}" class="form-horizontal">
        @csrf
        @method('PUT')

        <div class="form-group row align-items-start">
            <label for="password" class="col-sm-3 font-weight-bold col-form-label">
                <i class="fas fa-lock mr-2 text-primary"></i>
                PW Lama
            </label>

            <div class="col-sm-9">
                <input type="password" class="form-control" name="oldpass" id="oldpass"
                    placeholder="Masukkan password yang sekarang">
                <span class="invalid-feedback d-block error-text oldpass_error"></span>
            </div>
        </div>

        <div class="form-group row align-items-start">
            <label for="password" class="col-sm-3 font-weight-bold col-form-label">
                <i class="fas fa-key mr-2 text-primary"></i>
                PW Baru
            </label>
            <div class="col-sm-9">
                <input type="password" class="form-control" name="newpass" id="newpass"
                    placeholder="Masukkan password baru">
                <span class="invalid-feedback d-block error-text newpass_error"></span>
            </div>
        </div>

        <div class="form-group row align-items-start">
            <label for="emailSet" class="col-sm-3 font-weight-bold col-form-label">
                <i class="fas fa-check mr-2 text-primary"></i>
                Konfirmasi PW
            </label>
            <div class="col-sm-9">
                <input id="confirmpass" type="password" class="form-control"
                    name="confirmpass" placeholder="Konfirmasi password baru">
                <span class="invalid-feedback d-block error-text confirmpass_error"></span>
            </div>
        </div>

        <div class="form-group m-0 p-0 row">
            <div class="offset-sm-3 col-sm-9">
                <button type="submit" class="btn btn-primary btnPass">Perbarui</button>
            </div>
        </div>
    </form>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#formUpdatePassword").on("submit", function (e) {
                e.preventDefault();

                $.ajax({
                    type: $(this).attr('method'),
                    url: $(this).attr('action'),
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    beforeSend: function() {
                        $('.btnPass').attr('disabled', true);
                        $(".btnPass").html("<i class='fas fa-spin fa-spinner'></i>");
                        $(document).find('span.error-text').text('');
                        $(document).find('.form-control').removeClass('is-invalid');
                    },
                    complete: function() {
                        $('.btnPass').attr('disabled', false);
                        $(".btnPass").html("Perbarui");
                    },
                    success: function(res) {
                        if (res.status == 400) {
                            if (res.tipe == 'validation') {
                                $.each(res.errors, function(key, val) {
                                    $("span." + key + "_error").text(val[0]);
                                    $("#" + key).addClass('is-invalid');
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    html: res.message,
                                    allowOutsideClick: false,
                                });
                            }

                        } else {
                            if (res.tipe == 'warning') {
                                Swal.fire({
                                    icon: "warning",
                                    html: res.message,
                                    allowOutsideClick: false,
                                });
                            } else {
                                Swal.fire({
                                    icon: "success",
                                    html: res.message,
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.reload();
                                    }
                                });
                            }
                        }
                    },
                    error: function(xhr, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }); // Form update password
        });
    </script>
@endpush
