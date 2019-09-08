<?php

use Slim\Http\RequestBody;
use Slim\Http\UploadedFile;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Uri;
use Slim\Http\Headers;
use PHPUnit\Framework\TestCase;
class AuthTestCase extends TestCase
{
    protected $app;
    public function setUp()
    {
        $container = new \Slim\Container();
        require __DIR__ . '/../Dependencies.php';

        $this->app = new \Slim\App($container);
        (new Router())->commit($this->app);
    }

    public function testRegister() {
        $env = Environment::mock([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI'    => '/api/auth/register',
            ]);
        $req = Request::createFromEnvironment($env);

        $data = (object)["name" => 'test', 'password' => 'test'];

        $req = $req->withParsedBody($data);
        $this->app->getContainer()['request'] = $req;
        $response = $this->app->run(true);
        $this->assertSame($response->getStatusCode(), 500);
        $this->assertSame((string)$response->getBody(), "Hello, Todo");
    } 
}