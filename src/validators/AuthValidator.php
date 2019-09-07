<?php

class AuthValidator{
    private $auth;
    private $entityManager;

    public static $allowedPaths = [
        '/api/auth/register',
        '/api/auth/login',
        '/api/exchange',
        '/api/top/users',
        '/api/auth/updatetoken'
    ];

    public function __construct(AuthInteractor $auth){
        $this->auth = $auth;
    }

    public function dispatch($request, $response, $next){
        $path = $request->getUri()->getPath();
        $response = $response->withHeader('Content-Type', 'application/json');

        // Доступ без наличия токена
        if (in_array($path, AuthValidator::$allowedPaths)){
            $response = $next($request, $response);
            return $response;
        }
        else{
            $data = $request->getParsedBody();
            if (!empty($data[Auth::$ACCESS_TOKEN])){
                $accessToken = $data[Auth::$ACCESS_TOKEN];
                if (empty($data['data'])){
                    $data['data'] = [];
                }
                $requestBody = $data['data'];

                try{
                    $requestBody['user_id'] = $this->auth->validateAccessToken($accessToken);
                    $request = $request->withParsedBody($requestBody);
                    $response = $next($request, $response);
                }
                catch(Exception $ex){
                    $ex->getMessage();
                    $res = ['error' => $ex->getMessage()];
                    $response->write(json_encode($res));
                }
                return $response;
            }
            else{
                $res = ['error' => 'Field [' . Auth::$ACCESS_TOKEN . '] not found.'];
                $response->write(json_encode($res));
                return $response;
            }
        }
    }
}