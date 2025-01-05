<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Requests\V1\Auth\RegisterRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
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
                $token = JWTAuth::fromUser($user);
                $refreshToken = JWTAuth::claims(['refresh' => true])->fromUser($user);
            }
            return self::success(['user' => new UserResource($user),'token' => $token, 'refresh_token' => $refreshToken],'ورود با موفقیت انجام شد');
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
            $newToken = auth()->refresh();
            return self::success(['access_token' => $newToken,'token_type' => 'bearer','expires_in' => auth()->factory()->getTTL() * 60]);
        } catch (\Exception $e) {
            return self::error(401,'به توکن جدید دسترسی ندارید');
        }
    }
}
