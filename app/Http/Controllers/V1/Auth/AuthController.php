<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\MobileRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Requests\V1\Auth\VerifyOtpRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
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

    public function sendOtp(MobileRequest $request)
    {
        try {
            $mobile = $request->validated()['mobile'];
            $OTP = rand(100000, 999999);
            Cache::put($OTP.'_'.$mobile, $OTP, now()->addMinutes(5));
            Log::info("OTP generated for {$mobile}: {$OTP}");
            return self::success(null,'ارسال کد تأیید با موفقیت انجام شد.');
        }catch (\Exception $e){
            return self::error(500);
        }
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $validated = $request->validated();
            $mobile = $validated['mobile'];
            $otp = $validated['otp'];
            $CashOTP = Cache::get($otp.'_'.$mobile);

            if(!$CashOTP || $CashOTP != $otp){
                return self::error(401,'Invalid or expired OTP');
            }

            $user = User::where('mobile',$mobile)->first();
            $accessToken = JWTAuth::fromUser($user);
            return self::success(['user' => new UserResource($user),'access_token' => $accessToken],'ورود با موفقیت انجام شد');
        }catch (\Exception $e){
            return self::error(500);
        }
    }
}
