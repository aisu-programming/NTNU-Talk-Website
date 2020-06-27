<?php

  include($_SERVER['DOCUMENT_ROOT'] . "/cookie.php");
  check_cookie("profile");
  $_SESSION['randomNumber'] = mt_rand();

  $configs = include($_SERVER['DOCUMENT_ROOT'] . "/api/config/config.php");
  include($_SERVER['DOCUMENT_ROOT'] . "/api/lib/sqlcmd.php");
  $db = mysqli_connect($configs['host'],
                       $configs['username'],
                       $configs['password'],
                       $configs['dbname']);
  
  // Database connect failed
  if (!$db) {
    header($_SERVER['SERVER_PROTOCOL'] . " 501");
    echo "Debugging errno: " . mysqli_connect_errno();
    exit;
  }

  // If an user wants to look his/her profile when he/she is not login, redirect to login page
  if (!isset($_GET['user_id']) && !isset($_SESSION['user_id']))
  {
    header("Location: login.php");
    exit;
  }
  // If URL has no user_id at the end, regard as looking himself/herself
  if (!isset($_GET['user_id']) && isset($_SESSION['user_id'])) $_GET['user_id'] = $_SESSION['user_id'];

  // Query for the user's information
  $sql_result = $db->query(sqlcmd_getProfile($_GET['user_id']));

  if ($sql_result === FALSE) $nickname = FALSE;
  else if ($sql_result->num_rows !== 1) $nickname = FALSE;
  else
  {
    $row = $sql_result->fetch_assoc();
    $nickname = stringDecode($row['nickname']);
    $avatar = $row['avatar'];

    // Query for the relationship between user and the target
    if ($_GET['user_id'] !== $_SESSION['user_id'])
    {
      $sql_result = $db->query(sqlcmd_checkRelation($_SESSION['user_id'], $_GET['user_id']));
      if ($sql_result === FALSE) $followed = FALSE;
      else if ($sql_result->num_rows !== 1) $followed = FALSE;
      else {
        $row = $sql_result->fetch_assoc();
        if ($row['status'] == 3) $followed = TRUE;
        else if ($row['a_id'] === $_SESSION['user_id'] && $row['status'] == 1) $followed = TRUE;
        else if ($row['b_id'] === $_SESSION['user_id'] && $row['status'] == 2) $followed = TRUE;
        else $followed = FALSE;
      }
    }
  }

  //Close the connection
  $db->close();

?>

<!DOCTYPE HTML>
<html>

  <head>

    <?php if ($nickname !== FALSE) { ?>
      <title><?php echo htmlentities($nickname, ENT_NOQUOTES); ?> 的個人頁面</title>';
    <?php } else { ?>
      <title>查無此人</title>
    <?php } ?>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
      @media only screen and (min-width: 576px) {
        .head-spacer {
          display: none;
        }
      }
    </style>
    <link rel="stylesheet" href="css/mycss.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/19bb405b06.js"></script>
    <script src="js/request.js"></script>
    <script src="js/profile.js"></script>
    <script src="js/check.js"></script>
    <script>
      r = <?php echo $_SESSION['randomNumber']; ?>;
      <?php // Check if user has JWT but so SESSION ?>
      // if (getCookie('JWT') != "" && <?php echo isset($_SESSION['user_id']) * 1 ?> == 0) check('profile');
    </script>
  </head>
  
  <body>
    <div class="container-fluid h-100">

      <!-- Title bar -->
      <?php
        include($_SERVER['DOCUMENT_ROOT'] . "/component/bar.php");
        display_title_bar("profile");
      ?>

      <?php if (isset($_SESSION['user_id'])) { ?>
        <div class="row head-spacer" style="height: 32px;"></div>
      <?php } else { ?>
        <div class="row head-spacer" style="height: 72px;"></div>
      <?php } ?>

      <?php if ($nickname !== FALSE) { ?>

        <div class="row justify-content-center align-content-end h-50">
          <img class="img-thumbnail" style="background: #cccccc; width: 250px; height: 250px;" alt="Avatar" id="avatar" src="<?php echo htmlentities($avatar, ENT_NOQUOTES); ?>">
        </div>

        <div class="row justify-content-center align-content-start h-50">

          <?php if ($_GET['user_id'] === $_SESSION['user_id']) { ?>

            <div class="col-12 p-2 pt-4" align="center">
              <h4 class="m-0">點此上傳新頭像</h4>
            </div>
            <div class="col-12 p-2" align="center">
              <input type="file" class="form-control-file border" style="max-width: 300px;" accept="image/*" id="upload-image">
            </div>
            <div class="col-12 p-2" align="center">
              <button onclick="uploadImage()" class="btn btn-primary" id="upload-btn">
                <span class="spinner-border spinner-border-sm" style="display: none;" id="upload-spinner"></span>
                <a id="upload-text">上傳</a>
              </button>
            </div>

          <?php } else { ?>
            <?php if ($followed === FALSE) { ?>

              <!-- 未關注，可關注 -->
              <div class="col-12 p-2" align="center">
                <button onclick="follow('<?php echo $_GET['user_id']; ?>')" class="btn btn-primary" id="follow-btn">
                  <a id="follow-text"><i class="fas fa-user-plus"></i> 關注</a>
                </button>
              </div>

            <?php } else { ?>

              <!-- 已關注，可取消關注 -->

              <div class="col-12 p-2" align="center">
                <button onclick="cancelFollow('<?php echo $_GET['user_id']; ?>')" class="btn btn-primary" id="follow-btn">
                  <a id="follow-text"><i class="fas fa-user-times"></i> 取消關注</a>
                </button>
              </div>

            <?php } ?>
          <?php } ?>
        <?php } ?>
           
      </div>

    </div>
  </body>

</html>