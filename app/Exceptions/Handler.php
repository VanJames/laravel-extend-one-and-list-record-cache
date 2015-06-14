<?php namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException'
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($request->ajax()) {
            $code = $e instanceof AppException ? $e->getCode() : 500;
            $message = !config('app.debug') ? [
                'errorMassage' => $e->getMessage(),
                'trace' => $e->getTrace()
            ] : $e->getMessage();
            return \Response::json(['error' => $code, 'msg' => $message, 'data' => []]);
        }
        if (config('app.debug')) {
            return parent::render($request, $e);
        }
        //404
        return parent::render($request, $e);
    }

}
