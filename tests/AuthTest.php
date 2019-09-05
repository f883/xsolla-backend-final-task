<?php

use Library\App\LibraryRoute;
use Slim\Http\Environment;
use Slim\Http\Request;
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

    public function testUsersGet() {
        $env = Environment::mock([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/api/users/12',
            ]);
        $req = Request::createFromEnvironment($env);
        $this->app->getContainer()['request'] = $req;

        // tells Slim to run the app instance and instead of return 
        // HTTP headers and body of the response it should return an object.
        $response = $this->app->run(true); 

        $this->assertSame($response->getStatusCode(), 200);
        $this->assertSame((string)$response->getBody(), "[23,22,21,14,8]");
    } 
}