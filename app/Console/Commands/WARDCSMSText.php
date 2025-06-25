<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\BroadcastNotification;
use App\Models\Property;
use Illuminate\Support\Facades\Notification;

class WARDCSMSText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:WARDCSMSText';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WARDC Sms Text';

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
        $nProperty = Property::whereHas('assessment', function ($query) {
            $query->whereYear('created_at', now()->format('Y'));
        })->withAssessmentCalculation(now()->format('Y'))
            ->having('current_year_payment', 0)
            ->having('total_payable_due', '>', 0)
            ->orderBy('total_payable_due', 'desc')->get();

        if ($nProperty and $nProperty->count()) {
            foreach ($nProperty as $property) {

                //$sms_text = "Dear Property Owner, you are in arrears of Le {$property->total_payable_due} for your " . now()->format('Y') . " WARDC Property Rate. Kindly endeavor to make payments soon. Ignore if already paid or 076864861 for queries.";
                $sms_text = "Dear Property Owner, you have arrears of NLe {$property->total_payable_due} for your " . now()->format('Y') . " WARDC PropertyRate. Kindly make payments soon. Ignore if already paid or 76864861 for query";
                //dd($property->landlord->mobile_1);
                if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $property->landlord->mobile_1)) {
                    $notification = $property->landlord->notify(new BroadcastNotification($property, $sms_text));
                    //dd($property->landlord->mobile_1);
                }
            }
        }
    }
}
