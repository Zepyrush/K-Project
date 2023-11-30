@if ($absens->count() > 0)
    <div class="card card-primary card-outline">
        <div class="card-header p-2">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="m-0 p-0 font-weight-bold ml-2">
                    <i class="fas fa-calendar-check text-primary mr-2"></i> Absensi Hari ini
                </h5>

                <a href="{{ route('manajemen.kuliah.absen.dosen.index') }}"
                    class="btn btn-sm btn-primary" data-toggle="tooltip" title="Pergi ke halaman absensi">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <div style="overflow: auto;max-height: 360px;">
                <table class="table table-hover">
                    <thead style="position: sticky; top:0;">
                        <tr style="background: #fff; box-shadow: 1px 7px 11px -11px #000">
                            <th>Matakuliah</th>
                            <th>Kelas</th>
                            <th>Pertemuan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($absens as $absen)
                            @php
                                $jadwal = $absen->jadwal;

                                if (jam_sekarang() >= $jadwal->started_at && jam_sekarang() <= $jadwal->ended_at && $jadwal->hari == hari_ini()) {
                                    $output =
                                        '<a href="' .
                                        route('manajemen.kuliah.kelas.dosen.index', encrypt($absen->jadwal_id)) .
                                        '"
                                        class="btn btn-sm btn-primary show_btn" data-toggle="tooltip"
                                        title="Lihat Kelas" data-placement="left">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>';
                                } else {
                                    $output = '<a href="javascript:void(0)"
                                        class="btn btn-sm btn-secondary cursor_default" data-toggle="tooltip"
                                        title="Kelas ini sudah selesai." data-placement="left">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>';
                                }
                            @endphp

                            <tr>
                                <td>{{ $jadwal->matkul->nama }}</td>
                                <td>{{ $jadwal->kelas->kode }}</td>
                                <td>{{ $absen->pertemuan }}</td>
                                <td>
                                    <?php echo $output; ?>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card card-primary card-outline mb-2">
        <div class="card-header">
            <h5 class="m-0 p-0 font-weight-bold">
                <i class="fas fa-calendar-check text-primary mr-2"></i> Absensi Hari ini
            </h5>
        </div>
    </div>

    <div class="alert alert-primary">
        <i class="fas fa-bell mr-2"></i>
        Tidak ada absensi hari ini.
    </div>
@endif
