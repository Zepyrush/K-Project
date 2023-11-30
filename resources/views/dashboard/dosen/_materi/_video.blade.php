<div class="tab-pane fade show" id="vidio">
    @if ($materiVideos->count() > 0)
        <div class="row">
            @foreach ($materiVideos as $materi)
                <div class="col-lg-4 col-12">
                    <div class="card card-primary card-outline materi">
                        <div class="card-header d-flex align-items-center p-2">
                            <h5 class="m-0 font-weight-bold ml-2">{{ $materi->judul }}</h5>
                            {{-- dropdown --}}
                            <div class="dropdown ml-auto dropleft mr-2">
                                <button class="btn btn-sm dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <button class="dropdown-item edit_btn"
                                        value="{{ encrypt($materi->id) }}">
                                        <i class="fas fa-pen mr-1 text-warning"></i>
                                        Edit
                                    </button>
                                    <button class="dropdown-item del_btn"
                                        value="{{ encrypt($materi->id) }}"
                                        data-judul="{{ $materi->judul }}"
                                        data-tipe="{{ $materi->tipe }}">
                                        <i class="fas fa-trash mr-1 text-danger"></i>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            {{-- iframe --}}
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item"
                                    src="https://www.youtube.com/embed/{{ $materi->file_or_link }}"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                        <div class="card-footer bg-white"
                            style="padding: 0.75rem 0.75rem !important; border-radius: 0 0 8px 8px;">
                            <div class="mt-1 text-muted d-flex justify-content-between">
                                <small>Pertemuan {{ $materi->pertemuan }}</small>
                                <small>
                                    {{ Carbon\Carbon::parse($materi->created_at)->translatedFormat('d F Y - H:i') . " WIB" }}</small>
                            </div>

                            <div class="divider2"></div>

                            <p class="p-0 m-0">
                                {{ $materi->deskripsi }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center text-uppercase font-weight-bold">
            <i class="fas fa-info-circle mr-1"></i> Tidak ada vidio pembelajaran
        </div>
    @endif
</div>
