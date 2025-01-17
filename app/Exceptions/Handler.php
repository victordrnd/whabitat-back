<?php

namespace App\Exceptions;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
  /**
  * A list of the exception types that are not reported.
  *
  * @var array
  */
  protected $dontReport = [
    //
  ];

  /**
  * A list of the inputs that are never flashed for validation exceptions.
  *
  * @var array
  */
  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];

  /**
  * Report or log an exception.
  *
  * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
  *
  * @param  \Exception  $exception
  * @return void
  */
  public function report(Exception $exception)
  {
    //return ['error'];
    parent::report($exception);
  }

  /**
  * Render an exception into an HTTP response.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \Exception  $exception
  * @return \Illuminate\Http\Response
  */
  public function render($request, Exception $exception)
  {
    if ($this->isHttpException($exception)) {
      if ($exception->getStatusCode() == 404) {
        return Controller::responseJson(404, "La route n'existe pas");
      }
    }
    return parent::render($request, $exception);
  }
}
