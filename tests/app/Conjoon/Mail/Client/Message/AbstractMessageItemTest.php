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

use Conjoon\Mail\Client\Message\AbstractMessageItem,
    Conjoon\Mail\Client\Data\CompoundKey\MessageKey,
    Conjoon\Mail\Client\Data\MailAddress,
    Conjoon\Mail\Client\Data\MailAddressList,
    Conjoon\Util\Jsonable;


class AbstractMessageItemTest extends TestCase
{



// ---------------------
//    Tests
// ---------------------

    /**
     * Tests constructor
     */
    public function testConstructor() {

        $messageKey = $this->createMessageKey();
        $messageItem = $this->createMessageItem($messageKey);
        $this->assertInstanceOf(Jsonable::class, $messageItem);
    }


    /**
     * Test class.
     */
    public function testClass() {

        $item = $this->getItemConfig();

        $messageKey = $this->createMessageKey();

        $messageItem = $this->createMessageItem($messageKey, $item);

        $this->assertSame($messageKey, $messageItem->getMessageKey());

        foreach ($item as $key => $value) {

            $method = "get" . ucfirst($key);

            switch ($key) {
                case 'date':
                    $this->assertNotSame($item["date"], $messageItem->getDate());
                    $this->assertEquals($item["date"], $messageItem->getDate());
                    break;
                case 'from':
                    $this->assertNotSame($item["from"], $messageItem->getFrom());
                    $this->assertEquals($item["from"], $messageItem->getFrom());
                    break;
                case 'to':
                    $this->assertNotSame($item["to"], $messageItem->getTo());
                    $this->assertEquals($item["to"], $messageItem->getTo());
                    break;
                default :
                    $this->assertSame($messageItem->{$method}(), $item[$key], $key);
            }
        }
    }


    /**
     * Test type exceptions.
     */
    public function testTypeException() {

        $caught = [];

        $testException = function($key, $type) use (&$caught) {

            $item = $this->getItemConfig();

            switch ($type) {
                case "int":
                    $item[$key] = (int)$item[$key];
                    break;
                case "string":
                    $item[$key] = (string)$item[$key];
                    break;

                default:
                    $item[$key] = $type;
                    break;
            }

            try {
                $this->createMessageItem($this->createMessageKey(), $item);
            } catch (\TypeError $e) {
                if (in_array($e->getMessage(), $caught)) {
                    return;
                }
                $caught[] = $e->getMessage();
            }

        };

        $testException("subject", "int");
        $testException("charset", "int");
        $testException("size", "string");
        $testException("seen", "string");
        $testException("answered", "string");
        $testException("recent", "string");
        $testException("draft", "string");
        $testException("hasAttachments", "string");
        $testException("flagged", "string");
        $testException("from", "");
        $testException("to", "");
        $testException("date", "");

        $this->assertSame(12, count($caught));
    }


    /**
     * Test \BadMethodCallException for setMessageKey
     */
    public function testSetMessageKey() {

        $this->expectException(\BadMethodCallException::class);

        $messageKey = $this->createMessageKey();

        $messageItem = $this->createMessageItem($messageKey);

        $messageKey2 = $this->createMessageKey();

        $messageItem->setMessageKey($messageKey2);
    }


    /**
     * Test toJson
     */
    public function testToJson() {
        $item = $this->getItemConfig();

        $messageKey = $this->createMessageKey();

        $messageItem = $this->createMessageItem($messageKey, $item);

        $keys = array_keys($item);

        $this->assertEquals(
            $messageKey->toJson(),
            array_intersect_key($messageItem->toJson(), array_flip(['id', 'mailAccountId', 'mailFolderId']))
        );


        foreach ($keys as $key) {
            if ($key === "charset") {
                $this->assertFalse(array_key_exists('charste', $messageItem->toJson()));
            }else if ($key === "from" || $key === "to") {
                $this->assertEquals($item[$key]->toJson(), $messageItem->toJson()[$key]);
            } else if ($key == "date") {
                $this->assertEquals($item[$key]->format("Y-m-d H:i:s"), $messageItem->toJson()[$key]);
            } else{
                $this->assertSame($item[$key], $messageItem->toJson()[$key]);
            }
        }


        $messageKey = $this->createMessageKey();

        $messageItem = $this->createMessageItem($messageKey);

        $json = $messageItem->toJson();

        $this->assertSame("1970-01-01 00:00:00", $json["date"]);
        $this->assertSame([], $json["to"]);
        $this->assertSame([], $json["from"]);

    }


    /**
     * Test setFrom /w null
     */
    public function testSetFromWithNull() {

        $messageKey = $this->createMessageKey();

        $messageItem = $this->createMessageItem($messageKey, ["from" => null]);

        $this->assertSame(null, $messageItem->getFrom());

    }

// ---------------------
//    Helper Functions
// ---------------------


    /**
     * Returns an anonymous class extending AbstractMessageItem.
     * @param MessageKey $key
     * @param array|null $data
     * @return AbstractMessageItem
     */
    protected function createMessageItem(MessageKey $key, array $data = null) :AbstractMessageItem {
        // Create a new instance from the Abstract Class
       return new class($key, $data) extends AbstractMessageItem {

        };
    }

    /**
     * Returns an MessageItem as array.
     */
    protected function getItemConfig() {

        return [
            'charset'        => 'iso-8859-1',
            'from'           => $this->createFrom(),
            'to'             => $this->createTo(),
            'size'           => 23,
            'subject'        => "SUBJECT",
            'date'           => new \DateTime(),
            'seen'           => false,
            'answered'       => true,
            'draft'          => false,
            'flagged'        => true,
            'recent'         => false,
            'hasAttachments' => true
        ];

    }


    /**
     * Returns a MessageKey.
     *
     * @param string $mailFolderId
     * @param string $id
     *
     * @return MessageKey
     */
    protected function createMessageKey($mailAccountId = "dev", $mailFolderId = "INBOX", $id = "232") :MessageKey {
        return new MessageKey($mailAccountId, $mailFolderId, $id);
    }


    /**
     * Returns a MailAddress to be used with the "from" property of the MessageItem
     * to test.
     *
     * @return MailAddress
     */
    protected function createFrom() :MailAddress {
        return new MailAddress("peterParker@newyork.com", "Peter Parker");
    }

    /**
     * Returns a MailAddressList to be used with the "to" property of the MessageItem
     * @return MailAddressList
     */
    protected function createTo() : MailAddressList {

        $list = new MailAddressList;

        $list[] = new MailAddress("name1", "name1@address.testcomdomaindev");
        $list[] = new MailAddress("name2", "name2@address.testcomdomaindev");

        return $list;
    }

}