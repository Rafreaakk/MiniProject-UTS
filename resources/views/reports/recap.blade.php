<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Laporan MBG</title>
</head>
<body>
    <h1>Rekap Laporan MBG</h1>

    <form method="GET" action="{{ route('reports.recap') }}">
        <label for="school_id">Filter sekolah:</label>
        <select id="school_id" name="school_id">
            <option value="">Semua sekolah</option>
            @foreach ($schools as $school)
                <option
                    value="{{ $school->id }}"
                    @selected((string) $schoolId === (string) $school->id)
                >
                    {{ $school->name }}
                </option>
            @endforeach
        </select>
        <button type="submit">Terapkan filter</button>
    </form>

    <h2>Ringkasan</h2>
    <ul>
        <li>Total laporan: {{ $totalReports }}</li>
        <li>Laporan masih diproses: {{ $reportsInProcess }}</li>
        <li>Laporan selesai: {{ $reportsCompleted }}</li>
    </ul>

    <h2>Rekap berdasarkan sekolah</h2>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Sekolah</th>
                <th>Jumlah laporan</th>
                <th>Jumlah orang terdampak</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schoolRecap as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->total_reports }}</td>
                    <td>{{ $row->total_people_affected }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">Belum ada laporan untuk filter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p><a href="{{ route('reports.index') }}">Kembali ke daftar laporan</a></p>
</body>
</html>