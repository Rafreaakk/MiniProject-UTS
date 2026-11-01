<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Laporan</title>
</head>
<body>
    <h1>Kirim Laporan Kejadian</h1>

    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label for="school_id">Sekolah</label>
        <select id="school_id" name="school_id" required>
            <option value="">Pilih sekolah</option>
            @foreach ($schools as $school)
                <option
                    value="{{ $school->id }}"
                    @selected(old('school_id') == $school->id)
                >
                    {{ $school->name }}
                </option>
            @endforeach
        </select>
        @error('school_id')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <label for="distribution_id">Distribusi makanan</label>
        <select id="distribution_id" name="distribution_id" required disabled>
            <option value="">Pilih sekolah terlebih dahulu</option>
        </select>
        @error('distribution_id')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <label for="incident_at">Waktu kejadian</label>
        <input
            id="incident_at"
            type="datetime-local"
            name="incident_at"
            value="{{ old('incident_at') }}"
            required
        >
        @error('incident_at')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <label for="description">Keterangan kejadian</label>
        <textarea
            id="description"
            name="description"
            maxlength="5000"
            required
        >{{ old('description') }}</textarea>
        @error('description')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <label for="people_affected">Jumlah orang yang mengalami keluhan</label>
        <input
            id="people_affected"
            type="number"
            name="people_affected"
            min="1"
            max="65535"
            value="{{ old('people_affected', 1) }}"
            required
        >
        @error('people_affected')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <label for="evidence">Bukti pendukung (opsional, maksimal 2 MB)</label>
        <input
            id="evidence"
            type="file"
            name="evidence"
            accept=".jpg,.jpeg,.png,.pdf"
        >
        @error('evidence')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <button type="submit">Kirim laporan</button>
    </form>

    <script>
        const schoolSelect = document.getElementById('school_id');
        const distributionSelect = document.getElementById('distribution_id');
        const previousDistribution = @json(old('distribution_id'));

        async function loadDistributions(schoolId, selectedId = '') {
            distributionSelect.disabled = true;
            distributionSelect.innerHTML = '<option value="">Memuat distribusi...</option>';

            if (!schoolId) {
                distributionSelect.innerHTML =
                    '<option value="">Pilih sekolah terlebih dahulu</option>';
                return;
            }

            try {
                const response = await fetch(
                    `/schools/${encodeURIComponent(schoolId)}/distributions`,
                    { headers: { 'Accept': 'application/json' } }
                );

                if (!response.ok) {
                    throw new Error('Permintaan distribusi gagal.');
                }

                const distributions = await response.json();
                distributionSelect.innerHTML =
                    '<option value="">Pilih distribusi</option>';

                for (const distribution of distributions) {
                    const option = document.createElement('option');
                    option.value = distribution.id;
                    option.textContent =
                        `${distribution.distribution_date} - ` +
                        (distribution.meal_description || 'Menu tidak dicantumkan');
                    option.selected =
                        String(distribution.id) === String(selectedId);

                    distributionSelect.appendChild(option);
                }

                if (distributions.length === 0) {
                    distributionSelect.innerHTML =
                        '<option value="">Belum ada distribusi untuk sekolah ini</option>';
                } else {
                    distributionSelect.disabled = false;
                }
            } catch (error) {
                distributionSelect.innerHTML =
                    '<option value="">Gagal memuat distribusi</option>';
            }
        }

        schoolSelect.addEventListener('change', () => {
            loadDistributions(schoolSelect.value);
        });

        if (schoolSelect.value) {
            loadDistributions(schoolSelect.value, previousDistribution);
        }
    </script>
</body>
</html>