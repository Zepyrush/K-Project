@include('layouts.sub-dashboard.sidebar._brand')

<div class="sidebar">

    @include('layouts.sub-dashboard.sidebar._profile-info')

    {{--
        Function setActive(), menuOpen() is defined in app/Helpers/Helper.php
    --}}

    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
            data-accordion="false">

            <li class="nav-header">MAIN</li>
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ setActive('admin.dashboard') }}">
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
             <li class="nav-item {{ menuOpen(['manajemen.kuliah.jadwal.admin.kuliah.index', 'manajemen.kuliah.jadwal.admin.ujian.index']) }}">
                <a href="javascript:void(0)" class="nav-link {{ setActive(['manajemen.kuliah.jadwal.admin.kuliah.index', 'manajemen.kuliah.jadwal.admin.ujian.index']) }}">
                    <i class="nav-icon fa fa-calendar-alt"></i>
                    <p>
                        Jadwal
                        <i class="fas fa-angle-left right"></i>
                    </p>
                </a>

                <ul class="nav nav-treeview" style="{{ d_block(['manajemen.kuliah.jadwal.admin.kuliah.index', 'manajemen.kuliah.jadwal.admin.ujian.index']) }}">
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.jadwal.admin.kuliah.index') }}"
                            class="nav-link {{ setActive(['manajemen.kuliah.jadwal.admin.kuliah.index']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Kuliah</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('manajemen.kuliah.jadwal.admin.ujian.index') }}"
                            class="nav-link {{ setActive(['manajemen.kuliah.jadwal.admin.ujian.index']) }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Ujian</p>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('manajemen.kuliah.fakultas.index') }}"
                    class="nav-link {{ setActive('manajemen.kuliah.fakultas.index') }}">
                    <i class="nav-icon fas fa-graduation-cap"></i>
                    <p>
                        Fakultas
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('manajemen.kuliah.prodi.index') }}"
                    class="nav-link {{ setActive('manajemen.kuliah.prodi.index') }}">
                    <i class="nav-icon fas fa-book"></i>
                    <p>
                        Program Studi
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('manajemen.kuliah.matkul.index') }}"
                    class="nav-link {{ setActive('manajemen.kuliah.matkul.index') }}">
                    <i class="nav-icon fa fa-book-open"></i>
                    <p>
                        Matakuliah
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('manajemen.kuliah.kelas.index') }}"
                    class="nav-link {{ setActive('manajemen.kuliah.kelas.index') }}">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Kelas
                    </p>
                </a>
            </li>

            <li class="nav-header">KELOLA PENGGUNA</li>
            <li class="nav-item">
                <a href="{{ route('manage.users.dosen.index') }}"
                    class="nav-link {{ setActive('manage.users.dosen.index') }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>
                        Dosen
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('manage.users.mahasiswa.index') }}"
                    class="nav-link {{ setActive('manage.users.mahasiswa.index') }}">
                    <i class="nav-icon fas fa-user-graduate"></i>
                    <p>
                        Mahasiswa
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('manage.users.user.index') }}"
                    class="nav-link {{ setActive('manage.users.user.index') }}">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        Pengguna
                    </p>
                </a>
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
