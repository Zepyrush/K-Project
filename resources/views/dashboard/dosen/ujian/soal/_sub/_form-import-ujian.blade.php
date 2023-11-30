<div class="col-lg-6">
    <div class="form-group">
        <label for="judul">Judul</label>
        <input type="text" name="judul" id="judul_import" class="form-control"
            placeholder="Masukkan judul ujian.">
        <span class="invalid-feedback d-block error-text judul_error"></span>
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
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text"><i class="fa fa-clock"></i></div>
            </div>

            <input id="durasi_import"
                @if ($duration != 0) value="{{ $duration }}" readonly @endif
                type="number" name="durasi" class="form-control"
                placeholder="Masukkan durasi ujian. (Per Menit)" />

            <div class="input-group-append">
                <div class="input-group-text">Menit</div>
            </div>
        </div>
        <span class="invalid-feedback d-block error-text durasi_error"></span>
    </div>

    <div class="form-group">
        <label for="deskripsi">Deskripsi</label>
        <textarea name="deskripsi" id="deskripsi_import" class="form-control" rows="5"
            placeholder="Masukkan deskripsi ujian."></textarea>
        <span class="invalid-feedback d-block error-text deskripsi_error"></span>
    </div>
</div>
<div class="col-lg-6">
    <div class="form-group">
        <label for="tipe_ujian">Tipe Ujian
            <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                title="Tipe ujian akan menyesuaikan pilihan jika didalam semester ada yang digunakan.
                Contoh: didalam semester ini sudah ada UTS, maka yang muncul adalah UAS dan sebaliknya, Jika tidak ada
                maka akan muncul dua2nya.">
            </i>
        </label>
        <select name="tipe_ujian" id="tipe_ujian_import" class="form-control">
            <option value="">-- Silahkan Pilih --</option>
            @foreach ($tipe_ujian as $item)
                <option value="{{ $item }}">{{ strtoupper($item) }}</option>
            @endforeach
        </select>
        <span class="invalid-feedback d-block error-text tipe_ujian_error"></span>
    </div>
    <div class="form-group">
        <label for="random_soal">Random Soal</label>
        <select name="random_soal" id="random_soal_import" class="form-control">
            <option value="" selected disabled>-- Silahkan Pilih --</option>
            <option value="1">Ya</option>
            <option value="0">Tidak</option>
        </select>
        <span class="invalid-feedback d-block error-text random_soal_error"></span>
    </div>
    <div class="form-group">
        <label for="lihat_hasil">Lihat Hasil
            <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                title="Maksud lihat hasil adalah ketika mahasiswa sudah selesai ujian, si mahasiswa itu bisa lihat hasil ujiannya.">
            </i>
        </label>
        <select name="lihat_hasil" id="lihat_hasil_import" class="form-control">
            <option value="" selected disabled>-- Silahkan Pilih --</option>
            <option value="1">Ya</option>
            <option value="0">Tidak</option>
        </select>
        <span class="invalid-feedback d-block error-text lihat_hasil_error"></span>
    </div>
    <div class="form-group">
        <label for="file">File Import</label>
        <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
            title="Jika kamu bingung untuk importnya pakai apa, bisa download template nya terlebih dahulu di pojok bawah kiri. Lalu isi sesuai dengan data yang kamu inginkan. Setelah itu kamu bisa upload file nya disini.">
        </i>
        <div class="custom-file">
            <input type="file" name="file" class="custom-file-input">
            <label class="custom-file-label" id="file_import" for="file">
                Silahkan pilih file nya.
            </label>
        </div>
        <span class="invalid-feedback d-block error-text file_error"></span>
    </div>
</div>
