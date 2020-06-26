<?php

    function display_title_bar($page)
    {
        echo 
            '<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
                <a class="navbar-brand p-0" href="/">
                    <!-- img src="logo.png" alt="Logo" style="width: 50px;" -->
                    NTNU talk X
                </a>
                <ul class="navbar-nav">';

        if (isset($_SESSION['username']) && isset($_COOKIE['JWT'])) {
            echo    '<li class="nav-item';
            if ($page == "profile") echo ' active';
            echo    '">
                        <a class="nav-link" href="/profile.php">個人頁面</a>
                    </li>';
        }

        
        echo        '<li class="nav-item';
        if ($page == "comment") echo ' active';
        echo        '">
                        <a class="nav-link" href="/comment.php?page=1">留言版</a>
                    </li>';

        // Not having both SESSION and JWT
        if (!isset($_SESSION['username']) || !isset($_COOKIE['JWT']))
        {
            echo    '<li class="nav-item';
            if ($page == "login") echo ' active';
            echo    '">
                        <a class="nav-link" href="/login.php">登入</a>
                    </li>
                    <li class="nav-item';
            if ($page == "register") echo ' active';    
            echo    '">
                        <a class="nav-link" href="/register.php">註冊</a>
                    </li>';
        }

        echo    '</ul>
            </nav>';
    }

?>