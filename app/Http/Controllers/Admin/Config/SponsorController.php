<?php


namespace App\Http\Controllers\Admin\Config;


use App\Http\Controllers\Controller;
use App\Logic\SystemConfig;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function __invoke()
    {

        $optionGroup = SystemConfig::getOptionGroup(SystemConfig::SPONSOR_GROUP);

        return view('admin.config.sponsor', compact('optionGroup'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'place_sponsor' => 'nullable|string|max:191',
            'product_sponsor' => 'nullable|string|max:191',
            'auto_sponsor' => 'nullable|string|max:191',
            'real_state_sponsor' => 'nullable|string|max:191',
            'quiz_sponsor' => 'nullable|string|max:191',

        ]);
        SystemConfig::saveGroupOptions($request, SystemConfig::SPONSOR_GROUP);

        return redirect()->back()->with($this->setMessage(
            'Sponsor text has been successfully updated.',
            self::MESSAGE_SUCCESS
        ));
    }
}
