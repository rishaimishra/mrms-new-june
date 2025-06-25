<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\BroadcastNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{

    public function index()
    {
        $type = ['' => "Select Recipient", 'property' => 'Property Owner', 'landlord' => 'Unique Landlord'];

        return view('admin.notification.edit', compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'sms_text' => 'required|string|max:50',
            'type' => 'required'
        ], [], [
            'type' => 'recipient'
        ]);

        $count = 0;

        $model = $request->input('type');
        $sms_text = $request->input('sms_text');

        if ($model == 'property') {
            $nProperty = Property::whereHas('assessment', function ($query) use ($request) {
                $query->whereYear('created_at', now()->format('Y'));
            })->withAssessmentCalculation(now()->format('Y'))
                ->having('current_year_payment', 0)
                ->having('total_payable_due', '>', 0)
                ->orderBy('total_payable_due')->get();

            if ($nProperty and $nProperty->count()) {
                foreach ($nProperty as $property) {
                    //dd($property->landlord->mobile_1);
                    if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $property->landlord->mobile_1)) {
                        $notification = $property->landlord->notify(new BroadcastNotification($property, $sms_text));
                    }
                }
            }
        } else {
            $nProperty = Property::whereHas('assessment', function ($query) use ($request) {
                $query->whereYear('created_at', now()->format('Y'));
            })->withAssessmentCalculation(now()->format('Y'))
                ->having('current_year_payment', 0)
                ->having('total_payable_due', '>', 0)
                ->orderBy('total_payable_due')->get();

            if ($nProperty and $nProperty->count()) {
                foreach ($nProperty as $property) {

                    if (preg_match('^(\+)([1-9]{3})(\d{8})$^', $property->landlord->mobile_1)) {
                        $notification = $property->landlord->notify(new BroadcastNotification($property, $sms_text));
                    }
                }
            }
        }

        return redirect()->back()->with($this->setMessage('Notification is successfully sent', self::MESSAGE_SUCCESS));
    }
}
