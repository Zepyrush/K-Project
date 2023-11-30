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
                placeholder="Masukkan judul ujian." value="{{ $ujian->judul }}">
        </div>
        {{-- <div class="form-group">
            <label for="semester">
                Semester
                <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                    title="Semester akan bertambah otomatis, jika didalam semester tipe ujiannya sudah digunakan
                    semua (UTS & UAS)">
                </i>
            </label>
            <input type="text" class="form-control" name="semester" id="semester"
                value="{{ $ujian->semester }}" readonly>
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

                <input required id="durasi"
                    @if ($duration != 0) value="{{ $duration }}" readonly @else value="{{ $ujian->durasi_ujian }}" @endif
                    type="number" name="durasi" class="form-control"
                    placeholder="Masukkan durasi ujian." />

                <div class="input-group-append">
                    <div class="input-group-text">Menit</div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea required name="deskripsi" id="deskripsi" class="form-control" rows="5"
                placeholder="Masukkan deskripsi ujian.">{{ $ujian->deskripsi }}</textarea>
        </div>
        <div class="form-group">
            <label for="tipe_ujian">
                Tipe Ujian
                <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                    title="Tipe ujian akan ditukar (UTS <-> UAS) ke ujian yang sama, jika kamu memperbaruinya.">
                </i>
            </label>
            <select required name="tipe_ujian" id="tipe_ujian" class="form-control">
                <option value="">-- Silahkan Pilih --</option>
                <option value="uts" {{ $ujian->tipe_ujian == 'uts' ? 'selected' : '' }}>UTS</option>
                <option value="uas" {{ $ujian->tipe_ujian == 'uas' ? 'selected' : '' }}>UAS</option>
            </select>
        </div>
        <div class="form-group">
            <label for="random_soal">Random Soal</label>
            <select required name="random_soal" id="random_soal" class="form-control">
                <option value="">-- Silahkan Pilih --</option>
                <option value="1" {{ $ujian->random_soal == '1' ? 'selected' : '' }}>Ya</option>
                <option value="0" {{ $ujian->random_soal == '0' ? 'selected' : '' }}>Tidak
                </option>
            </select>
        </div>
        <div class="form-group">
            <label for="lihat_hasil">Lihat Hasil
                <i class="fas fa-info-circle text-primary ml-1" data-toggle="tooltip"
                    title="Maksud lihat hasil adalah ketika mahasiswa sudah selesai ujiannya, si mahasiswa itu bisa lihat hasilnya.">
                </i>
            </label>
            <select required name="lihat_hasil" id="lihat_hasil" class="form-control">
                <option value="">-- Silahkan Pilih --</option>
                <option value="1" {{ $ujian->lihat_hasil == '1' ? 'selected' : '' }}>Ya</option>
                <option value="0" {{ $ujian->lihat_hasil == '0' ? 'selected' : '' }}>Tidak
                </option>
            </select>
        </div>

    </div> {{-- end card-body --}}

</div> {{-- end card --}}

@if ($jadwal->dosen_can_manage == 1)
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h6 class="font-weight-bold p-0 m-0 text-uppercase">
                Kelola Jadwal Ujian
            </h6>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="tanggal_ujian">Tanggal Ujian</label>
                <input required type="date" name="tanggal_ujian" id="tanggal_ujian"
                    class="form-control dated"
                    value="{{ date('Y-m-d', strtotime($jadwal->tanggal_ujian)) }}">
            </div>
            <div class="form-group">
                <label class="d-flex align-items-center" for="started">
                    Dimulai Jam
                </label>
                <input required type="time" name="started_at" id="started"
                    class="form-control timepicker" value="{{ $jadwal->started_at }}">
            </div>
            <div class="form-group">
                <label class="d-flex align-items-center" for="ended">
                    Selesai Jam
                    <small class="text-primary font-weight-bold ml-1">(Boleh kosong)</small>
                </label>
                <input type="time" name="ended_at" id="ended" class="form-control timepicker"
                    value="{{ $jadwal->ended_at }}">
            </div>
            <div class="form-group">
                <label for="status_ujian">
                    Status Ujian
                </label>
                <select required name="status_ujian" id="status_ujian" class="form-control">
                    <option value="">-- Silahkan Pilih --</option>
                    <option value="aktif" {{ $jadwal->status_ujian == 'aktif' ? 'selected' : '' }}>
                        Aktif</option>
                    <option value="draft" {{ $jadwal->status_ujian == 'draft' ? 'selected' : '' }}>
                        Draft</option>
                </select>
            </div>
        </div>
    </div>
@endif
