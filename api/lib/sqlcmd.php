<?php

    function stringEncode(string $input) : string {
        return base64_encode($input);
    }

    function stringDecode(string $input) : string {
        return base64_decode($input);
    }

    // ------------------------------ ↓ Numbers ↓ ------------------------------ //

    function sqlcmd_createNumberTable() : string {
        return "CREATE TABLE number (
                    id INT(2) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                    name VARCHAR(20) NOT NULL UNIQUE,
                    value INT(6) UNSIGNED NOT NULL DEFAULT 0
                )";
    }

    function sqlcmd_addNumber(string $name) : string {
        return "INSERT INTO number (name) VALUES ('$name')";
    }

    function sqlcmd_getNumber(string $name) : string {
        return "SELECT value FROM number WHERE name = '$name'";
    }

    function sqlcmd_addNumberByOne(string $name) : string {
        return "UPDATE number SET value = value + 1 
                WHERE name = '$name'";
    }

    // ------------------------------ ↓ Users ↓ ------------------------------ //

    function sqlcmd_createUserTable() : string {
        return "CREATE TABLE user (
                  serial_no int unsigned NOT NULL AUTO_INCREMENT,
                  user_id char(9) NOT NULL,
                  password varchar(128) NOT NULL,
                  real_name varchar(30) DEFAULT NULL,
                  nickname varchar(50) NOT NULL,
                  gender int DEFAULT NULL,
                  signup_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  avatar varchar(31) NOT NULL DEFAULT 'https://i.imgur.com/57UTYCI.png',
                  PRIMARY KEY (serial_no),
                  UNIQUE KEY serial_no_UNIQUE (serial_no),
                  UNIQUE KEY id_UNIQUE (user_id)
                )";
    }

    function sqlcmd_checkUserExist(string $user_id) : string {

        return "SELECT user_id FROM user 
                WHERE user_id = '$user_id'";
    }
    
    function sqlcmd_addUser(string $user_id, string $password, string $nickname) : string {

        $sha512_pwd = hash('sha512', $password);
        $encode_nickname = stringEncode($nickname);

        return "INSERT INTO user (user_id, password, nickname) 
                VALUES ('$user_id', '$sha512_pwd', '$encode_nickname')";
    }
    
    function sqlcmd_getUser(string $user_id, string $password) : string {

        $sha512_pwd = hash('sha512', $password);

        return "SELECT user_id FROM user 
                WHERE user_id = '$user_id' && password = '$sha512_pwd'";
    }

    function sqlcmd_addUserLoginTurn(string $user_id) : string {

        return "UPDATE user SET login_turn = login_turn + 1 
                WHERE user_id = '$user_id'";
    }

    function sqlcmd_getUserLoginTurn(string $user_id) : string {

        return "SELECT login_turn FROM user WHERE user_id = '$user_id'";
    }
    
    // Not finished
    function sqlcmd_getProfile(string $user_id) {

        return "SELECT nickname, avatar FROM user 
                WHERE user_id = '$user_id'";
    }
    
    function sqlcmd_updateAvatar(string $user_id, string $link) : string {

        return "UPDATE user SET avatar = '$link' 
                WHERE user_id = '$user_id'";
    }

    // ------------------------------ ↓ Relations ↓ ------------------------------ //

    function sqlcmd_createRelationTable() : string {
        return "CREATE TABLE relation (
                    relation_id int unsigned NOT NULL AUTO_INCREMENT,
                    status int unsigned NOT NULL DEFAULT '0',
                    a_id varchar(9) NOT NULL,
                    b_id varchar(9) NOT NULL,
                    PRIMARY KEY (relation_id),
                    UNIQUE KEY relation_id_UNIQUE (relation_id),
                    KEY user_idx (a_id,b_id)
                )";
    }
    
    function sqlcmd_checkRelation(string $user_id, string $target_id) : string {
        return "SELECT * FROM relation
                WHERE (a_id = '$user_id' AND b_id = '$target_id')
                OR (a_id = '$target_id' AND b_id = '$user_id')";
    }

    function sqlcmd_createRelationAndFollow(string $user_id, string $target_id) : string {
        return "INSERT INTO relation (status, a_id, b_id) 
                VALUES (1, '$user_id', '$target_id')";
    }

    function sqlcmd_follow(string $user_id, string $target_id) : string {
        return "UPDATE relation SET status = (
                    CASE
                        WHEN (a_id = '$user_id' AND b_id = '$target_id' AND status = 0) THEN 1
                        WHEN (a_id = '$user_id' AND b_id = '$target_id' AND status = 2) THEN 3
                        WHEN (a_id = '$target_id' AND b_id = '$user_id' AND status = 0) THEN 2
                        WHEN (a_id = '$target_id' AND b_id = '$user_id' AND status = 1) THEN 3
                        ELSE status
                    END
                ) 
                WHERE (a_id = '$user_id' AND b_id = '$target_id')
                OR (a_id = '$target_id' AND b_id = '$user_id')";
    }

    function sqlcmd_cancelFollow(string $user_id, string $target_id) : string {
        return "UPDATE relation SET status = (
            CASE
                WHEN (a_id = '$user_id' AND b_id = '$target_id' AND status = 1) THEN 0
                WHEN (a_id = '$user_id' AND b_id = '$target_id' AND status = 3) THEN 2
                WHEN (a_id = '$target_id' AND b_id = '$user_id' AND status = 2) THEN 0
                WHEN (a_id = '$target_id' AND b_id = '$user_id' AND status = 3) THEN 1
                ELSE status
            END
        ) 
        WHERE (a_id = '$user_id' AND b_id = '$target_id')
        OR (a_id = '$target_id' AND b_id = '$user_id')";
    }

    // ------------------------------ ↓ Comments ↓ ------------------------------ //
    
    function sqlcmd_createCommentTable() : string {
        return "CREATE TABLE comment (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
                    user_id VARCHAR(40) NOT NULL,
                    date TIMESTAMP, -- NOT NULL,
                    alive BOOLEAN NOT NULL DEFAULT TRUE,
                    title VARCHAR(40) NOT NULL,
                    content TEXT NOT NULL
                )";
    }

    function sqlcmd_getComments(int $page) : string {

        $last_item = $page * 10 + 1;
        $first_item = $last_item - 10;

        return "SELECT comment.*, user.user_id, user.avatar, user.nickname FROM comment
                Left JOIN user ON user.user_id = comment.user_id
                WHERE comment.id >= $first_item AND comment.id <= $last_item AND comment.user_id=user.user_id";
    }

    function sqlcmd_getCommentById(int $id) : string {
        return "SELECT user_id FROM comment WHERE comment.id = $id";
    }

    function sqlcmd_deleteComment(int $id) : string {
        return "UPDATE comment SET alive = false 
                WHERE id = $id";
    }

    function sqlcmd_addComment(string $user_id, string $title, string $content) : string {

        $encode_title = stringEncode($title);
        $encode_content = stringEncode($content);

        return "INSERT INTO comment (user_id, title, content) 
                VALUES ('$user_id', '$encode_title', '$encode_content')";
    }
    
?>
