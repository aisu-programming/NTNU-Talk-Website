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
    $url = $configs['referer'] . "profile.php";
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

            case 'uploadImage':
                if (is_invalid('link')) {
                    $aResult['error'] = "Missing arguments!";
                }
                // Invalid link (Somebody attacks me)
                else if (strlen($_POST['link']) > 40 || strpos($_POST['link'], "https://i.imgur.com/") !== 0) {
                    if ($configs['debug'])
                        $aResult['error'] = "Invalid link.";
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

                    $sql_result = $db->query(sqlcmd_updateAvatar($_SESSION['username'], $_POST['link']));

                    // Query failed
                    if ($sql_result === FALSE) {
                        header($_SERVER['SERVER_PROTOCOL'] . " 501");
                        $aResult['error'] = $db->error;
                    }
                    else {
                        header($_SERVER['SERVER_PROTOCOL'] . " 200");
                        $aResult['result'] = "Upload image succeed!";
                    }

                    // Close the connection
                    $db->close();
                }
                break;

            case 'follow':
                if (is_invalid('userId')) {
                    $aResult['error'] = "Missing arguments!";
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

                    // Check target user is exist
                    $sql_result = $db->query(sqlcmd_checkUserExist($_POST['userId']));

                    // Query failed
                    if ($sql_result === FALSE) {
                        header($_SERVER['SERVER_PROTOCOL'] . " 501");
                        $aResult['error'] = $db->error;
                    }
                    // Target user not found
                    else if ($sql_result->num_rows === 0) {
                        header($_SERVER['SERVER_PROTOCOL'] . " 403");
                        $aResult['error'] = "Target user does not exist.";
                    }
                    // Bug
                    else if ($sql_result->num_rows > 1) {
                        header($_SERVER['SERVER_PROTOCOL'] . " 403");
                        $aResult['error'] = "This is a bug. Please report.";
                    }
                    else {

                        $sql_result = $db->query(sqlcmd_checkRelation($_SESSION['user_id'], $_POST['userId']));
    
                        // Query failed
                        if ($sql_result === FALSE) {
                            header($_SERVER['SERVER_PROTOCOL'] . " 501");
                            $aResult['error'] = $db->error;
                        }
                        // Haven't create relation table between the user and the target user
                        else if ($sql_result->num_rows === 0) {

                            $sql_result = $db->query(sqlcmd_addRelationAndFollow($_SESSION['user_id'], $_POST['userId']));

                            if ($sql_result === FALSE) {
                                header($_SERVER['SERVER_PROTOCOL'] . " 501");
                                $aResult['error'] = $db->error;
                            }
                            else {
                                header($_SERVER['SERVER_PROTOCOL'] . " 200");
                                $aResult['result'] = "Follow succeed!";
                            }
                        }
                        else if ($sql_result->num_rows === 1) {

                            $sql_result = $db->query(sqlcmd_follow($_SESSION['user_id'], $_POST['userId']));

                            // Query failed
                            if ($sql_result === FALSE) {
                                header($_SERVER['SERVER_PROTOCOL'] . " 501");
                                $aResult['error'] = $db->error;
                            }
                            else {
                                header($_SERVER['SERVER_PROTOCOL'] . " 200");
                                $aResult['result'] = "Follow succeed!";
                            }
                        }
                        // Bug
                        else {
                            header($_SERVER['SERVER_PROTOCOL'] . " 403");
                            $aResult['error'] = "This is a bug. Please report.";
                        }
                    }

                    // Close the connection
                    $db->close();
                }
                break;

                case 'cancelFollow':
                    if (is_invalid('userId')) {
                        $aResult['error'] = "Missing arguments!";
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
    
                        // Query to check that relation exist
                        $sql_result = $db->query(sqlcmd_checkRelation($_SESSION['user_id'], $_POST['userId']));
    
                        // Query failed
                        if ($sql_result === FALSE) {
                            header($_SERVER['SERVER_PROTOCOL'] . " 501");
                            $aResult['error'] = $db->error;
                        }
                        // Bug
                        else if ($sql_result->num_rows !== 1) {
                            header($_SERVER['SERVER_PROTOCOL'] . " 403");
                            $aResult['error'] = "This is a bug. Please report.";
                        }
                        else {

                            $sql_result = $db->query(sqlcmd_cancelFollow($_SESSION['user_id'], $_POST['userId']));

                            // Query failed
                            if ($sql_result === FALSE) {
                                header($_SERVER['SERVER_PROTOCOL'] . " 501");
                                $aResult['error'] = $db->error;
                            }
                            else {
                                header($_SERVER['SERVER_PROTOCOL'] . " 200");
                                $aResult['result'] = "Cancel follow succeed!";
                            }
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