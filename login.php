<?php
  session_start();

  // Ban users who was already login but try to visit this page
  if (isset($_SESSION['user_id']) && isset($_COOKIE['JWT'])) {
    header("Location: profile.php");
    exit;
  }
  else if (isset($_SESSION['user_id']) && !isset($_COOKIE['JWT'])) {
    unset($_SESSION['user_id']);
  }

  $_SESSION['randomNumber'] = mt_rand();
?>

<!DOCTYPE HTML>
<html>

  <head>
    <title>登入</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/mycss.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="js/request.js"></script>
    <script src="js/login.js"></script>
    <script src="js/check.js"></script>
    <script>
      r = <?php echo $_SESSION['randomNumber']; ?>;
      <?php // Check and set SESSION if user has JWT ?>
      if (getCookie('JWT') != "") check('login');
    </script>
  </head>
  
  <body>
    <div class="container-fluid h-100">

      <!-- Title bar -->
      <?php include($_SERVER['DOCUMENT_ROOT'] . "/component/bar.php");
        display_title_bar("login");
      ?>

      <div class="row justify-content-center align-content-center h-100" style="padding-top: 65px;">
        <div class="col-12 col-md-10" align="center">
          <div class="card" id="login-form">
            <div class="card-header">使用者登入</div>
            <div class="card-body">
              <div class="form-group text-left">
                <label for="user_id">學號：</label>
                <input type="text" class="form-control" maxlength="30" id="user_id">
              </div>
              <div class="form-group text-left">
                <label for="password">密碼：</label>
                <input type="password" class="form-control" id="password">
              </div>
              <div class="row-fluid">
                <div class="col-fluid text-right">
                  <button type="button" class="btn btn-success" style="width: 100px;" onclick="login()">登入</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </body>

</html>