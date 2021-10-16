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

namespace Tests\App\Providers;

use App\Providers\ImapAuthServiceProvider;
use Conjoon\Illuminate\Auth\Imap\DefaultImapUserProvider;
use Conjoon\Illuminate\Auth\Imap\ImapUser;
use Conjoon\Illuminate\Auth\Imap\ImapUserProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Mockery;
use ReflectionClass;
use ReflectionException;
use Tests\TestCase;

/**
 * Class ImapAuthServiceProviderTest
 * @package Tests\App\Providers
 */
class ImapAuthServiceProviderTest extends TestCase
{

    /**
     * Makes sure register() registers the ImapUserProvider.
     *
     *
     */
    public function testRegister()
    {
        $app = $this->createAppMock();
        $provider = new ImapAuthServiceProvider($app);

        $app['auth'] = Mockery::mock($app['auth']);

        $app['auth']
            ->shouldReceive("provider")
            ->withArgs(
                function ($driverName, $callback) use ($app) {
                    $this->assertSame("ImapUserProviderDriver", $driverName);
                    $this->assertIsCallable($callback);

                    $this->assertInstanceOf(
                        ImapUserProvider::class,
                        $callback($app, [])
                    );

                    return true;
                }
            );

        $provider->register();
    }

    /**
     * Makes sure boot() calls getImapUser.
     *
     */
    public function testBootCallsGetUser()
    {
        $app = $this->createAppMock();

        $imapUser = $this->getMockBuilder(ImapUser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $provider = $this->getMockBuilder(ImapAuthServiceProvider::class)
            ->setConstructorArgs([$app])
            ->onlyMethods(["getImapUser"])
            ->getMock();

        $cb = function () use ($imapUser) {
            return $imapUser;
        };

        $provider->method("getImapUser")
            ->willReturnCallback($cb);


        $app['auth'] = Mockery::mock($app['auth']);

        $app['auth']->shouldReceive("viaRequest")
            ->withArgs(function ($api, $callback) use ($app, $imapUser) {
                $this->assertSame("api", $api);
                $this->assertIsCallable($callback);

                $this->assertSame($imapUser, $callback(
                    new Request(),
                    $app->make(ImapUserProvider::class)
                ));

                return true;
            });

        $provider->boot();
    }


    /**
     * Should delegate call to UserProvider's retrieveByCredentials
     *
     * @throws ReflectionException
     * @throws BindingResolutionException
     */
    public function testGetImapUserCallRetrieveByCredentials()
    {
        $app = $this->createAppMock();

        $authProvider = new ImapAuthServiceProvider($app);

        $request = Mockery::mock(new Request());
        $request->shouldReceive("getUser")
            ->andReturn("someuser");
        $request->shouldReceive("getPassword")
            ->andReturn("somepassword");

        $userProvider = Mockery::mock($app->make(ImapUserProvider::class));

        $userProvider->shouldReceive("retrieveByCredentials")
                     ->withArgs([["username" => "someuser", "password" => "somepassword"]])
                     ->andReturn(
                         $this->getMockBuilder(ImapUser::class)
                              ->disableOriginalConstructor()
                              ->getMock()
                     );

        $reflection = new ReflectionClass($authProvider);
        $property = $reflection->getMethod("getImapUser");
        $property->setAccessible(true);

        $property->invokeArgs($authProvider, [$request, $userProvider]);
    }


    /**
     * Mocks the application by returning a specific ImapUserProvider-configuration.
     *
     * @return Application|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected function createAppMock(): Application
    {
        $app = Mockery::mock($this->app);

        $app->shouldReceive("make")
            ->with(ImapUserProvider::class)
            ->andReturn(new DefaultImapUserProvider([
                ["id" => "conjoon",
                    "inbox_type" => "IMAP",
                    "inbox_port" => 993,
                    "inbox_ssl" => true,
                    "outbox_address" => "a.b.c",
                    "outbox_port" => 993,
                    "outbox_ssl" => true,
                    "root" => ["INBOX"],
                    "match" => ["/conjoon$/mi"]
                ]
            ]));

        return $app;
    }
}