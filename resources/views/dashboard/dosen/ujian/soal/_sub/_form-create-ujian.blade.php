<div class="card card-primary card-outline">
    <div class="card-header">
        <h5 class="font-weight-bold m-0 p-0">
            Informasi Ujian
        </h5>
    </div>
    <div class="card-body p-3">
        <div class="form-group">
            <label for="judul">Judul</label>
            <input required type="text" name="judul" id="judul" class="form-control"
                placeholder="Masukkan judul ujian.">
        </div>
        {{-- <div class="form-group">
            <label for="semester">Semester
                <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                    title="Semester akan bertambah otomatis, jika didalam semester tipe ujiannya sudah digunakan
                    semua (UTS & UAS)">
                </i>
            </label>
            <input type="text" class="form-control" name="semester" id="semester"
                value="{{ $semester }}" readonly>
        </div> --}}
        <div class="form-group">
            <label for="durasi">
                Durasi
                <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                    title="Durasi akan terisi otomatis jika ada jam selesainya dibagian jadwal ujian.">
                </i>
            </label>
            <div class="input-group" id="durasi">
                <div class="input-group-prepend">
                    <div class="input-group-text"><i class="fa fa-clock"></i></div>
                </div>

                <input required id="durasi"
                    @if ($duration != 0) value="{{ $duration }}" readonly @endif
                    type="number" name="durasi" class="form-control"
                    placeholder="Masukkan durasi ujian. (Per Menit)" />

                <div class="input-group-append">
                    <div class="input-group-text">Menit</div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea required name="deskripsi" id="deskripsi" class="form-control" rows="5"
                placeholder="Masukkan deskripsi ujian."></textarea>
        </div>
        <div class="form-group">
            <label for="tipe_ujian">
                Tipe Ujian
                <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                    title="Tipe ujian akan menyesuaikan pilihan jika didalam semester ada yang digunakan.
                    Contoh: didalam semester ini sudah ada UTS, maka yang muncul adalah UAS dan sebaliknya, Jika tidak ada
                    maka akan muncul dua2nya.">
                </i>
            </label>
            <select required name="tipe_ujian" id="tipe_ujian" class="form-control">
                <option value="">-- Silahkan Pilih --</option>
                @foreach ($tipe_ujian as $item)
                    <option value="{{ $item }}">{{ strtoupper($item) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="random_soal">Random Soal</label>
            <select required name="random_soal" id="random_soal" class="form-control">
                <option value="">-- Silahkan Pilih --</option>
                <option value="1">Ya</option>
                <option value="0">Tidak</option>
            </select>
        </div>
        <div class="form-group">
            <label for="lihat_hasil">Lihat Hasil
                <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                    title="Maksud lihat hasil adalah ketika mahasiswa sudah selesai ujian, si mahasiswa itu bisa lihat hasil ujiannya.">
                </i>
            </label>
            <select required name="lihat_hasil" id="lihat_hasil" class="form-control">
                <option value="">-- Silahkan Pilih --</option>
                <option value="1">Ya</option>
                <option value="0">Tidak</option>
            </select>
        </div>
    </div> {{-- card-body --}}
</div> {{-- card --}}
