<?php

use Slim\Http\RequestBody;
use Slim\Http\UploadedFile;
// use Slim\Http\;
// use Slim\Http\;
// use Slim\Http\;
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

    // public function requestFactory()
    // {
    //     $env = Environment::mock();
    //     $uri = Uri::createFromString('https://example.com:443/foo/bar?abc=123');
    //     $headers = Headers::createFromEnvironment($env);
    //     $cookies = [
    //         'user' => 'john',
    //         'id' => '123',
    //     ];
    //     $serverParams = $env->all();
    //     $body = new RequestBody();
    //     $uploadedFiles = UploadedFile::createFromEnvironment($env);
    //     $request = new Request('GET', $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);
    //     return $request;
    // }

    // public function testRegister() {
        // $env = Environment::mock([
        //     'REQUEST_METHOD' => 'post',
        //     'REQUEST_URI'    => '/api/auth/register',
        //     'CONTENT' => [
        //         "name" => "qwe2",
        //         "password" => "qwe"
        //         ]    
        //     ]);
        // $req = Request::createFromEnvironment($env);
        // $this->app->getContainer()['request'] = $req;
        // $response = $this->app->run(true); 

        // // $this->assertSame($response->getStatusCode(), 200);
        // $this->assertSame((string)$response->getBody(), '{"ok": "true"}');
    // } 

    public function testRegister() {
        // create our http client (Guzzle)
        $client = new Client('http://localhost:8000', array(
            'request.options' => array(
                'exceptions' => false,
            )
        ));

        $nickname = 'ObjectOrienter'.rand(0, 999);
        $data = array(
            'nickname' => $nickname,
            'avatarNumber' => 5,
            'tagLine' => 'a test dev!'
        );

        $request = $client->post('/api/programmers', null, json_encode($data));
        $response = $request->send();
    } 

    /*
//setup environment vals to create request
$env = Environment::mock();
$uri = Uri::createFromString('/1.0/' . $relativeLink);
$headers = Headers::createFromEnvironment($env);
$cookies = [];
$serverParams = $env->all();
$body = new RequestBody();
$uploadedFiles = UploadedFile::createFromEnvironment($env);
$request = new Request('GET', $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);

//write request data
$request->write(json_encode([ 'key' => 'val' ]));
$request->getBody()->rewind();
//set method & content type
$request = $request->withHeader('Content-Type', 'application/json');
$request = $request->withMethod('POST');

//execute request
$app = new App();
$resOut = $app($request, new Response());
$resOut->getBody()->rewind();

$this->assertEquals('full response text', $resOut->getBody()->getContents());
    */
}