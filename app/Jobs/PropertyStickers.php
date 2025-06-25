<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PropertyStickers implements ShouldQueue
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
    public function handle($properties, $request)
    {
        //return view('admin.payments.stickers', compact('properties', 'request'));

        $pdf = \PDF::loadView('admin.payments.stickers', ['properties' => $properties, 'request' => $request]);

        return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '-stickers.pdf');
    }
}
