<table>
    <thead>
        <tr>
            <th rowspan="2">#</th>
            <th rowspan="2">Nama</th>
            <th rowspan="2">NIM</th>
            <th rowspan="2">Kelas</th>
            <th rowspan="2">Matkul</th>
            <th rowspan="2">Fakultas</th>
            <th colspan="14" class="text-center">Tugas</th>
            <th rowspan="2" class="text-center">Rata-rata</th>
            <th rowspan="2" class="text-center">UTS</th>
            <th rowspan="2" class="text-center">UAS</th>
            <th rowspan="2" class="text-center">Total</th>
        </tr>
        <tr>
            @for ($i = 1; $i <= 14; $i++)
                <th>
                    P{{ $i }}
                </th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach ($mahasiswas as $mahasiswa)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $mahasiswa['nama'] }}</td>
                <td>{{ $mahasiswa['nim'] }}</td>
                <td>{{ $mahasiswa['kelas'] }}</td>
                <td>{{ $mahasiswa['matkul'] }}</td>
                <td>{{ $mahasiswa['fakultas'] }}</td>
                @for($i = 1; $i <= 14; $i++)
                    <td>{{ $mahasiswa["p$i"] }}</td>
                @endfor
                <td>{{ $mahasiswa['rata_rata'] }}</td>
                <td>{{ $mahasiswa['nilai_uts'] }}</td>
                <td>{{ $mahasiswa['nilai_uas'] }}</td>
                <td>{{ $mahasiswa['total'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
