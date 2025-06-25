<?php

namespace App\Http\Controllers\APIV2\Landlord;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\APIV2\ApiController;
use App\Models\UserVerification;
use App\Notifications\MobileNumberVerification;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Twilio;
use App\Logic\SystemConfig;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        /* Validate Request */
        $request->validate([
            'mobile_number' => 'required',

        ]);


        $mobile_number = $request->input('mobile_number');
        /* Gererate otp from helper*/
        $code = generateOtp();


        //UserVerification::where(['mobile' => $mobile_number])->delete();

        $user = UserVerification::updateOrCreate(['mobile' => $mobile_number], [
            'code' => $code,
            'expired_at' => now()->addMinutes(15),
        ]);

        Passport::personalAccessTokensExpireIn(now()->addHours(1));

        $tokenResult = $user->createToken('Person Access Token');
        /* Otp Send On User's  Mobile */

        $user->notify(new MobileNumberVerification($code));

        $optionGroup = SystemConfig::getOptionGroup(SystemConfig::COMMUNITY_GROUP);

        $currency = ["usd" => "1", "le" => ($optionGroup->{\App\Logic\SystemConfig::CURRENCY_RATE})];

        return $this->success([
            'token' => $tokenResult->accessToken,
            'auth_type' => "Bearer",
            'expire_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'currency_rate' => $currency,
            'user' => $user->toArray(),

        ]);
    }

    public function mobileVerification(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:4',
        ]);

        $user = $request->user('landlord-api');
        $code = $request->code;


        $isValidVerification = UserVerification::where([
            'mobile' => $user->mobile,
            'code' => $code,

        ])
            ->where('expired_at', '>', now()->subMinutes(15)->format('Y-m-d H:i:s'))
            ->exists();

        if ($isValidVerification) {
            return $this->success(["message" => 'Mobile number verification success.']);
        } else {
            return $this->error(4003, ["message" => 'OTP may expired or invalidated.']);
        }
    }
}
