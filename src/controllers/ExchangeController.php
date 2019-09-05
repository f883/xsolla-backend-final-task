<?php 

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ExchangeController{
    private $adminModel;
    private $userModel;
    private $commonModel;

    public function __construct(AdminModel $adminModel, CommonModel $commonModel, UserModel $userModel){
        $this->adminModel = $adminModel;
        $this->commonModel = $commonModel;
        $this->userModel = $userModel;
    }
    
    public function getStatus(Request $request, Response $response, $args)
    {
        $res = [];
        $status = $this->commonModel->getExchangeStatus();

        $res = [
            'ok' => 'true',
            'data' => $status
        ];

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200);
    }
    
    public function setFee(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $respCode = 200;
        $res = [];

        if (empty($data['fee'])){
            $res = [
                'error' => 'Fee not set.'
            ];
            $respCode = 400;
        }
        else{
            $fee = $data['fee'];
            if (!$this->checkFee($fee)){
                $res = [
                    'error' => 'Wrong fee value.'
                ];
                $respCode = 400;
            }
            else{
                if ($this->adminModel->setExchangeFee($fee)){
                    $res = ['ok' => 'true'];
                }
                else{
                    $res = ['ok' => 'false'];
                    $respCode = 500;
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function getBalance(Request $request, Response $response, $args)
    {
        $res = [
            'ok' => 'true',
            'balance' => $this->adminModel->getExchangeBalance()
        ];

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200);
    }

    public function getEarn(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $respCode = 200;
        $res = [];

        if (empty($data['from_date'])){
            $res = ['error' => 'Field [from_date] not set.'];
            $respCode = 400;
        }
        else{
            if (empty($data['to_date'])){
                $res = ['error' => 'Field [to_date] not set.'];
                $respCode = 400;
            }
            else{
                try{
                    $fromDate = DateTime::createFromFormat('d-m-Y', $data['from_date']);// '15-02-2009');
                    $toDate = DateTime::createFromFormat('d-m-Y', $data['to_date']);

                    $res = [
                        'earn' => $this->adminModel->getEarn($fromDate, $toDate),
                        'from_date' => $fromDate->format('d-m-Y'),
                        'to_date' => $toDate->format('d-m-Y')
                    ];
                }
                catch(Exception $ex){
                    $res = ['error' => $ex->getMessage()];
                    $respCode = 400;    
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function depositMoney(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $respCode = 200;
        $res = [];
        $userId = $args['id'];

        if (empty($data->value)){
            $res = ['error' => 'Field [value] not set.'];
            $respCode = 400;
        }
        else{
            $value = $data->value;
            if (!$this->checkMoneyValue($value)){
                $res = ['error' => 'Wrong [value] value.'];
                $respCode = 400;
            }
            else{
                if ($this->checkIntValue($userId)){
                    try{
                        $this->adminModel->depositMoney($userId, $value);
                        $res = ['ok' => 'true'];
                    }
                    catch(Exception $ex){
                        $res = ['error' => $ex->getMessage()];
                        $respCode = 400;
                    }
                }
                else{
                    $res = ['error' => 'Wrong user id.'];
                    $respCode = 400;
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    public function withdrawMoney(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $respCode = 200;
        $res = [];
        $userId = $args['id'];

        if (empty($data['value'])){
            $res = ['error' => 'Field [value] not set.'];
            $respCode = 400;
        }
        else{
            $value = $data['value'];
            if (!$this->checkMoneyValue($value)){
                $res = ['error' => 'Wrong [value] value.'];
                $respCode = 400;
            }
            else{
                if ($this->checkIntValue($userId)){
                    try{
                        $this->adminModel->withdrawMoney($userId, $value);
                        $res = ['ok' => 'true'];
                    }
                    catch(Exception $ex){
                        $res = ['error' => $ex->getMessage()];
                        $respCode = 400;
                    }
                }
                else{
                    $res = ['error' => 'Wrong user id.'];
                    $respCode = 400;
                }
            }
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus($respCode);
    }

    private function checkFee($value){
        if (!is_numeric($value)){
            return false;
        }
        else{
            if ((double)$value < 0){
                return false;
            }
            else{
                if ((double)$value > 1){
                    return false;
                }
            }
        }
        return true;
    }

    private function checkMoneyValue($value){
        if (!is_numeric($value)){
            return false;
        }
        else{
            if ((double)$value <= 0){
                return false;
            }
        }
        return true;
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