<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UsersPresenter{
    private $usersInteractor;

    public function __construct(UsersInteractor $usersInteractor){
        $this->usersInteractor = $usersInteractor;
    }

        
    public function addItemToUser(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $respCode = 200;
        
        $res = [];
        $userId = $args['id'];
        if (empty($data['item_type_id'])){
            $respCode = 400;
            $res = ['error' => 'Field [item_type_id] not found.'];
        }
        else {
            if (!$this->checkIntValue($data['item_type_id'])){
                $respCode = 400;
                $res = ['error' => 'Wrong [item_type_id] value.'];
            }
            else{
                if (!$this->checkIntValue($userId)){
                    $respCode = 400;
                    $res = ['error' => 'Wrong user id.'];
                }
                else{
                    try{
                        $success = $this->usersInteractor->depositItem($data['user_id'], $userId, $data['item_type_id']);
                        if ($success){
                            $res = ['ok' => 'true'];
                        }
                        else{
                            $res = ['ok' => 'false'];
                        }
                    }
                    catch(Exception $ex){
                        $respCode = 400;
                        $res = ['error' => $ex->getMessage()];
                    }
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }


    public function getUser(Request $request, Response $response, $args)
    {
        $res = [];
        $respCode = 200;

        if (!is_numeric($args['id'])){
            $respCode = 400;
            $res = ['error' => 'Wrong user id.'];
        }
        else{
            try{
                $res = $this->usersInteractor->getUserInfo($args['id']);
            }
            catch(Exception $ex){
                $respCode = 400;
                $res = ['error' => $ex->getMessage()];
            }
        }
        
        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function getUserHistory(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $respCode = 200;
        $res = [];

        $filter = [];
        if (!empty($data['filter'])){
            $filter = $data['filter'];
        }

        $userId = $args['id'];
        if (!$this->checkIntValue($userId)){
            $respCode = 400;
            $res = ['error' => 'Wrong user id.'];
        }
        else{
            try{

                $list = $this->usersInteractor->getUserHistory($userId, $filter);
                $res = [
                    'ok' => 'true',
                    'orders' => $list
                ];
            }
            catch(Exception $ex){
                $respCode = 400;
                $res = ['error' => $ex->getMessage()];
            }
        }
        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    private function checkIntValue($value){
        if (!is_numeric($value)){
            return false;
        }
        else{
            if ((int)$value < 0){
                return false;
            }
        }
        return true;
    }
}