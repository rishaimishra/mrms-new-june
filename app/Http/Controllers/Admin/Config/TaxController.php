<?php


namespace App\Http\Controllers\Admin\Config;


use App\Http\Controllers\Controller;
use App\Logic\SystemConfig;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function __invoke()
    {
        $optionGroup = SystemConfig::getOptionGroup(SystemConfig::TAX_GROUP);
        return view('admin.config.tax', compact('optionGroup'));
    }

    public function save(Request $request)
    {

        $request->validate([
            'digital_administration' => 'required|numeric|min:1',
            'transport' => 'required|numeric|min:1',
            'fuel' => 'required|numeric|min:1',
            'gst' => 'required|numeric|min:1',
            'tip' => 'required|numeric|min:0',

        ]);

        SystemConfig::saveGroupOptions($request, SystemConfig::TAX_GROUP);

        return redirect()->back()->with($this->setMessage(
            'About has been successfully updated.',
            self::MESSAGE_SUCCESS)
        );
    }
}