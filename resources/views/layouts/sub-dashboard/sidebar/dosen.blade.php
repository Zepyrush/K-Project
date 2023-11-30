@include('layouts.sub-dashboard.sidebar._brand')

<div class="sidebar">

    @include('layouts.sub-dashboard.sidebar._profile-info')

    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <li class="nav-header">MAIN</li>
            <li class="nav-item">
                <a href="{{ route('dosen.dashboard') }}" class="nav-link {{ setActive('dosen.dashboard') }}">
                    <i class="nav-icon fas fa-chart-pie"></i>
                    <p>
                        Dashboard
                    </p>
                </a>
            </li>

            <li class="nav-header">AKUN</li>
            <li class="nav-item">
                <a href="{{ route('profile.index') }}" class="nav-link {{ setActive('profile.index') }}">
                    <i class="nav-icon fas fa-user"></i>
                    <p>
                        Profile
                    </p>
                </a>
            </li>

            <li class="nav-header">MANAJEMEN KULIAH</li>
            <li class="nav-item">
                <a id="kelasDosen" href="{{ route('manajemen.kuliah.jadwal.dosen.kuliah.index') }}"
                    class="nav-link kelas {{ setActive(['manajemen.kuliah.kelas.index', 'manajemen.kuliah.kelas.dosen.index', 'manajemen.kuliah.materi.dosen.index', 'manajemen.kuliah.tugas.dosen.index', 'manajemen.kuliah.tugas.dosen.show']) }}">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Kelas
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a id="ujianDosen" href="#"
                    class="nav-link {{ setActive(['manajemen.kuliah.jadwal.dosen.ujian.show', 'manajemen.kuliah.jadwal.dosen.ujian.soal.pg.create', 'manajemen.kuliah.jadwal.dosen.ujian.soal.pg.edit', 'manajemen.kuliah.jadwal.dosen.ujian.soal.essay.create', 'manajemen.kuliah.jadwal.dosen.ujian.soal.essay.edit']) }}">
                    <i class="nav-icon fa fa-file-alt"></i>
                    <p>
                        Ujian
                    </p>
                </a>
            </li>

            <li
                class="nav-item {{ menuOpen(['manajemen.kuliah.jadwal.dosen.kuliah.index', 'manajemen.kuliah.jadwal.dosen.ujian.index']) }}">
                <a href="javascript:void(0)"
                    class="nav-link {{ setActive(['manajemen.kuliah.jadwal.dosen.kuliah.index', 'manajemen.kuliah.jadwal.dosen.ujian.index']) }}">
                    <i class="nav-icon fa fa-calendar-alt"></i>
                    <p>
                        Jadwal
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview"
                    style="{{ d_block(['manajemen.kuliah.jadwal.dosen.kuliah.index', 'manajemen.kuliah.jadwal.dosen.ujian.index']) }}">
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.jadwal.dosen.kuliah.index') }}"
                            class="nav-link {{ setActive(['manajemen.kuliah.jadwal.dosen.kuliah.index']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Kuliah</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.jadwal.dosen.ujian.index') }}"
                            class="nav-link {{ setActive(['manajemen.kuliah.jadwal.dosen.ujian.index']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Ujian</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('manajemen.kuliah.absen.dosen.index') }}"
                    class="nav-link {{ setActive('manajemen.kuliah.absen.dosen.index') }}">
                    <i class="nav-icon fa fa-calendar-check"></i>
                    <p>
                        Absensi Hari Ini
                    </p>
                </a>
            </li>

            <li class="nav-item {{ menuOpen(['manajemen.kuliah.laporan.dosen.absen', 'manajemen.kuliah.laporan.dosen.nilai.tugas', 'manajemen.kuliah.laporan.dosen.nilai.ujian', 'manajemen.kuliah.laporan.dosen.nilai']) }}">
                <a href="javasript:void(0)"
                    class="nav-link {{ setActive(['manajemen.kuliah.laporan.dosen.absen', 'manajemen.kuliah.laporan.dosen.nilai.tugas', 'manajemen.kuliah.laporan.dosen.nilai.ujian', 'manajemen.kuliah.laporan.dosen.nilai']) }}">
                    <i class="nav-icon fas fa-chart-line"></i>
                    <p>
                        Laporan
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview"
                    style="{{ d_block(['manajemen.kuliah.laporan.dosen.absen', 'manajemen.kuliah.laporan.dosen.nilai.tugas', 'manajemen.kuliah.laporan.dosen.nilai.ujian', 'manajemen.kuliah.laporan.dosen.nilai']) }}">
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.laporan.dosen.absen') }}"
                            class="nav-link {{ setActive(['manajemen.kuliah.laporan.dosen.absen']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Absensi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.laporan.dosen.nilai') }}" class="nav-link {{ setActive(['manajemen.kuliah.laporan.dosen.nilai']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>
                                Nilai <small class="text-muted">(Tugas & Ujian)</small>
                            </p>
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.laporan.dosen.nilai.tugas') }}"
                            class="nav-link {{ setActive(['manajemen.kuliah.laporan.dosen.nilai.tugas']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Nilai Tugas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.laporan.dosen.nilai.ujian') }}" class="nav-link {{ setActive(['manajemen.kuliah.laporan.dosen.nilai.ujian']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Nilai Ujian</p>
                        </a>
                    </li> --}}

                </ul>
            </li>

            <li class="nav-item mb-2 mt-1">
                <a href="javascript:void(0)" class="nav-link btn-danger text-white" id="logoutButton">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <p class="font-weight-bold">LOGOUT</p>
                </a>
            </li>

        </ul>
    </nav>

</div>

@push('js')
    <script>
        let ujianDosen = $("#ujianDosen"),
            kelasDosen = $("#kelasDosen");

        if (ujianDosen.hasClass('active')) {
            ujianDosen.removeClass("d-none")
                .removeAttr('href')
                .attr('href', 'javascript:void(0)');
        } else {
            ujianDosen.addClass("d-none");
        }

        if (kelasDosen.hasClass('active')) {
            kelasDosen.removeClass("d-none")
                .removeAttr('href')
                .attr('href', 'javascript:void(0)');
        } else {
            kelasDosen.addClass("d-none");
        }
    </script>
@endpush
