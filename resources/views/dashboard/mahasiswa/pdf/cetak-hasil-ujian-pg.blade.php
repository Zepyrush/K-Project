@extends('layouts.cetak-hasil')

@section('title', 'Bukti Hasil Ujian ' . $jadwal->matkul->nama)

@section('content')
    <div class="py-3" style="background-color: #1F7A8E">
        <center>
            <img src="{{ asset('assets/image/logo.png') }}" alt="logo">

            <h2 class="m-0 p-0 font-weight-bold text-white mt-1">
                {{ config('app.name') }}
            </h2>

            <h6 class="p-0 m-0 text-white">
                Learning Management System
            </h6>
        </center>
    </div>

    <center class="mt-4 mb-4">
        <p>Terima kasih Telah Mengikuti Ujian Online</p>
    </center>

    <table align="center">
        @php
            $start = Carbon\Carbon::parse($mahasiswa->started_at); // waktu mulai
            $end = Carbon\Carbon::parse($mahasiswa->ended_at); // waktu selesai
            $diff = $end->diffInSeconds($start); // waktu selesai dikurangi waktu mulai (dalam detik)
            $hours = floor($diff / 3600); // mengubah waktu ke jam
            $minutes = floor(($diff / 60) % 60); // mengubah waktu ke menit
            $seconds = $diff % 60; // mengubah waktu ke detik

            $jadwal->ended_at == null ? ($ended = '') : ($ended = ' - ' . Carbon\Carbon::parse($jadwal->ended_at ?? ' ')->format('H:i:s'));

            $adaJwbanNull = $mahasiswa->ujianHasil->where('jawaban', null)->count() > 0;
        @endphp

        <tr class="satu">
            <td width="120">Nama</td>
            <td width="300">: {{ $mahasiswa->mahasiswa->nama }}</td>
        </tr>

        <tr>
            <td>NIM</td>
            <td>: {{ $mahasiswa->mahasiswa->nim }}</td>
        </tr>

        <tr class="satu">
            <td>Tanggal Ujian</td>
            <td>: {{ Carbon\Carbon::parse($jadwal->tanggal_ujian)->isoFormat('dddd, D MMMM Y') }}
            </td>
        </tr>

        <tr>
            <td>Waktu Ujian</td>
            <td>: {{ Carbon\Carbon::parse($jadwal->started_at)->format('H:i:s') }} {{ $ended }} WIB</td>
        </tr>

        <tr class="satu">
            <td>Mulai Ujian</td>
            <td>: {{ Carbon\Carbon::parse($mahasiswa->started_at)->format('H:i:s') }} -
                {{ Carbon\Carbon::parse($mahasiswa->ended_at)->format('H:i:s') }} WIB</td>
        </tr>

        <tr>
            <td>Lama Mengerjakan</td>
            <td>:
                {{ $hours }} Jam {{ $minutes }} Menit {{ $seconds }} Detik
            </td>
        </tr>

        <tr class="satu">
            <td>Matakuliah</td>
            <td>: {{ $jadwal->matkul->nama }} - {{ $jadwal->matkul->kode }}</td>
        </tr>

        <tr>
            <td>Semester</td>
            <td>: {{ $jadwal->ujian->semester }}</td>
        </tr>

        <tr class="satu">
            <td>Tipe Ujian</td>
            <td>:
                {{ $jadwal->ujian->tipe_soal . " (" . strtoupper($jadwal->ujian->tipe_ujian) . ")" }}
            </td>
        </tr>

        <tr>
            <td>Jumlah Soal</td>
            <td>: {{ $mahasiswa->ujianHasil->count() }} Soal</td>
        </tr>

        <tr class="satu">
            <td>Jumlah Benar</td>
            <td>: {{ $mahasiswa->ujianHasil->where('status', '1')->count() }}</td>
        </tr>

        <tr>
            <td>Jumlah Salah</td>
            <td>: {{ $mahasiswa->ujianHasil->where('status', '0')->where('jawaban', '!=', null)->count() }}</td>
        </tr>

        @if ($adaJwbanNull)
            <tr class="satu">
                <td>Tidak Jawab</td>
                <td>: {{ $mahasiswa->ujianHasil->where('jawaban', null)->count() }}</td>
            </tr>
            <tr>
                <td>Nilai Ujian</td>
                <td>: {{ $mahasiswa->nilai }}</td>
            </tr>
        @else
            <tr class="satu">
                <td>Nilai Ujian</td>
                <td>: {{ $mahasiswa->nilai }}</td>
            </tr>
        @endif

    </table>

    <center class="mt-4 mb-4">
        <p class="text-uppercase">Simpan sebagai <b>bukti</b> bahwa anda telah <b>mengikuti ujian</b></p>
    </center>


    <table align="center" class="table table-bordered cetak_hasil">
        <thead>
            <tr class="satu">
                <th>No</th>
                <th>Pertanyaan</th>
                <th>Kunci Jawaban</th>
                <th>Jawaban Kamu</th>
                <th>Status</th>
                <th>Ragu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ujianHasil as $ujian)
                @php
                    $soal = $ujian->soalUjianPg;
                    $jawaban = $soal->jawaban_benar;
                    $jawabanMhs = $ujian->jawaban;
                    $jawabanMhs == null ? ($jawabanMhs = '-') : ($jawabanMhs = $jawabanMhs . '. ' . $soal['pilihan_' . $jawabanMhs]);
                @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="break">{!! $soal->pertanyaan !!}</td>
                    <td>{!! $jawaban . '. ' . $soal['pilihan_' . $jawaban] !!}</td>
                    <td>{!! $jawabanMhs !!}</td>
                    <td>
                        @if ($ujian->status == 0)
                            @if ($ujian->jawaban == null)
                                <span class="badge badge-secondary">Tidak Jawab</span>
                            @else
                                <span class="badge badge-danger">Salah</span>
                            @endif
                        @else
                            <span class="badge badge-success">Benar</span>
                        @endif
                    </td>
                    <td>
                        @if ($ujian->ragu == 1)
                            <span class="badge badge-warning">Ya</span>
                        @else
                            <span class="badge badge-secondary">Tidak</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
