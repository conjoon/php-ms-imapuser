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

use Conjoon\Mail\Client\Writer\DefaultMessageItemDraftWriter,
    Conjoon\Mail\Client\Writer\MessageItemDraftWriter,
    Conjoon\Mail\Client\Data\MailAddress,
    Conjoon\Mail\Client\Data\MailAddressList,
    Conjoon\Mail\Client\Message\MessageItemDraft;


class DefaultMessageItemDraftWriterTest extends TestCase {


    public function testClass() {

        $writer = new DefaultMessageItemDraftWriter();
        $this->assertInstanceOf(MessageItemDraftWriter::class, $writer);
    }


    public function testProcess() {

        $writer = new DefaultMessageItemDraftWriter();

        $data = [
            "seen"    => true,
            "flagged" => true,
            "from"    => json_encode($this->createFrom()->toJson()),
            "replyTo" => json_encode($this->createReplyTo()->toJson()),
            "to"      => json_encode($this->createTo()->toJson()),
            "cc"      => json_encode($this->createCc()->toJson()),
            "bcc"     => json_encode($this->createBcc()->toJson()),
            "subject" => "Mail Subject",
            "date"    => "12332489"
        ];

        $draft = $writer->process($data);

        $this->assertInstanceOf(MessageItemDraft::class, $draft);

        $this->assertSame($data["subject"], $draft->getSubject());
        $this->assertSame($data["flagged"], $draft->getFlagged());
        $this->assertSame($data["seen"], $draft->getSeen());
        $this->assertSame($data["from"], json_encode($draft->getFrom()->toJson()));
        $this->assertSame($data["replyTo"], json_encode($draft->getReplyTo()->toJson()));
        $this->assertSame($data["to"], json_encode($draft->getTo()->toJson()));
        $this->assertSame($data["cc"], json_encode($draft->getCc()->toJson()));
        $this->assertSame($data["bcc"], json_encode($draft->getBcc()->toJson()));
        $this->assertSame($data["date"], "" . $draft->getDate()->getTimestamp());

    }


// +--------------------------------------
// | Helper
// +--------------------------------------

    /**
     * @return MailAddress
     */
    protected function createFrom() : MailAddress {
        return new MailAddress("from@demo.org", "From From");
    }


    /**
     * @return MailAddress
     */
    protected function createReplyTo()  : MailAddress {
        return new MailAddress("replyto@demo.org", "ReplyTo ReplyTo");
    }


    /**
     * @return MailAddressList
     */
    protected function createTo() : MailAddressList {
        $list = new MailAddressList();
        $list[] = new MailAddress("to1@demo.org", "To1 To1");
        $list[] = new MailAddress("to2@demo.org", "To2 To2");

        return $list;
    }


    /**
     * @return MailAddressList
     */
    protected function createCc() : MailAddressList  {
        $list = new MailAddressList();
        $list[] = new MailAddress("cc1@demo.org", "CC1 CC1");
        $list[] = new MailAddress("cc2@demo.org", "CC2 CC2");

        return $list;
    }


    /**
     * @return MailAddressList
     */
    protected function createBcc() : MailAddressList {
        $list = new MailAddressList();
        $list[] = new MailAddress("bcc1@demo.org", "BCC1 BCC1");
        $list[] = new MailAddress("bcc2@demo.org", "BCC2 BCC2");

        return $list;
    }
}