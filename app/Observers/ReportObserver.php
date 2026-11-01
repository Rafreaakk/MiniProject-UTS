<?php

namespace App\Observers;

use App\Models\Report;
use Illuminate\Support\Facades\Log;

class ReportObserver
{
    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
    {
        if (! $report->wasChanged('status')) {
            return;
        }

        Log::info('Status laporan berubah', [
            'report_id' => $report->id,
            'status_sebelum' => $report->getOriginal('status'),
            'status_sesudah' => $report->status,
            'diubah_oleh' => auth()->id(),
        ]);
    }

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "restored" event.
     */
    public function restored(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "force deleted" event.
     */
    public function forceDeleted(Report $report): void
    {
        //
    }
}
