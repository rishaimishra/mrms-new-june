<?php

namespace App\Jobs;

use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PropertyNotice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle($properties)
    {
        //return view('admin.payments.notice', compact('properties'));

        $pdf = \PDF::loadView('admin.payments.notice', compact('properties'));
        // ->setPaper('a4', 'landscape');
        // ->setOptions([
        //     //'dpi' => 125
        // ]);


        return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '-notice.pdf');
    }
}
