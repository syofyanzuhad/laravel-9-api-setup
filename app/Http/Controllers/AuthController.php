<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

/**
 * @group Authentication
 *
 * APIs for managing Authentication User.
 */
class AuthController extends Controller
{

    /**
     * Get the authenticated User.
     *
     * @bodyParam username string required The user username. Example: admin
     * @bodyParam password string required Used to authenticate the user.
     *
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');

        $expirationInMinutes = config('jwt.ttl');
        if ($request->remember_me == 'true') {
            $expirationInMinutes = config('jwt.remember_ttl');
            config([
                'jwt.ttl' => $expirationInMinutes, // <=== Not expired
                'jwt.required_claims' => ['iss', 'iat', 'nbf', 'sub', 'jti'],
            ]);
        }

        try {
            if (! $token = auth('api')->attempt($credentials)) {
                return $this->errorResponse('Username atau password salah !');
            }
        } catch (JWTException $e) {
            report($e);

            return $this->errorResponse("Server error ! {$e->getMessage()}", 500);
        }
        $user = auth('api')->user();

        return $this->successResponse(compact('user', 'token', 'expirationInMinutes'), 'Login berhasil');
    }

    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:6|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->validationErrorResponse($validator->errors(), 422);
    //     }

    //     $user = User::create([
    //         'name' => $request->get('name'),
    //         'email' => $request->get('email'),
    //         'password' => Hash::make($request->get('password')),
    //     ]);

    //     // Santri::create([
    //     //     'id_santri' => strtoupper(Str::random(12)),
    //     //     'user_id' => $user->id,
    //     //     'username' => substr($user->email, 0, strpos($user->email, '@')),
    //     // ]);

    //     $token = JWTAuth::fromUser($user);
    //     $message = ['Registrasi berhasil'];

    //     // try {
    //     //     $user->sendEmailVerificationNotification();
    //     //     array_push($message, 'Email terkirim');
    //     // } catch (\Throwable $th) {
    //     //     report($th);

    //     //     array_push($message, 'Email tidak terkirim');
    //     // }

    //     return $this->successResponse(compact('user', 'token'), $message, 201);
    // }

    /**
     * Get the new token array structure.
     *
     * @authenticated
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        try {
            $newToken = auth('api')->refresh();

            return $this->successResponse(['token' => $newToken]);
        } catch (\Throwable $th) {
            report($th);

            return $this->errorResponse('Terjadi kesalahan !', 500);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @authenticated
     *
     * @return JsonResponse
     */
    public function logout()
    {
        try {
            auth('api')->logout();

            return $this->successResponse(null, 'Logout success !');
        } catch (\Throwable $th) {
            report($th);

            return $this->errorResponse(['logout' => 'Logout gagal !', 'detail' => $th->getMessage()], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @authenticated
     *
     * @return JsonResponse
     */
    public function me()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->errorResponse('Pengguna tidak ditemukan !', 404);
            }
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e) {
            report($e);

            return $this->errorResponse('Token kadaluwarsa !');
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
            report($e);

            return $this->errorResponse('Token tidak valid !');
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            report($e);

            return $this->errorResponse('Token tidak ditemukan !', 404);
        }

        return $this->successResponse($user);
    }

    /**
     * Update authenticated User.
     *
     * @authenticated
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->errorResponse('Pengguna tidak ditemukan !', 404);
            }
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException $e) {
            report($e);

            return $this->errorResponse('Token kadaluwarsa !');
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException $e) {
            report($e);

            return $this->errorResponse('Token tidak valid !');
        } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
            report($e);

            return $this->errorResponse('Token tidak ditemukan !', 404);
        }

        $validated = $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nama' => 'required|string|max:255',
            'username' => 'required|alpha_num|max:255|unique:users,username,'.$user->id,
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->update($validated);
        $this->storeImage($request, $user, 'avatar', 'avatar');

        return $this->successResponse(compact('user'), 'Update berhasil');
    }

    /**
     * Update password of authenticated User.
     *
     * @authenticated
     *
     * @bodyParam password string required The old password.
     * @bodyParam new_password string required The new password.
     * @bodyParam new_password_confirmation string required The new password confirmation.
     *
     * @return JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string',
            'new_password' => 'required|string|confirmed|min:8',
        ]);

        $user = auth('api')->user();

        if (! Hash::check($validated['password'], $user->password)) {
            return $this->errorResponse('Password lama salah !');
        }

        $user->update(['password' => Hash::make($validated['new_password'])]);

        return $this->successResponse(compact('user'), 'Update password berhasil');
    }
}
