<?php

namespace App\Http\Controllers\APIV2\General;

use App\Http\Controllers\API\ApiController;
use App\Models\User;
use App\Models\UserTitleTypes;
use App\Types\ApiStatusCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppUserController extends ApiController
{
    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        $user = $request->user();

        if ($request->hasFile('image'))
            $user->image = $request->file('image')->store(User::USER_IMAGE);

        $user_name = $request->input('name');
        $user_title = UserTitleTypes::where('id',$request->input('user_title_id'))->value('label');
        $user->name = $user_title.". ".$user_name;
        $user->save();

        return $this->success([
            'user' => $user,
        ]);

    }

}
