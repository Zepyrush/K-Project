@include('layouts.sub-dashboard.sidebar._brand')

<div class="sidebar">

    @include('layouts.sub-dashboard.sidebar._profile-info')

    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

            <li class="nav-header">MAIN</li>
            <li class="nav-item">
                <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link {{ setActive('mahasiswa.dashboard') }}">
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
                <a id="kelasMahasiswa" href="javascript:void(0)"
                    class="nav-link kelas {{ setActive(['manajemen.kuliah.kelas.mahasiswa.index', 'manajemen.kuliah.kelas.mahasiswa.materi', 'manajemen.kuliah.kelas.mahasiswa.tugas']) }}">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Kelas
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a id="ujianMahasiswa" href="#" class="nav-link {{ setActive(['manajemen.kuliah.ujian.mahasiswa.pg.ujian', 'manajemen.kuliah.ujian.mahasiswa.essay.ujian']) }}">
                    <i class="nav-icon fa fa-file-alt"></i>
                    <p>
                        Ujian
                    </p>
                </a>
            </li>


            <li class="nav-item {{ menuOpen(['manajemen.kuliah.jadwal.mahasiswa.index', 'manajemen.kuliah.ujian.mahasiswa.index']) }}">
                <a href="javascript:void(0)"
                    class="nav-link {{ setActive('manajemen.kuliah.jadwal.mahasiswa.index', 'manajemen.kuliah.ujian.mahasiswa.index') }}">
                    <i class="nav-icon fa fa-calendar-alt"></i>
                    <p>
                        Jadwal
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview"
                    style="{{ d_block(['manajemen.kuliah.jadwal.mahasiswa.index', 'manajemen.kuliah.ujian.mahasiswa.index']) }}">
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.jadwal.mahasiswa.index') }}"
                            class="nav-link {{ setActive(['manajemen.kuliah.jadwal.mahasiswa.index']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Kuliah</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.ujian.mahasiswa.index') }}"
                            class="nav-link {{ setActive('manajemen.kuliah.ujian.mahasiswa.index') }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Ujian</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('manajemen.kuliah.ujian.mahasiswa.riwayatUjian') }}" class="nav-link {{ setActive(['manajemen.kuliah.ujian.mahasiswa.riwayatUjian']) }}">
                    <i class="nav-icon fa fa-history"></i>
                    <p>
                        Riwayat Ujian
                    </p>
                </a>
            </li>

            <li class="nav-item mb-2 mt-1">
                <a href="#" class="nav-link btn-danger text-white" id="logoutButton">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <p class="font-weight-bold">LOGOUT</p>
                </a>
            </li>

        </ul>
    </nav>

</div>

@push('js')
    <script>
        $(document).ready(function() {
            let ujianMhs = $("#ujianMahasiswa"),
                kelasMhs = $("#kelasMahasiswa");

            if (ujianMhs.hasClass('active')) {
                ujianMhs.removeClass("d-none");
                ujianMhs.removeAttr('href');
                ujianMhs.attr('href', 'javascript:void(0)');
            } else {
                ujianMhs.addClass("d-none");
            }

            if (kelasMhs.hasClass('active')) {
                kelasMhs.removeClass("d-none");

                kelasMhs.removeAttr('href');
                kelasMhs.attr('href', 'javascript:void(0)');
            } else {
                kelasMhs.addClass("d-none");

                // ALERT JIKA BELUM MEMILIH KELAS
                // kelasMhs.click(function() {
                //     Swal.fire({
                //         icon: 'warning',
                //         html: 'Untuk mengakses menu kelas, silahkan pilih kelas terlebih dahulu di menu <b>Jadwal Kuliah</b>.',
                //         allowOutsideClick: false,
                //         confirmButtonText: 'Oke, paham!',
                //     }).then((result) => {
                //         if (result.isConfirmed) {
                //             window.location.href =
                //                 "{{ route('manajemen.kuliah.jadwal.mahasiswa.index') }}";
                //         }
                //     });
                // });
            }
        });
    </script>
@endpush
