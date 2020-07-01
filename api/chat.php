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

            case 'getAllChatRoom':
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

                // Query for the chat target user's information
                $sql_result = $db->query(sqlcmd_getAllChatRoom($_SESSION['user_id']));
                
                // Query failed// Query failed
                if ($sql_result === FALSE) {
                    header($_SERVER['SERVER_PROTOCOL'] . " 501");
                    $aResult['error'] = $db->error;
                }
                else {
                    $aResult['chat_rooms'] = array();
                    while ($row = $sql_result->fetch_assoc()) {
                        $chat_rooms = array('uid'=>$row['uid'],
                                            'nickname'=>stringDecode($row['nickname']),
                                            'avatar'=>$row['avatar'],
                                            'send_by_me'=>$row['send_by_me'],
                                            'preview'=>stringDecode($row['preview']));
                        array_push($aResult['chat_rooms'], json_encode($chat_rooms));
                    }
                    header($_SERVER['SERVER_PROTOCOL'] . " 200");
                    $aResult['result'] = "Succeed!";
                }

                // Close the connection
                $db->close();
                break;

            case 'getMessage':
                if (is_invalid('target_id')) {
                    $aResult['error'] = "Missing arguments!";
                }
                // Invalid target_id
                else if (strlen($_POST['target_id']) != 9) {
                    if ($configs['debug'])
                        $aResult['error'] = "Invalid target User ID.";
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

                    // Query for the chat target user's information
                    $sql_result = $db->query(sqlcmd_getProfile($_POST['target_id']));

                    // Query failed// Query failed
                    if ($sql_result === FALSE) {
                        header($_SERVER['SERVER_PROTOCOL'] . " 501");
                        $aResult['error'] = $db->error;
                    }
                    // Target user does not exist
                    else if ($sql_result->num_rows === 0) {
                        if ($configs['debug'])
                            $aResult['error'] = "Target user doesn't exist.";
                    }
                    // Bug
                    else if ($sql_result->num_rows !== 1) {
                        if ($configs['debug'])
                            $aResult['error'] = "This is a bug. Please report.";
                    }
                    else {
                        $row = $sql_result->fetch_assoc();
                        $aResult['nickname'] = $row['nickname'];

                        // Query for the chat content with target user
                        $sql_result = $db->query(sqlcmd_getMessage($_SESSION['user_id'], $_POST['target_id']));
                        // Query failed
                        if ($sql_result === FALSE) {
                            header($_SERVER['SERVER_PROTOCOL'] . " 501");
                            $aResult['error'] = "Debugging errno: " . mysqli_connect_errno();
                        }
                        else {
                            $aResult['messages'] = array();
                            while ($row = $sql_result->fetch_assoc()) {
                                $message = array('send_by_me'=>$row['send_by_me'],
                                                 'time'=>$row['time'],
                                                 'content'=>stringDecode($row['content']));
                                array_push($aResult['messages'], json_encode($message));
                            }
                            header($_SERVER['SERVER_PROTOCOL'] . " 200");
                            $aResult['result'] = "Succeed!";
                        }
                    }

                    // Close the connection
                    $db->close();
                }
                break;

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