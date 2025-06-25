<?php

namespace App\Jobs;

use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PropertyInBulk implements ShouldQueue
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
                // Get default limit
            $normalTimeLimit = ini_get('max_execution_time');

            // Restore default limit
            ini_set('max_execution_time', "1000"); 
                
            
            // Get default limit
            $normalMemoryLimit = ini_get('memory_limit');


            // Restore default limit
            ini_set('memory_limit', "512M"); 
           


        if(!$year)
        {
            $year = date('Y');
        }

        //return view('admin.payments.bulk-receipt', ['properties' => $properties, 'year' => $year]);

        $properties = $properties->whereHas('assessment', function($query) use ($year) {
            // $query->whereYear('created_at', '<=',$year);
            $query->whereYear('created_at',$year);
        })->with([
            'assessment' => function ($query) use ($year) {
                // $query->whereYear('created_at', '<=', $year)
                $query->whereYear('created_at', $year)
                    ->with('categories', 'types', 'valuesAdded', 'dimension', 'wallMaterial', 'roofMaterial', 'zone', 'swimming','payments');
            },
        ])->latest()->get();

         // return view('admin.payments.bulk-receipt', ['properties' => $properties, 'year' => $year]);
        $pdf = \PDF::loadView('admin.payments.bulk-receipt', ['properties' => $properties, 'year' => $year]);

        return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '.pdf');
    }
    
    
    public function handleAssessmentLandLord($properties)
    {
                // Get default limit
            $normalTimeLimit = ini_get('max_execution_time');

            // Restore default limit
            ini_set('max_execution_time', "1000"); 
                
            
            // Get default limit
            $normalMemoryLimit = ini_get('memory_limit');


            // Restore default limit
            ini_set('memory_limit', "512M"); 
           


       
        //return view('admin.payments.bulk-receipt', ['properties' => $properties, 'year' => $year]);

        $properties = $properties->whereHas('assessment', function($query) {
        
        })->with([
            'assessment' => function ($query) {
                $query->with('categories');
            },
        ])->latest()->get();

        //   dd($properties);

             return view('admin.properties.printasseslandBulk', ['properties' => $properties]);
            // $pdf = \PDF::loadView('admin.properties.printasseslandBulk', ['properties' => $properties, 'year' => 2022]);


         $pdf = \PDF::loadView('admin.properties.printasseslandBulk', ['properties' => $properties]);

        return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '.pdf');


        // return view('admin.payments.bulk-receipt', ['properties' => $properties, 'aslord' => 'asguard']);

        // $pdf = \PDF::loadView('admin.payments.bulk-receipt', ['properties' => $properties, 'aslord' => 'asguard']);

        // return $pdf->download(Carbon::now()->format('Y-m-d-H-i-s') . '.pdf');
    
        
    }
}
