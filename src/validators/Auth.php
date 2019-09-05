<?php

use \Firebase\JWT\JWT;

class Auth{
    public static $ACCESS_TOKEN = 'access_token';
    public static $REFRESH_TOKEN = 'refresh_token';
    private $entityManager;
    
    public function __construct(Doctrine\ORM\EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    public function generateTokens(string $userId){
        $tokens = [];
        $issuer = 'http://example.org';
        $audience = 'http://example.com';

        $dateNow = new DateTime();
        $timestampCreated = strtotime($dateNow->format('Y-m-d'));
        
        $dateAccessExpires = new DateTime();
        $dateAccessExpires->add(new DateInterval('P1D'));
        $timestampAccessExpires = strtotime($dateAccessExpires->format('Y-m-d'));
        
        $accessToken = array(
            "type" => Auth::$ACCESS_TOKEN,
            "userId" => $userId,
            "iss" => $issuer,
            "aud" => $audience,
            "iat" => $timestampCreated,
            "nbf" => $timestampCreated,
            "exp" => $timestampAccessExpires
        );

        $dateRefreshExpires = new DateTime();
        $dateRefreshExpires->add(new DateInterval('P1M'));
        $timestampRefreshExpires = strtotime($dateRefreshExpires->format('Y-m-d'));

        $refreshToken = array(
            "type" => Auth::$REFRESH_TOKEN,
            "userId" => $userId,
            "iss" => $issuer,
            "aud" => $audience,
            "iat" => $timestampCreated,
            "nbf" => $timestampCreated,
            "exp" => $timestampRefreshExpires
        );
        
        $salt = $this->generateRandomString(40);
        $tokens[Auth::$ACCESS_TOKEN] = JWT::encode($accessToken, $salt);
        $tokens[Auth::$REFRESH_TOKEN] = JWT::encode($refreshToken, $salt);
        
        $user = $this->getUserById($userId);
        $user->setTokenSalt($salt);
        $user->setAccessTokenHash($this->hash($tokens[Auth::$ACCESS_TOKEN]));
        $user->setRefreshTokenHash($this->hash($tokens[Auth::$REFRESH_TOKEN]));
        $this->saveObject($user);

        return $tokens;
    }

    public function validateAccessToken($jwt){
        $user = $this->getUserByAccessToken($jwt);
        if (empty($user)){
            throw new Exception('Invalid access token.');
        }
        $salt = $user->getTokenSalt();
        $decoded = JWT::decode($jwt, $salt, array('HS256'));
        if ($decoded->type === Auth::$ACCESS_TOKEN){
            return $user->getId();
        }
        else{
            throw new Exception('Invalid token type [' . $decoded->type . '].');
        }
    }

    public function validateRefreshToken($jwt){
        $user = $this->getUserByRefreshToken($jwt);
        if (empty($user)){
            throw new Exception('Invalid access token.');
        }
        $salt = $user->getTokenSalt();
        $decoded = JWT::decode($jwt, $salt, array('HS256'));
        if ($decoded->type === Auth::$REFRESH_TOKEN){
            return $user->getId();
        }
        else{
            throw new Exception('Invalid token type [' . $decoded->type . '].');
        }
    }

    public function invalidateTokens($userId){
        $user = $this->getUserById($userId);
        if (empty($user)){
            throw new Exception('User not found.');
        }
        
        $user->setAccessTokenHash(null);
        $user->setRefreshTokenHash(null);
        $this->saveObject($user);
        return true;
    }

    private function saveObject($obj){
        $this->entityManager->persist($obj);
        $this->entityManager->flush();
    }

    private function getUserById($id){
        return $this->entityManager->getRepository('User')
        ->findOneBy(
            ['id' => $id]
        );
    }

    private function getUserByAccessToken($token){
        return $this->entityManager->getRepository('User')
        ->findOneBy(
            ['accessTokenHash' => $this->hash($token)]
        );
    }

    private function getUserByRefreshToken($token){
        return $this->entityManager->getRepository('User')
        ->findOneBy(
            ['refreshTokenHash' => $this->hash($token)]
        );
    }

    private function hash($value){
        return hash('sha512', $value);
    }

    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}