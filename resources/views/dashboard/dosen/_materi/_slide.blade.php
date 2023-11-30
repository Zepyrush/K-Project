<div class="tab-pane fade show" id="slide">
    @if ($materiSlides->count() > 0)
        <div class="row">
            @foreach ($materiSlides as $slide)
                <div class="col-lg-4 col-12">
                    <div class="card card-primary card-outline materi">
                        <div class="card-body text-center d-flex justify-content-center p-5">

                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-icon-slide">
                                    <i class="far fa-file-archive"></i>
                                </div>

                                <p class="slide-text mb-3 mt-3">{{ $slide->judul }}</p>

                                <div class="flex-row">
                                    <a download
                                        href="{{ asset('assets/file/slide/' . $slide->file_or_link) }}"
                                        class="btn btn-primary mr-1" data-toggle="tooltip"
                                        title="Download file">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button value="{{ encrypt($slide->id) }}"
                                        class="btn btn-warning edit_slide mr-1" data-toggle="tooltip"
                                        title="Edit file">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button value="{{ encrypt($slide->id) }}"
                                        class="btn btn-danger del_btn" data-toggle="tooltip"
                                        title="Hapus file" data-judul="{{ $slide->judul }}"
                                        data-tipe="{{ $slide->tipe }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                {{-- <a href="#" download class="btn btn-primary">
                                Download
                            </a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center text-uppercase font-weight-bold">
            <i class="fas fa-info-circle mr-1"></i> Tidak ada slide pembelajaran
        </div>
    @endif
</div>
