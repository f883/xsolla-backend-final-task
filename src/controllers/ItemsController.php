<?php 

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ItemsController{
    private $adminModel;
    private $userModel;
    private $commonModel;

    public function __construct(AdminModel $adminModel, CommonModel $commonModel, UserModel $userModel){
        $this->adminModel = $adminModel;
        $this->commonModel = $commonModel;
        $this->userModel = $userModel;
    }

    public function getItemTypes(Request $request, Response $response, $args)
    {
        $respCode = 200;
        $res = [
            'types' => $this->commonModel->getItemTypesList()
        ];

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function getItemType(Request $request, Response $response, $args)
    {
        $respCode = 200;
        $res = [];
        
        $id = $args['id'];
        if (!$this->checkIntValue($id)){
            $res = ['error' => 'Wrong item type id.'];
            $respCode = 400;
        }
        else{
            try{
                $res = ['item_type' => $this->commonModel->getItemType($id)];  
            }
            catch(Exception $ex){
                $res = ['error' => $ex->getMessage()];
                $respCode = 400;
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function getItem(Request $request, Response $response, $args)
    {
        $respCode = 200;
        $res = [];
        
        $id = $args['id'];
        if (!$this->checkIntValue($id)){
            $res = ['error' => 'Wrong item type id.'];
            $respCode = 400;
        }
        else{
            try{
                $res = ['item_type' => $this->commonModel->getItem($id)];  
            }
            catch(Exception $ex){
                $res = ['error' => $ex->getMessage()];
                $respCode = 400;
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function getItems(Request $request, Response $response, $args)
    {
        $respCode = 200;
        $res = [
            'items' => $this->commonModel->getItemsList()
        ];

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function addItemType(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $respCode = 200;
        $res = [];

        // print_r($data);

        if (empty($data['name'])){
            $res = [
                'error' => 'Field [name] not set.'
            ];
            $respCode = 400;
        }
        else{
            try{
                $this->adminModel->createItemType($data['name']);
                $res = ['ok' => 'true'];
            }
            catch(Exception $ex){
                $res = ['error' => $ex->getMessage()];
                $respCode = 400;
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