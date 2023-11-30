<div class="modal fade bd-example-modal-lg imagecrop" id="modalUpload" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title font-weight-bold ml-2" id="exampleModalLabel">
                    <i class="fas fa-crop text-primary mr-1"></i> Memotong Foto..
                </h5>
                <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">
                        <i class="fas fa-times"></i>
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-3 d-flex justify-content-center">
                            <div class="preview"></div>
                        </div>
                        <div class="col-md-9">
                            <div class="alert bg-white card card-outline card-primary alert-dismissible fade show"
                                role="alert">
                                <div class="d-flex align-items-center justify-content-between">
                                    <i class="fas fa-info-circle mr-3 text-primary"
                                    style="font-size: 18px;">
                                    </i>

                                    <p style="line-height: 1.3;">
                                        Untuk memfokuskan objek pada foto, silahkan fokuskan(kursor) ke objek lalu scroll
                                        mouse kedepan.
                                    </p>

                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center">
                                <img class="img-fluid" id="sampleImage" src="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary crop" id="cropBtn">Potong Foto</button>
            </div>
        </div>
    </div>
</div>
