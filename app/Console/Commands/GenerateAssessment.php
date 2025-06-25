<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

class GenerateAssessment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assessment:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate assessments for current year';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $properties = Property::whereDoesntHave('assessment', function ($query) {
            $year = now()->format('Y');
            $query->whereYear('created_at', $year);
        })->get();

        foreach($properties as $property) {
            $property->generateAssessments();
        }

        $this->info($properties->count());

    }
}
