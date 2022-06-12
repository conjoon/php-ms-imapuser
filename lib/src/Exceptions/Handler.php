<?php

/**
 * conjoon
 * lumen-app-email
 * Copyright (c) 2019-2022 Thorsten Suckow-Homberg https://github.com/conjoon/lumen-app-email
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge,
 * publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Exceptions;

use Conjoon\Core\JsonStrategy;
use Conjoon\Http\Json\Problem\ProblemFactory;
use Conjoon\Http\Exception\HttpException as ConjoonHttpException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * @var JsonStrategy|null
     */
    protected JsonStrategy $jsonStrategy;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        ModelNotFoundException::class,
        ValidationException::class,
    ];


    /**
     * @param JsonStrategy|null $jsonStrategy
     */
    public function __construct(JsonStrategy $jsonStrategy)
    {
        $this->jsonStrategy = $jsonStrategy;
    }


    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Exception $e
     *
     * @return void
     *
     * @throws Exception
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Exception $e
     *
     * @return Response|JsonResponse
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ConjoonHttpException) {
            $problem = ProblemFactory::make(
                $e->getCode(),
                null,
                $e->getMessage()
            );

            return response()->json(
                ["errors" => [$problem->toJson($this->jsonStrategy)]],
                $problem->getStatus()
            );
        }
        return parent::render($request, $e);
    }
}
