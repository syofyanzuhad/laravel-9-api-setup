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
            'callback_url' => 'required|url'
        ]);

        $user = \App\Models\User::where('email', $input['email'])->first();
        try {
            // send mail notification
            $user->notify(new \App\Notifications\MailResetPasswordNotification(app('auth.password.broker')->createToken($user), $request->callback_url));
    
            return $this->successResponse([
                'email' => $input['email'],
            ], 'Email berhasil dikirim, silakan cek email anda.');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
        //$message = $response == Password::RESET_LINK_SENT ? 'Mail send successfully' : GLOBAL_SOMETHING_WANTS_TO_WRONG;
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->errorResponse(trans($response));
    }
}
