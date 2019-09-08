<?php

use \Firebase\JWT\JWT;

class AuthInteractor{
    public static $ACCESS_TOKEN = 'access_token';
    public static $REFRESH_TOKEN = 'refresh_token';
    private $repository;
    
    public function __construct(Repository $repository){
        $this->repository = $repository;
    }

    // Регистрация
    public function register($username, $password){
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $user = $this->repository->getUserByName($username);

        if (!empty($user)){
            throw new Exception('User with name [' . $username . '] already exists.');
        }     

        $user = new User();
        $user->setName($username);
        $user->setPasswordHash($hash);
        $user->setRole($this->repository->getOrCreateUserRoleByValue(UserRole::$USER));

        $this->repository->saveEntity($userRole);
        $this->repository->saveEntity($user);
        return $user->getId();
    } 

    // Войти в стистему
    public function login($username, $password){
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $user = $this->repository->getUserByName($username);

        if (empty($user)){
            throw new Exception('User with name [' . $username . '] not found.');
        }     
        return $user->getId();
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
            "type" => AuthInteractor::$ACCESS_TOKEN,
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
            "type" => AuthInteractor::$REFRESH_TOKEN,
            "userId" => $userId,
            "iss" => $issuer,
            "aud" => $audience,
            "iat" => $timestampCreated,
            "nbf" => $timestampCreated,
            "exp" => $timestampRefreshExpires
        );
        
        $salt = $this->generateRandomString(40);
        $tokens[AuthInteractor::$ACCESS_TOKEN] = JWT::encode($accessToken, $salt);
        $tokens[AuthInteractor::$REFRESH_TOKEN] = JWT::encode($refreshToken, $salt);
        
        $user = $this->repository->getUserById($userId);
        $user->setTokenSalt($salt);
        $user->setAccessTokenHash($this->hash($tokens[AuthInteractor::$ACCESS_TOKEN]));
        $user->setRefreshTokenHash($this->hash($tokens[AuthInteractor::$REFRESH_TOKEN]));
        $this->repository->saveEntity($user);

        return $tokens;
    }

    public function validateAccessToken($jwt){
        $user = $this->repository->getUserByAccessToken($this->hash($jwt));
        if (empty($user)){
            throw new Exception('Invalid access token.');
        }
        $salt = $user->getTokenSalt();
        $decoded = JWT::decode($jwt, $salt, array('HS256'));
        if ($decoded->type === AuthInteractor::$ACCESS_TOKEN){
            return $user->getId();
        }
        else{
            throw new Exception('Invalid token type [' . $decoded->type . '].');
        }
    }

    public function validateRefreshToken($jwt){
        $user = $this->repository->getUserByRefreshToken($this->hash($jwt));
        if (empty($user)){
            throw new Exception('Invalid access token.');
        }
        $salt = $user->getTokenSalt();
        $decoded = JWT::decode($jwt, $salt, array('HS256'));
        if ($decoded->type === AuthInteractor::$REFRESH_TOKEN){
            return $user->getId();
        }
        else{
            throw new Exception('Invalid token type [' . $decoded->type . '].');
        }
    }

    public function invalidateTokens($userId){
        $user = $this->repository->getUserById($userId);
        if (empty($user)){
            throw new Exception('User not found.');
        }
        
        $user->setAccessTokenHash(null);
        $user->setRefreshTokenHash(null);
        $this->repository->saveEntity($user);
        return true;
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