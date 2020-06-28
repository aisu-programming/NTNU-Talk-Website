<?php
  include($_SERVER['DOCUMENT_ROOT'] . '/cookie.php');
  check_cookie("comment");
  $_SESSION['randomNumber'] = mt_rand();
?>

<!DOCTYPE HTML>
<html>

  <head>
    <title>留言板 - 第 <?php echo (int) trim($_GET['page']) ?> 頁</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/mycss.css">
    <style>
      @media only screen and (min-width: 576px) {
        .head-spacer {
          display: none;
        }
      }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="js/request.js"></script>
    <script src="js/comment.js"></script>
    <script>
      r = <?php echo $_SESSION['randomNumber']; ?>;
      getComments(<?php echo (int) trim($_GET['page']) ?>);
    </script>
  </head>

  <body>

    <!-- Title bar -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/component/bar.php");
      display_title_bar("comment");
    ?>

    <nav class="navbar navbar-expand-sm bg-dark fixed-bottom" style="padding: 0px;">
      <div class="row justify-content-center w-100">
        
        <ul class="pagination" style="margin: 8px;">
          <?php
            $this_page = (int) trim($_GET['page']);
            
            $next_page_url = "comment.php?page=" . ($this_page + 1);
            
            if ($this_page - 1 > 0) {
              $previos_page_url = "comment.php?page=" . ($this_page - 1);
          ?>
            <li class="page-item"><a class="page-link" href=<?php echo $previos_page_url ?>>< 上一頁</a></li>
          <?php } else { ?>
            <li class="page-item disabled"><a class="page-link">< 上一頁</a></li>
          <?php } ?>

          <li class="page-item disabled"><a class="page-link"><?php echo $this_page ?></a></li>
          <li class="page-item disabled" id="next-page"><a class="page-link" href=<?php echo $next_page_url ?>>下一頁 ></a></li>
        </ul>
        
        <button type="button" class="btn btn-primary" style="margin: 8px; margin-left: 48px">
          <a href="/postComment.php" style="color: white; text-decoration: none;">我要留言</a>
        </button>
        
      </div>
    </nav>

    <div class="container-fluid" style="padding-top: 65px; padding-bottom: 54px">

      <?php if (isset($_SESSION['user_id'])) { ?>
        <div class="row head-spacer" style="height: 32px;"></div>
      <?php } else { ?>
        <div class="row head-spacer" style="height: 72px;"></div>
      <?php } ?>

      <div class="row align-content-start h-100">
        <div class="col-12 h-100" style="padding: 16px; padding-bottom: 8px;" id="card-group">
            
        </div>
      </div>
    </div>

  </body>
</html>