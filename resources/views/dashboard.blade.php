<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laporan MBG</title>
</head>
<body>
    <h1>Dashboard Pelaporan MBG</h1>

    <p>Selamat datang, {{ auth()->user()->name }}</p>
    <p>Peran: {{ auth()->user()->role }}</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Keluar</button>
    </form>
</body>
</html>