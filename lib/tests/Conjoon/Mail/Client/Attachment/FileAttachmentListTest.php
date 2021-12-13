<?php

/**
 * conjoon
 * php-ms-imapuser
 * Copyright (C) 2019-2021 Thorsten Suckow-Homberg https://github.com/conjoon/php-ms-imapuser
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

namespace Tests\Conjoon\Mail\Client\Attachment;

use Conjoon\Mail\Client\Attachment\FileAttachment;
use Conjoon\Mail\Client\Attachment\FileAttachmentList;
use Conjoon\Mail\Client\Data\CompoundKey\AttachmentKey;
use Conjoon\Util\AbstractList;
use Conjoon\Util\Jsonable;
use Tests\TestCase;

/**
 * Class FileAttachmentListTest
 * @package Tests\Conjoon\Mail\Client\Attachment
 */
class FileAttachmentListTest extends TestCase
{


// ---------------------
//    Tests
// ---------------------

    /**
     * Tests constructor
     */
    public function testClass()
    {

        $attachmentList = new FileAttachmentList();
        $this->assertInstanceOf(AbstractList::class, $attachmentList);
        $this->assertInstanceOf(Jsonable::class, $attachmentList);


        $this->assertSame(FileAttachment::class, $attachmentList->getEntityType());
    }


    /**
     * Tests toJson()
     */
    public function testToJson()
    {
        $attachment1 = $this->createAttachment();
        $attachment2 = $this->createAttachment();

        $attachmentList = new FileAttachmentList();
        $attachmentList[] = $attachment1;
        $attachmentList[] = $attachment2;

        $this->assertSame([
            $attachment1->toJson(),
            $attachment2->toJson(),
        ], $attachmentList->toJson());
    }


    /**
     * @return FileAttachment
     */
    protected function createAttachment(): FileAttachment
    {

        return new FileAttachment(
            new AttachmentKey("dev", "INBOX", "123", "1"),
            [
                "type"          => "1",
                "text"          => "2",
                "size"          => 3,
                "content"   => "4",
                "encoding" => "base64"
            ]
        );
    }
}
