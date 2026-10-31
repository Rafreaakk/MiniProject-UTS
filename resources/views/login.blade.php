<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Laporan MBG</title>
</head>
<body>
    <h1>Masuk</h1>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <label for="email">Email</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
        >
        @error('email')
            <p>{{ $message }}</p>
        @enderror

        <label for="password">Kata sandi</label>
        <input
            id="password"
            type="password"
            name="password"
            required
        >
        @error('password')
            <p>{{ $message }}</p>
        @enderror

        <button type="submit">Masuk</button>
    </form>
</body>
</html>