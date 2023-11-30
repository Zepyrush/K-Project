<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-cyan card-outline">

        <div class="inner">
            <h3>{{ $fakultas }}</h3>

            <p class=" mb-2">Fakultas</p>
        </div>

        <div class="icon">
            <i class="fas fa-graduation-cap text-cyan"></i>
        </div>

        <a href="{{ route('manajemen.kuliah.fakultas.index') }}" class="small-box-footer"> Selengkapnya
            <i class="fas fa-arrow-circle-right text-cyan"></i>
        </a>
    </div>
</div> {{-- END FAKULTAS --}}

<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-indigo card-outline">
        <div class="inner">
            <h3>{{ $prodi }}</h3>

            <p class=" mb-2">Program Studi</p>
        </div>

        <div class="icon">
            <i class="fas fa-book text-indigo"></i>
        </div>

        <a href="{{ route('manajemen.kuliah.prodi.index') }}" class="small-box-footer"> Selengkapnya
            <i class="fas fa-arrow-circle-right text-indigo"></i>
        </a>
    </div>
</div> {{-- END PRODI --}}

<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-purple card-outline">
        <div class="inner">
            <h3>{{ $matkul }}</h3>

            <p class=" mb-2">Matakuliah</p>
        </div>

        <div class="icon">
            <i class="fas fa-book-open text-purple"></i>
        </div>

        <a href="{{ route('manajemen.kuliah.matkul.index') }}" class="small-box-footer"> Selengkapnya
            <i class="fas fa-arrow-circle-right text-purple"></i>
        </a>
    </div>
</div> {{-- END MATAKULIAH --}}

<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-pink card-outline">
        <div class="inner">
            <h3>{{ $kelas }}</h3>

            <p class=" mb-2">Kelas</p>
        </div>

        <div class="icon">
            <i class="fas fa-chalkboard-teacher text-pink"></i>
        </div>

        <a href="{{ route('manajemen.kuliah.kelas.index') }}" class="small-box-footer"> Selengkapnya
            <i class="fas fa-arrow-circle-right text-pink"></i>
        </a>
    </div>
</div> {{-- END KELAS --}}

<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-red card-outline">
        <div class="inner">
            <h3>{{ $kuliah }}</h3>

            <p class=" mb-2">Jadwal Kuliah</p>
        </div>

        <div class="icon">
            <i class="fas fa-calendar text-red"></i>
        </div>

        <a href="{{ route('manajemen.kuliah.jadwal.admin.kuliah.index') }}" class="small-box-footer">
            Selengkapnya
            <i class="fas fa-arrow-circle-right text-red"></i>
        </a>
    </div>
</div> {{-- END JADWAL KULIAH --}}

<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-orange card-outline">
        <div class="inner">
            <h3>{{ $ujian }}</h3>

            <p class=" mb-2">Jadwal Ujian</p>
        </div>

        <div class="icon">
            <i class="fas fa-calendar-alt text-orange"></i>
        </div>

        <a href="{{ route('manajemen.kuliah.jadwal.admin.ujian.index') }}" class="small-box-footer">
            Selengkapnya
            <i class="fas fa-arrow-circle-right text-orange"></i>
        </a>
    </div>
</div> {{-- END JADWAL UJIAN --}}

<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-yellow card-outline">
        <div class="inner">
            <h3>{{ $dosen }}</h3>

            <p class=" mb-2">Dosen</p>
        </div>

        <div class="icon">
            <i class="fas fa-user-tie text-yellow"></i>
        </div>

        <a href="{{ route('manage.users.dosen.index') }}" class="small-box-footer"> Selengkapnya
            <i class="fas fa-arrow-circle-right text-yellow"></i>
        </a>
    </div>
</div> {{-- END USER DOSEN --}}

<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-green card-outline">
        <div class="inner">
            <h3>{{ $mahasiswa }}</h3>

            <p class=" mb-2">Mahasiswa</p>
        </div>

        <div class="icon">
            <i class="fas fa-user-graduate text-green"></i>
        </div>

        <a href="{{ route('manage.users.mahasiswa.index') }}" class="small-box-footer"> Selengkapnya
            <i class="fas fa-arrow-circle-right text-green"></i>
        </a>
    </div>
</div> {{-- END USER MAHASISWA --}}

<div class="col-lg-3 col-6">
    <div class="small-box bg-white card-teal card-outline">
        <div class="inner">
            <h3>{{ $users->count() }}</h3>

            <p class=" mb-2">Pengguna</p>
        </div>

        <div class="icon">
            <i class="fas fa-users text-teal"></i>
        </div>

        <a href="{{ route('manage.users.user.index') }}" class="small-box-footer"> Selengkapnya
            <i class="fas fa-arrow-circle-right text-teal"></i>
        </a>
    </div>
</div> {{-- END USERS --}}



