<?php

    // Fake 404 website, but can be recognize by 'X-Powered-By' in Response Header
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        header($_SERVER["SERVER_PROTOCOL"] . " 404");
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested URL was not found on this server.</p>
</body></html>';
        exit;
    }

    return array(

        // Debug
        'debug' => TRUE,

        // Cookies (JWT)
        'key' => "MyVeryLongAndStrong-512-BitSecretKeyString",
        'isser' => "aisu.ntnu",
        'exp' => 7200,      // 2 hours

        // Programming
        // 'referer' => "http://172.30.36.160/",
        // 'referer' => "http://localhost/",
        'referer' => "http://ntnu-40747026s-aisu.us-east-1.elasticbeanstalk.com/",
        
        // Database
        // 'host' => 'localhost',
        // 'username' => 'root',
        'host' => "aafmmy0ddnwvx0.caqxcgcpxy7z.us-east-1.rds.amazonaws.com",
        'username' => 'aisu',
        'password' => 'hung170232',
        'dbname' => 'ebdb',
    );

?>