<?php
  session_start();

  if (!isset($_COOKIE['JWT'])) {
    unset($_SESSION['username']);
    header("Location: login.php");
    exit;
  }

  $_SESSION['randomNumber'] = mt_rand();
?>

<!DOCTYPE HTML>
<html>

  <head>
    <title>發文頁面</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/mycss.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="js/request.js"></script>
    <script src="js/comment.js"></script>
    <script src="js/check.js"></script>
    <script>
      r = <?php echo $_SESSION['randomNumber']; ?>;
      if (getCookie('JWT') != "" && <?php echo isset($_SESSION['username']) * 1 ?> == 0) check('postComment');
    </script>
  </head>

  <body>
    <div class="container-fluid h-100">

      <!-- Title bar -->
      <?php include($_SERVER['DOCUMENT_ROOT'] . "/component/bar.php");?>

      <div class="row justify-content-center align-content-center h-100" style="padding-top: 65px;">
        <div class="col-12 col-md-10" align="center">
          <div class="card" id="login-form">
            <div class="card-header">發個文唄</div>
            <div class="card-body">
              <div class="form-group text-left">
                <label for="title">標題：<br>（限制長度：30 個英文字母 / 10 個中文字）</label>
                <input type="text" class="form-control" maxlength="30" id="title">
              </div>
              <div class="form-group text-left">
                <label for="content">內容：<br>（限制長度：600 個英文字母 / 200 個中文字）</label>
                <textarea type="text" class="form-control" rows="8" maxlength="1200" id="content"></textarea>
              </div>
              <div class="row-fluid">
                <div class="col-fluid text-right">
                  <button type="button" class="btn btn-success" style="width: 100px;" onclick="postComment()">留言</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </body>

</html>