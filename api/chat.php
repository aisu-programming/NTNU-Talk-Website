<?php

    declare(strict_types=1);
    $configs = include($_SERVER['DOCUMENT_ROOT'] . "/api/config/config.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/api/lib/sqlcmd.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/api/lib/jwt.php");

    function is_invalid(string $argsName) : bool {
        if (!isset($_POST[$argsName]) || $_POST[$argsName] === '') return true;
        else return false;
    }

    session_start();

    $aResult = array();
    header($_SERVER['SERVER_PROTOCOL'] . " 403");
    header("Content-Type: application/json");

    // Check referer
    $url = $configs['referer'] . "chat.php";
    if (strpos($_SERVER['HTTP_REFERER'], $url) !== 0) {
        if ($configs['debug'])
            $aResult['error'] = "Unauthorized referer.";
    }
    // Check random number
    else if ($_POST['r'] != $_SESSION['randomNumber']) {
        if ($configs['debug'])
            $aResult['error'] = "Wrong random number.";
    }
    // Check data has action value
    else if (is_invalid('action')) {
        if ($configs['debug'])
            $aResult['error'] = "Missing action.";
    }
    else {

        switch($_POST['action']) {

            case 'sendMessage':
                if (is_invalid('target_id') || is_invalid('message')) {
                    $aResult['error'] = "Missing arguments!";
                }
                // Invalid message (Cannot send to user himself/herself)
                else if ($_POST['target_id'] === $_SESSION['user_id']) {
                    if ($configs['debug'])
                        $aResult['error'] = "You can't send message to yourself.";
                }
                // Invalid target_id
                else if (strlen($_POST['target_id']) != 9) {
                    if ($configs['debug'])
                        $aResult['error'] = "Invalid target User ID.";
                }
                // Message too long
                else if (strlen($_POST['message']) > 150) {
                    if ($configs['debug'])
                        $aResult['error'] = "Message too long!";
                }
                else {
                    $db = mysqli_connect($configs['host'],
                                         $configs['username'],
                                         $configs['password'],
                                         $configs['dbname']);

                    // Database connect failed
                    if (!$db) {
                        header($_SERVER['SERVER_PROTOCOL'] . " 501");
                        $aResult['error'] = "Debugging errno: " . mysqli_connect_errno();
                        break;
                    }

                    $sql_result = $db->query(sqlcmd_addMessage($_SESSION['user_id'], $_POST['target_id'], $_POST['message']));

                    // Query failed
                    if ($sql_result === FALSE) {
                        header($_SERVER['SERVER_PROTOCOL'] . " 501");
                        $aResult['error'] = $db->error;
                    }
                    else {
                        header($_SERVER['SERVER_PROTOCOL'] . " 200");
                        $aResult['result'] = "Message sent succeed!";
                    }

                    // Close the connection
                    $db->close();
                }
                break;

            default:
                if ($configs['debug'])
                    $aResult['error'] = "Nonexistent action.";
        }
    }
    
    echo json_encode($aResult);
?>