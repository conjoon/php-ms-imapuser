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

namespace App\Http\V0\Controllers;

use Conjoon\Mail\Client\Data\CompoundKey\CompoundKey;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * Returns the uri for the specified type. The type can be one
     * - MessageItem
     *
     * @param string $type
     * @param CompoundKey $key
     * @param $callerUri The uri that triggered a call to this method, and from
     * which the current version of the api being used should be extracted.
     *
     * @return string
     */
    protected function getResourceUrl(string $type, CompoundKey $key, $callerUri): string
    {
        preg_match("/\/(v\d*)\//mi", $callerUri, $matches);

        $version = $matches[1] ?? config("app.api.latest");

        $baseUrl = implode(
            "/",
            [config("app.url"), config("app.api.service.email"), $version]
        );

        switch ($type) {
            case "MessageItem":
                $path = implode("/", [
                    "MailAccounts",
                    $key->getMailAccountId() ,
                    "MailFolders",
                    $key->getMailFolderId() ,
                    "MessageItems",
                    $key->getId() ,
                ]);
                return $baseUrl . "/" . $path;
        }

        return $baseUrl;
    }
}
