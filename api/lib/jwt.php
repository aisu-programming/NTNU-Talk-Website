<?php

    require $_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php";
    use \Firebase\JWT\JWT;

    function jwt_create(string $user_id) : string {

        $configs = include($_SERVER['DOCUMENT_ROOT'] . "/api/config/config.php");

        $payload = array(
            'iss' => $configs['isser'],
            'iat' => $_SERVER['REQUEST_TIME'],
            'exp' => $_SERVER['REQUEST_TIME'] + $configs['exp'],
            'user_id' => $user_id
        );

        try {
            $jwt = JWT::encode($payload, $configs['key'], 'HS512');
            setcookie("JWT", $jwt, time() + $configs['exp'], '/');
            $_SESSION['user_id'] = $user_id;
            return $jwt;
        }
        catch (UnexpectedValueException $e) {
            unset($_COOKIE['JWT']);
            unset($_SESSION['user_id']);
            return "Error: " . $e->getMessage();
        }
    }

    function jwt_decode() : array {

        $configs = include($_SERVER['DOCUMENT_ROOT'] . "/api/config/config.php");

        JWT::$leeway = 60;
        $decoded = JWT::decode($_COOKIE['JWT'], $configs['key'], array('HS512'));

        return (array) $decoded;
    }

    function jwt_setUserID() : bool {

        try {
            if (isset($_SESSION['user_id'])) unset($_SESSION['user_id']);
            $_SESSION['user_id'] = jwt_decode()['user_id'];
            return true;
        }
        catch (Exception $e) {
            // Clear both JWT cookie and SESSION if there's an error
            unset($_COOKIE['JWT']);
            unset($_SESSION['user_id']);
            return false;
        }
    }

?>