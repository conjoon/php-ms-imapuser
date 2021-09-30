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

use Conjoon\Mail\Client\Reader\PurifiedHtmlStrategy,
    Conjoon\Mail\Client\Reader\HtmlReadableStrategy,
    Conjoon\Mail\Client\Message\Text\HtmlTextStrategy;


class PurifiedHtmlStrategyTest extends TestCase {


    public function testClass() {

        $strategy = new PurifiedHtmlStrategy();
        $this->assertInstanceOf(HtmlReadableStrategy::class, $strategy);
        $this->assertInstanceOf(HtmlTextStrategy::class, $strategy);
    }


    public function testProcess() {

        $strategy = new PurifiedHtmlStrategy();
        $text = "randomstring";

        $this->assertSame($text, $strategy->process($text));


        $text = "<html><head></head><body>Test</body></html>html>";
        $this->assertSame("Test", $strategy->process($text));

    }



}
