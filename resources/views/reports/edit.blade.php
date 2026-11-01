<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perbaiki Laporan</title>
</head>
<body>
    <h1>Perbaiki Laporan</h1>

    <p>Sekolah: {{ $report->distribution->school->name }}</p>
    <p>Tanggal distribusi: {{ $report->distribution->distribution_date }}</p>

    @if ($report->review_note)
        <p>Catatan Pemantau: {{ $report->review_note }}</p>
    @endif

    <form
        action="{{ route('reports.update', $report) }}"
        method="POST"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <label for="incident_at">Waktu kejadian</label>
        <input
            id="incident_at"
            type="datetime-local"
            name="incident_at"
            value="{{ old('incident_at', $incidentAt) }}"
            required
        >
        @error('incident_at') <p>{{ $message }}</p> @enderror

        <br><br>

        <label for="description">Keterangan kejadian</label>
        <textarea
            id="description"
            name="description"
            maxlength="5000"
            required
        >{{ old('description', $report->description) }}</textarea>
        @error('description') <p>{{ $message }}</p> @enderror

        <br><br>

        <label for="people_affected">Jumlah orang yang mengalami keluhan</label>
        <input
            id="people_affected"
            type="number"
            name="people_affected"
            min="1"
            max="65535"
            value="{{ old('people_affected', $report->people_affected) }}"
            required
        >
        @error('people_affected') <p>{{ $message }}</p> @enderror

        <br><br>

        <label for="evidence">Ganti atau tambahkan bukti (opsional, maksimal 2 MB)</label>
        <input
            id="evidence"
            type="file"
            name="evidence"
            accept=".jpg,.jpeg,.png,.pdf"
        >
        @error('evidence') <p>{{ $message }}</p> @enderror

        <br><br>

        <button type="submit">Simpan perbaikan</button>
    </form>
</body>
</html>