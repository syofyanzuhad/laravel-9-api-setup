<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldReport($e) && app()->bound('sentry') && config('app.env') == 'production') {
                app('sentry')->captureException($e);
            }
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException && ! $request->expectsJson()) {
            return back()->withError(__('Kami mendeteksi tidak ada aktivitas cukup lama, silakan ulangi aksi sebelumnya'));
        }

        if ($e instanceof AuthorizationException && ! $request->expectsJson()) {
            return redirect()->back(302, [], route('home'))->withError(
                __(
                    'Anda tidak diizinkan mengakses halaman :url',
                    ['url' => $request->fullUrl()]
                )
            );
        }

        if ($request->expectsJson()) {   //add Accept: application/json in request
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    private function handleApiException($request, Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof AuthorizationException) {
            $exception = $exception->getMessage();
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->customApiResponse($exception);
    }

    private function customApiResponse($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = [];

        switch ($statusCode) {
            case Response::HTTP_UNAUTHORIZED:
                $response['success'] = false;
                $response['status'] = 'error';
                $response['message'] = 'Unauthorized';
                $response['detail'] = Lang::get($this->checkException($exception, 'Silahkan login terlebih dahulu'));
                break;
            case Response::HTTP_FORBIDDEN:
                $response['success'] = false;
                $response['status'] = 'error';
                $response['message'] = 'Forbidden';
                $response['detail'] = Lang::get($this->checkException($exception, 'Anda tidak memiliki akses'));
                break;
            case Response::HTTP_NOT_FOUND:
                $response['success'] = false;
                $response['status'] = 'not_found';
                $response['message'] = 'Not Found';
                $response['detail'] = Lang::get($this->checkException($exception, 'Resource tidak ditemukan'));
                break;
            case Response::HTTP_METHOD_NOT_ALLOWED:
                $response['success'] = false;
                $response['status'] = 'error';
                $response['message'] = 'Method Not Allowed';
                $response['detail'] = Lang::get($this->checkException($exception, 'Method request tidak valid'));
                break;
            case Response::HTTP_UNPROCESSABLE_ENTITY:
                $response['success'] = false;
                $response['status'] = 'validation_error';
                $response['message'] = Lang::get($exception->original['message']);
                $response['errors'] = $this->transformErrors($exception->original['errors']);
                break;
            default:
                $response['success'] = false;
                $response['status'] = 'error';
                $response['message'] = ($statusCode == 500) ? 'Whoops, terjadi kesalahan !' : $exception->getMessage();
                $response['detail'] = $this->checkException($exception, '');
                break;
        }

        if (config('app.debug') && $statusCode != 422) {
            $response['file'] = method_exists($exception, 'getFile') ? $exception->getFile() : '';
            $response['code'] = method_exists($exception, 'getCode') ? $exception->getCode() : '';
            $response['trace'] = method_exists($exception, 'getTrace') ? $exception->getTrace() : '';
        }

        return response()->json($response, $statusCode);
    }

    public function checkException($exception, $message = 'Error exception message')
    {
        return method_exists($exception, 'getMessage') ? $exception->getMessage() : $message;
    }

    // transform the error messages,
    private function transformErrors($exception)
    {
        $errors = [];

        foreach ($exception as $field => $message) {
            $errors[] = [
                'field' => $field,
                'message' => $message,
            ];
        }

        return $errors;
    }
}
