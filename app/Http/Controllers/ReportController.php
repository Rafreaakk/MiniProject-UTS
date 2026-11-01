<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\School;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function create()
    {
        $schools = School::orderBy('name')->get();

        return view('reports.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'distribution_id' => ['required', 'exists:distributions,id'],
            'incident_at' => ['required', 'date', 'before_or_equal:now'],
            'description' => ['required', 'string', 'max:5000'],
            'people_affected' => ['required', 'integer', 'min:1', 'max:65535'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $distribution = Distribution::findOrFail($data['distribution_id']);
        $incidentDate = Carbon::parse($data['incident_at'])->toDateString();

        if ((int) $distribution->school_id !== (int) $data['school_id']) {
            return back()
                ->withErrors(['distribution_id' => 'Distribusi tidak sesuai dengan sekolah yang dipilih.'])
                ->withInput();
        }

        if ($distribution->distribution_date > $incidentDate) {
            return back()
                ->withErrors(['distribution_id' => 'Tanggal distribusi tidak boleh setelah tanggal kejadian.'])
                ->withInput();
        }

        $evidencePath = null;

        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')
                ->store('report-evidence', 'local');
        }

        $request->user()->reports()->create([
            'distribution_id' => $data['distribution_id'],
            'incident_at' => $data['incident_at'],
            'description' => $data['description'],
            'people_affected' => $data['people_affected'],
            'evidence_path' => $evidencePath,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Laporan berhasil dikirim.');
    }

    public function distributionsForSchool(School $school)
    {
        $distributions = $school->distributions()
            ->orderBy('distribution_date')
            ->get(['id', 'distribution_date', 'meal_description']);

        return response()->json($distributions);
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => [
                'nullable',
                'in:dikirim,sedang_ditinjau,perlu_perbaikan,diteruskan,selesai',
            ],
        ]);

        $user = $request->user();

        if (! in_array($user->role, ['pelapor', 'pemantau', 'koordinator'], true)) {
            abort(403);
        }

        $query = Report::with(['distribution.school', 'reporter'])
        ->orderByDesc('incident_at');

        if ($user->role === 'pelapor') {
            $query->where('reporter_id', $user->id);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['q'])) {
            $term = $filters['q'];

            $query->where(function ($search) use ($term) {
                $search->where('description', 'like', "%{$term}%")
                    ->orWhereHas('distribution.school', function ($school) use ($term) {
                        $school->where('name', 'like', "%{$term}%");
                    });
            });
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', compact('reports'));
    }

    public function evidence(Request $request, Report $report)
    {
        abort_unless($report->evidence_path, 404);

        $user = $request->user();

        $isOwner = (int) $report->reporter_id === (int) $user->id;
        $canReview = in_array($user->role, ['pemantau', 'koordinator'], true);

        abort_unless($isOwner || $canReview, 403);

        return \Illuminate\Support\Facades\Storage::disk('local')
            ->response($report->evidence_path);
    }

    public function recap()
    {
        $reportsByStatus = Report::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalReports = Report::count();
        $totalPeopleAffected = Report::sum('people_affected');

        return view('reports.recap', compact (
            'reportsByStatus',
            'totalReports',
            'totalPeopleAffected'
        ));
    }

    public function review(Request $request, Report $report)
    {
        $data = $request->validate([
            'status' => ['required', 'in:sedang_ditinjau,perlu_perbaikan'],
            'review_note' => [
                'nullable',
                'string',
                'max:2000',
                'required_if:status,perlu_perbaikan',
            ],
            'flagged_for_followup' => ['nullable', 'boolean'],
        ]);

        if (! in_array($report->status, ['dikirim', 'sedang_ditinjau'], true)) {
            abort(422, 'Laporan ini sudah tidak dapat ditinjau.');
        }

        $report->status = $data['status'];
        $report->review_note = $data['review_note'] ?? null;
        $report->flagged_for_followup = $request->boolean('flagged_for_followup');
        $report->save();

        return back()->with('status', 'Tinjauan laporan berhasil disimpan.');
    }

    public function forward(Request $request, Report $report)
    {
        $data = $request->validate([
            'forwarded_to' => ['required', 'string', 'max:255'],
        ]);

        if (
            $report->status !== 'sedang_ditinjau'
            || ! $report->flagged_for_followup
        ) {
            abort(422, 'Laporan harus ditinjau dan ditandai sebelum diteruskan.');
        }

        $report->forwarded_to = $data['forwarded_to'];
        $report->forwarded_at = now();
        $report->forwarded_by = $request->user()->id;
        $report->status = 'diteruskan';
        $report->save();

        return back()->with('status', 'Penerusan laporan berhasil dicatat.');
    }

    public function edit(Request $request, Report $report)
    {
        $this->ensureReporterCanEdit($request, $report);

        $incidentAt = Carbon::parse($report->incident_at)->format('Y-m-d\TH:i');

        return view('reports.edit', compact('report', 'incidentAt'));
    }

    private function ensureReporterCanEdit(Request $request, Report $report): void 
    {
        abort_unless(
            (int) $report->reporter_id === (int) $request->user()->id 
            && in_array($report->status, ['dikirim', 'perlu_perbaikan'], true),
            403
        );
    }
    
    public function update(Request $request, Report $report)
    {
        $this->ensureReporterCanEdit($request, $report);

        $data = $request->validate([
            'incident_at' => ['required', 'date', 'before_or_equal:now'],
            'description' => ['required', 'string', 'max:5000'],
            'people_affected' => ['required', 'integer', 'min:1', 'max:65535'],
            'evidence' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $report->incident_at = $data['incident_at'];
        $report->description = $data['description'];
        $report->people_affected = $data['people_affected'];

        if ($request->hasFile('evidence')) {
            $newPath = $request->file('evidence')->store('report-evidence', 'local');

            if ($report->evidence_path) {
                \Illuminate\Support\Facades\Storage::disk('local')
                    ->delete($report->evidence_path);
            }

            $report->evidence_path = $newPath;
        }

        if ($report->status === 'perlu_perbaikan') {
            $report->status = 'dikirim';
            $report->review_note = null;
            $report->flagged_for_followup = false;
        }

        $report->save();

        return redirect()
            ->route('reports.index')
            ->with('status', 'Laporan berhasil diperbaiki.');
    }
}