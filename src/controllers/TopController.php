<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class TopController{
    private $topInteractor;

    public function __construct(TopInteractor $topInteractor){
        $this->topInteractor = $topInteractor;
    }

    public function getTopItems(Request $request, Response $response, $args)
    {
        $items = $this->topInteractor->getTopSallingItems();

        $res = [
            'ok' => 'true',
            'items' => $items
        ];

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200);
    }

    // по количеству денег, по количеству товаров
    public function getTopUsers(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $res = [];

        if (empty($data['filter'])){
            $res = ['error' => 'Filter not set.'];
        }
        else{
            $users = $this->topInteractor->getTopUsers($data['filter']);
    
            $res = [
                'ok' => 'true',
                'users' => $users
            ];
        }

        $response->getBody()->write(json_encode($res));
        return $response->withStatus(200);
    }
}