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
        'key' => "|\/|y\/ery|_0ngAnd5+r0ng-Sl2-8i+5ecre+|<ey5+ring",
        'isser' => "NTNU talk",
        'exp' => 7200,      // 2 hours

        // Programming
        // 'referer' =>"http://114.24.94.76:9487/",
        'referer' => "http://localhost/",
        
        // Database
        // 'host' => 'localhost',
        // 'username' => 'root',
        'host' => "140.122.184.132",
        'username' => 'team10',
        'password' => 'DBehZktaWHEvdlY',
        'dbname' => 'team10',
    );

?>