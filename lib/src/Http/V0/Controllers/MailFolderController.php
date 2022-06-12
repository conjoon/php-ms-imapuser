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

declare(strict_types=1);

namespace App\Http\V0\Controllers;

use Conjoon\Mail\Client\Service\MailFolderService;
use Conjoon\Core\JsonStrategy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class MailFolderController
 * @package App\Http\Controllers
 */
class MailFolderController extends Controller
{
    /**
     * @var MailFolderService
     */
    protected MailFolderService $mailFolderService;

    /**
     * @var jsonStrategy
     */
    protected JsonStrategy $jsonStrategy;

    /**
     * MailFolderController constructor.
     *
     * @param MailFolderService $mailFolderService
     */
    public function __construct(MailFolderService $mailFolderService, JsonStrategy $jsonStrategy)
    {
        $this->mailFolderService = $mailFolderService;
        $this->jsonStrategy = $jsonStrategy;
    }


    /**
     * Returns all available MailFolders for the user that is currently
     * authenticated for the specified $mailAccountId.
     *
     * @param string $mailAccountId
     *
     * @return JsonResponse
     */
    public function index(string $mailAccountId): JsonResponse
    {
        $user = Auth::user();

        $mailFolderService = $this->mailFolderService;
        $mailAccount       = $user->getMailAccount($mailAccountId);

        return response()->json([
            "data" => $mailFolderService->getMailFolderChildList($mailAccount)->toJson($this->jsonStrategy)
        ]);
    }
}
