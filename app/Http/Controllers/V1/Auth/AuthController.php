<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\SendOtpRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Requests\V1\Auth\VerifyOtpRequest;
use App\Http\Resources\V1\UserResource;
use App\Jobs\SendOtpSmsJob;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Ghasedak\GhasedakApi;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = User::create($validated);
            $token = JWTAuth::fromUser($user);
            return self::success(['user' => new UserResource($user),'token' => $token],'ثبت نام کاربر با موفقیت انجام شد');
        } catch (\Exception $e) {
            return self::error(500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            if (auth()->attempt($request->validated())) {
                $user = auth()->user();
                $accessToken = JWTAuth::fromUser($user);
                $refreshToken = JWTAuth::claims(['refresh' => true])->fromUser($user);
            }
            return self::success(['user' => new UserResource($user),'access_token' => $accessToken, 'refresh_token' => $refreshToken],'ورود با موفقیت انجام شد');
        }catch (\Exception $e){
            return self::error(401,'اطلاعات ورود اشتباه است');
        }
    }

    public function logout()
    {
        try {
            auth()->logout();
            return self::success(null,'خروج کاربر با موفقیت انجام شد');
        } catch (\Exception $e) {
            return self::error(500);
        }
    }

    public function refresh()
    {
        try {
            $user = auth()->user();
            $accessToken = JWTAuth::fromUser($user);
            $refreshToken = auth()->refresh();
            return self::success(['access_token' => $accessToken, 'refresh_token' => $refreshToken,'token_type' => 'bearer','expires_in' => auth()->factory()->getTTL() * 60]);
        } catch (\Exception $e) {
            return self::error(401,'به توکن جدید دسترسی ندارید');
        }
    }

    public function sendOtp(SendOtpRequest $request)
    {
        try {
            $cellphone = $request->validated()['cellphone'];
            $User = User::updateOrCreate(
                ['cellphone'=> $cellphone],
                ['cellphone' => $cellphone]
            );

            $existOtpCode = Token::where('user_id', $User->id)->first();
            if ($existOtpCode && Carbon::parse($existOtpCode->expires_at)->gte(now()->subMinutes(5))) {
                return response()->json(['message' => 'OTP هنوز معتبر است  '], 200);
            }

            $otpCode = rand(100000, 999999);
            $accessToken = JWTAuth::fromUser($User);
            $refreshToken = JWTAuth::claims(['refresh' => true])->fromUser($User);

           Token::updateOrCreate(
                ['user_id' => $User->id],
                [
                    'token' => $accessToken,
                    'code' =>$otpCode,
                    'type' => 'otp',
                    'user_agent' => $request->header('User-Agent'),
                    'attempt' => $existOtpCode ? $existOtpCode->attempt + 1 : 1,
                    'expired_at' => now()->addMinutes(5),
                ]
            );
            SendOtpSmsJob::dispatch($cellphone, $otpCode);
            return self::success(['access_token' => $accessToken, 'refresh_token' => $refreshToken,'token_type' => 'bearer'], 'ارسال کد تأیید با موفقیت انجام شد.');
        } catch (\Exception $e) {
            return self::error(500);
        }
    }


    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $validated = $request->validated();
            $cellphone = $validated['cellphone'];
            $otp = $validated['otp'];
            $existOTP = Token::where('code', $otp)->first();

            if(!$existOTP || $existOTP->code != $otp){
                return self::error(401,'Invalid or expired OTP');
            }

            $user = User::where('cellphone',$cellphone)->first();
            $accessToken = JWTAuth::fromUser($user);
            return self::success(['user' => new UserResource($user),'access_token' => $accessToken],'ورود با موفقیت انجام شد');
        }catch (\Exception $e){
            return self::error(500);
        }
    }
}
