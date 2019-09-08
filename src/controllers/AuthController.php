<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthController{
    private $authInteractor;

    public function __construct(AuthInteractor $authInteractor){
        $this->authInteractor = $authInteractor;
    }

    public function register(Request $request, Response $response, $args){
        $data = $request->getParsedBody();
        
        $respCode = 200;
        $res = [];
        if (!empty($data['name']) && !empty($data['password'])){
            $userId = '';
            try{
                $userId = $this->authInteractor->register($data['name'], $data['password']);
                $res = [
                    'ok' => 'true'
                ];
            }
            catch (Exception $ex){
                $res = [
                    'error' => $ex->getMessage()
                ];
                $respCode = 400;
            }
        }
        else{
            if (is_null($data['name'])){
                $res = [
                    'error' => 'Field [name] not set.'
                ];
            }
            else{
                $res = [
                    'error' => 'Field [password] not set.'
                ];
            }
            $respCode = 400;
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function login(Request $request, Response $response, $args){
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;

        if (!empty($data['name']) && !empty($data['password'])){
            $userId = '';
            try{
                $userId = $this->authInteractor->login($data['name'], $data['password']);
            }
            catch (Exception $ex){
                $res = [
                    'error' => $ex->getMessage()
                ];
                $respCode = 400;
            }
            $tokens = $this->authInteractor->generateTokens($userId);
            $res = [
                'ok' => 'true',
                'data' => $tokens
            ];
        }
        else{
            if (is_null($data['name'])){
                $res = [
                    'error' => 'Field [name] not set.'
                ];
            }
            else{
                $res = [
                    'error' => 'Field [password] not set.'
                ];
            }
            $respCode = 400;
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    // Уничтожение выданных токенов
    public function logout(Request $request, Response $response, $args){
        $data = $request->getParsedBody();
        $res = [];
        $respCode = 200;

        if (empty($data['user_id'])){
            $res = ['error' => 'Field [user_id] not found.'];
            $respCode = 400;
        }
        else{
            try{
                if ($this->authInteractor->invalidateTokens($data['user_id'])){
                    $res = ['ok' => 'true'];
                }
                else{
                    $res = ['error' => 'Undefined exception.'];
                    $respCode = 400;
                }
            }
            catch(Exception $ex){
                $res = ['error' => $ex->getMessage()];
                $respCode = 400;
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    // Получение нового access token через refresh token
    public function updateToken(Request $request, Response $response, $args){
        $data = $request->getParsedBody();
        $respCode = 200;
        $res = [];

        if (empty($data[Auth::$REFRESH_TOKEN])){
            $res = ['error' => 'Field [' . Auth::$REFRESH_TOKEN . '] not found.'];
        }
        
        $authRes = [];
        try{
            $userId = $this->authInteractor->validateRefreshToken($data[Auth::$REFRESH_TOKEN]);
            if (!empty($userId)){
                $tokens = $this->auth->generateTokens($userId);
                $res = [
                    'ok' => 'true',
                    'data' => $tokens
                ];
            }
            else{
                $res = ['error' => 'Undefined error.'];
            }
        }
        catch(Exception $ex){
            $res = ['error' => $ex->getMessage()];
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }
}