<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\API\ApiController;
use App\Models\User;
use App\Types\ApiStatusCode;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    use ThrottlesLogins;

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->error(ApiStatusCode::VALIDATION_ERROR, [
                'errors' => $validator->errors()
            ]);
        }

        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $this->incrementLoginAttempts($request);

        if (Auth::attempt(['email' => request('email'), 'password' => request('password'), 'is_active' => true])) {

            /* @var $user User */

            $user = Auth::user();
            $tokenResult = $user->createToken('Person Access Token');
            $this->clearLoginAttempts($request);

            return $this->success([
                'token' => $tokenResult->accessToken,
                'auth_type' => "Bearer",
                'expired_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                'user' => $user->toArray()
            ]);
        } else {
            return $this->error(ApiStatusCode::AUTHENTICATION_ERROR, [
                'message' => 'Invalid credentials'
            ]);
        }
    }

    public function username()
    {
        return 'email';
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return $this->error(ApiStatusCode::LOCKOUT_ERROR, [
            'message' => Lang::get('auth.throttle', ['seconds' => $seconds]),
        ]);
    }


    public function resetPasswordRequest(Request $request)
    {
        $user_id = User::where('email', $request->email)->value('id');

        if ($user_id) {
            $requestPasswordReset = User::find($user_id)->passwordResetRequest()->where('process', 0)->value('id');

            if ($requestPasswordReset) {
                return $this->error(ApiStatusCode::VERIFICATION_ERROR, [
                    'message' => 'Request Already Raised '
                ]);
            } else {
                User::find($user_id)->passwordResetRequest()->create(['request' => 1, 'process' => 0]);
                return $this->success(['message' => 'Successfully Raised  Password Reset Request']);
            }
        } else {
            return $this->error(ApiStatusCode::VERIFICATION_ERROR, [
                'message' => 'Invalid Email Address'
            ]);
        }
    }
}
