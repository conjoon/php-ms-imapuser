<?php
/**
 * conjoon
 * php-cn_imapuser
 * Copyright (C) 2019 Thorsten Suckow-Homberg https://github.com/conjoon/php-cn_imapuser
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

use Conjoon\Util\AbstractList,
    Conjoon\Util\Jsonable,
    Conjoon\Mail\Client\Data\CompoundKey\AttachmentKey,
    Conjoon\Mail\Client\Attachment\Attachment,
    Conjoon\Mail\Client\Attachment\AttachmentList;


class AttachmentListTest extends TestCase
{


// ---------------------
//    Tests
// ---------------------

    /**
     * Tests constructor
     */
    public function testClass() {

        $attachmentList = new AttachmentList();
        $this->assertInstanceOf(AbstractList::class, $attachmentList);
        $this->assertInstanceOf(Jsonable::class, $attachmentList);

        $this->assertSame(Attachment::class, $attachmentList->getEntityType());
    }


    /**
     * Test toJson
     */
    public function testToJson() {

        $attachment1 = $this->createAttachment();
        $attachment2 = $this->createAttachment();

        $attachmentList = new AttachmentList();
        $attachmentList[] = $attachment1;
        $attachmentList[] = $attachment2;

        $this->assertSame([
            $attachment1->toJson(),
            $attachment2->toJson(),
        ], $attachmentList->toJson());
    }


// ---------------------
//    Helper
// ---------------------

    /**
     * @return Attachment
     */
    protected function createAttachment() :Attachment {

        return new Attachment(
            new AttachmentKey("dev", "INBOX", "123", "1"),
            ["type"          => "1",
             "text"          => "2",
             "size"          => 3,
             "downloadUrl"   => "4",
             "previewImgSrc" => "5"]
        );

    }

}