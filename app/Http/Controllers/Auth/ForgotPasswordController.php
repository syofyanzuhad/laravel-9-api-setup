<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

/**
 * @group Authentication
 * Class ForgotPasswordController
 */
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    /**
     * Send a reset link to the given user.
     *
     * @bodyParam email string required The email of the user.
     * @response {
     *      "success": true,
     *      "status": "success",
     *      "message": "Email berhasil dikirim, silakan cek email anda.",
     *      "data": {
     *          "email": "admin@gmail.coma"
     *      }
     * }
     *
     * @return JsonResponse
     */
    protected function sendResetLinkResponse(Request $request)
    {
        $input = $request->only('email');
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $response = Password::sendResetLink($input);

        switch ($response):
            case Password::RESET_LINK_SENT:
                $message = 'Email berhasil dikirim, silakan cek email anda.';

        return $this->successResponse($input, $message, 200);
        case Password::INVALID_USER:
                $message = 'Email tidak terdaftar.';

        return $this->errorResponse(['errors'=>$message]);
        default:
                $message = 'Terjadi kesalahan, silakan coba beberapa saat lagi.';

        return $this->errorResponse($message);
        endswitch;
        //$message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : GLOBAL_SOMETHING_WANTS_TO_WRONG;
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->errorResponse(trans($response));
    }
}
