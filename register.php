<?php
  include($_SERVER['DOCUMENT_ROOT'] . "/cookie.php");
  check_cookie("register");
  $_SESSION['randomNumber'] = mt_rand();
?>

<!DOCTYPE HTML>
<html>

  <head>
    <title>註冊</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/mycss.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="js/request.js"></script>
    <script src="js/register.js"></script>
    <script src="js/check.js"></script>
    <script>
      r = <?php echo $_SESSION['randomNumber']; ?>;
      <?php // Check and set SESSION if user has JWT ?>
      if (getCookie('JWT') != "") check('register');
    </script>
  </head>
  
  <body>
    <div class="container-fluid h-100">

      <!-- Title bar -->
      <?php include($_SERVER['DOCUMENT_ROOT'] . "/component/bar.php");
        display_title_bar("register");
      ?>

      <div class="row justify-content-center align-content-center h-100" style="padding-top: 65px;">
        <div class="col-12 col-md-10" align="center">
          <div class="card" id="register-form">
            <div class="card-header">使用者註冊</div>
            <div class="card-body">
              <div class="form-group text-left">
                <label for="user_id">設定帳號：<br>（請使用學號註冊）</label>
                <input type="text" class="form-control" maxlength="30" id="user_id">
              </div>
              <div class="form-group text-left">
                <label for="password">設定密碼：</label>
                <input type="password" class="form-control" id="password">
              </div>
              <div class="form-group text-left">
                <label for="pwdcheck">確認密碼：</label>
                <input type="password" class="form-control" id="pwdcheck">
              </div>
              <div class="form-group text-left">
                <label for="nickname">設定暱稱：<br>（中文 10 字以內，英文 30 字以內）</label>
                <input type="text" class="form-control" maxlength="30" id="nickname">
              </div>
              <div class="row-fluid">
                <div class="col-fluid text-right">
                  <a></a>
                  <button type="button" class="btn btn-success" style="width: 100px;" onclick="register()">註冊</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </body>

</html>