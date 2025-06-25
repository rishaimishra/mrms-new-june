<?php

namespace App\Http\Controllers\Admin\Config;

use App\Logic\SystemConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommunityController extends Controller
{
    public function __invoke()
    {
        $optionGroup = SystemConfig::getOptionGroup(SystemConfig::COMMUNITY_GROUP);
        return view('admin.config.community', compact('optionGroup'));
    }

    public function save(Request $request)
    {

        SystemConfig::saveGroupOptions($request, SystemConfig::COMMUNITY_GROUP);

        return redirect()->back()->with($this->setMessage(
            'Gated Community has been successfully updated.',
            self::MESSAGE_SUCCESS
        ));
    }
}
