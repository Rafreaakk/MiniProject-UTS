<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan</title>
</head>
<body>
    <h1>Daftar Laporan</h1>

    <form method="GET" action="{{ route('reports.index') }}">
        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari keterangan atau sekolah"
        >

        <select name="status">
            <option value="">Semua status</option>
            @foreach (['dikirim', 'sedang_ditinjau', 'perlu_perbaikan', 'diteruskan', 'selesai'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>
                    {{ str_replace('_', ' ', ucfirst($status)) }}
                </option>
            @endforeach
        </select>

        <button type="submit">Cari dan filter</button>
    </form>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Sekolah</th>
                <th>Tanggal distribusi</th>
                <th>Waktu kejadian</th>
                <th>Keterangan</th>
                <th>Status</th>
                <th>Bukti</th>
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $report->distribution->school->name }}</td>
                    <td>{{ $report->distribution->distribution_date }}</td>
                    <td>{{ $report->incident_at }}</td>
                    <td>{{ $report->description }}</td>
                    <td>{{ str_replace('_', ' ', $report->status) }}</td>
                    <td>
                        @if ($report->evidence_path)
                            <a href="{{ route('reports.evidence', $report) }}" target="_blank">
                                Lihat bukti
                            </a>
                        @else
                            Tidak ada
                        @endif
                    </td>
                    
                    <td>
                        @if (
                            auth()->user()->role === 'pelapor'
                            && (int) $report->reporter_id === (int) auth()->id()
                            && in_array($report->status, ['dikirim', 'perlu_perbaikan'], true)
                        )
                            <a href="{{ route('reports.edit', $report) }}">Perbaiki laporan</a>
                        @elseif (
                            auth()->user()->role === 'pemantau'
                            && in_array($report->status, ['dikirim', 'sedang_ditinjau'], true)
                        )
                            <form action="{{ route('reports.review', $report) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <select name="status" required>
                                    <option value="sedang_ditinjau">Sedang ditinjau</option>
                                    <option value="perlu_perbaikan">Perlu perbaikan</option>
                                </select>

                                <textarea name="review_note" placeholder="Catatan Pemantau"></textarea>

                                <label>
                                    <input type="checkbox" name="flagged_for_followup" value="1">
                                    Tandai untuk tindak lanjut
                                </label>

                                <button type="submit">Simpan tinjauan</button>
                            </form>
                        @elseif (
                            auth()->user()->role === 'koordinator'
                            && $report->status === 'sedang_ditinjau'
                            && $report->flagged_for_followup
                        )
                            <form action="{{ route('reports.forward', $report) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <input
                                    type="text"
                                    name="forwarded_to"
                                    placeholder="Tujuan penerusan"
                                    required
                                    maxlength="255"
                                >

                                <button type="submit">Catat penerusan</button>
                            </form> 
                        @else 
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada laporan yang cocok.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $reports->links() }}
</body>
</html>