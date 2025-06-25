<?php

namespace App\Jobs;

use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PropertyEnvpBulk implements ShouldQueue
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
    public function handle($properties, $year = null)
    {
        if(!$year)
        {
            $year = date('Y');
        }

        //return view('admin.payments.bulk-receipt', ['properties' => $properties, 'year' => $year]);

        $properties = $properties->whereHas('assessment', function($query) use ($year) {
            $query->whereYear('created_at', $year);
        })->with([
            'assessment' => function ($query) use ($year) {
                $query->whereYear('created_at', $year)
                    ->with('categories', 'types', 'valuesAdded', 'dimension', 'wallMaterial', 'roofMaterial', 'zone', 'swimming');
            },
        ])->latest()->get();

        //return view('admin.envelope.bulk-envelope', ['properties' => $properties, 'year' => $year]);

        $pdf = \PDF::loadView('admin.envelope.bulk-envelope', ['properties' => $properties, 'year' => $year]);

        return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '-envelope.pdf');
    }
}
