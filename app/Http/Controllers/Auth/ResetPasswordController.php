<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

/**
 * @group Authentication
 * Class ForgotPasswordController
 */
class ResetPasswordController extends Controller
{
    /**
     * Reset the given user's password.
     *
     * @bodyParam email string required The email of the user.
     * @bodyParam password string required The new password of the user.
     * @bodyParam password_confirmation string required The new password confirmation of the user.
     * @bodyParam token string required The token of the user.
     * @response {
     *    "success": true,
     *    "status": "success",
     *    "message": "Email berhasil diubah",
     *    "data": {
     *        "email": "admin@gmail.coma"
     *    }
     * }
     */
    protected function sendResetResponse(Request $request)
    {
        //password.reset
        $input = $request->only('email', 'token', 'password', 'password_confirmation');
        $request->validator([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);
        $response = Password::reset($input, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();
            //$user->setRememberToken(Str::random(60));
            event(new PasswordReset($user));
        });

        switch ($response):
            case Password::PASSWORD_RESET:
                $message = 'Password berhasil diubah';

        return $this->successResponse($input['email'], $message);
        case Password::INVALID_USER:
                $message = 'Email tidak ditemukan';

        return $this->errorResponse($message);
        default:
                $message = 'Password gagal diubah';

        return $this->errorResponse($message);
        endswitch;
    }
}
