<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Laporan MBG</title>
</head>
<body>
    <h1>Rekap Laporan MBG</h1>

    <p>Total laporan: {{ $totalReports }}</p>
    <p>Total orang terdampak: {{ $totalPeopleAffected }}</p>

    <h2>Jumlah laporan berdasarkan status</h2>

    <ul>
        <li>Dikirim: {{ $reportsByStatus->get('dikirim', 0) }}</li>
        <li>Sedang ditinjau: {{ $reportsByStatus->get('sedang_ditinjau', 0) }}</li>
        <li>Perlu perbaikan: {{ $reportsByStatus->get('perlu_perbaikan', 0) }}</li>
        <li>Diteruskan: {{ $reportsByStatus->get('diteruskan', 0) }}</li>
        <li>Selesai: {{ $reportsByStatus->get('selesai', 0) }}</li>
    </ul>

    <p><a href="{{ route('reports.index') }}">Kembali ke daftar laporan</a></p>
</body>
</html>