<?php 

  include($_SERVER['DOCUMENT_ROOT'] . '/cookie.php');
  check_cookie("chat");
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


  if ($_GET['user_id'] === $_SESSION['user_id']) unset($_GET['user_id']);
  
  if (isset($_GET['user_id'])) {
    
    // Query for the chat target user's information
    $sql_result = $db->query(sqlcmd_getProfile($_GET['user_id']));
    // Query failed
    if ($sql_result === FALSE) $nickname = FALSE;
    // Target user does not exist
    else if ($sql_result->num_rows === 0) {
      echo '<script language="javascript">alert("This user does not exist.")</script>';
      header("Location: chat.php");
      exit;
    }
    // Bug
    else if ($sql_result->num_rows !== 1) {
      header($_SERVER['SERVER_PROTOCOL'] . " 403");
      echo "This is a bug. Please report.";
      exit;
    }
    else {
      $row = $sql_result->fetch_assoc();
      $target = array();
      $target['avatar'] = $row['avatar'];
      $target['nickname'] = stringDecode($row['nickname']);
    }


    // Query for the chat content with target user
    $sql_result = $db->query(sqlcmd_getMessage($_SESSION['user_id'], $_GET['user_id']));
    // Query failed
    if ($sql_result === FALSE) {
      header($_SERVER['SERVER_PROTOCOL'] . " 501");
      echo "Debugging errno: " . $db->error;
      exit;
    }
    else {
      $content = array();
      while ($row = $sql_result->fetch_assoc()) {
        array_push($content, array('send_by_me'=>$row['send_by_me'],
                                   // 'content'=>$row['content']));
                                   'content'=>stringDecode($row['content'])));
      }
    }
  }


  // Query for my information
  $sql_result = $db->query(sqlcmd_getProfile($_SESSION['user_id']));
  // Query failed
  if ($sql_result === FALSE) {
    header($_SERVER['SERVER_PROTOCOL'] . " 501");
    echo "Debugging errno: " . $db->error;
    exit;
  }
  // Bug
  else if ($sql_result->num_rows !== 1) {
    header($_SERVER['SERVER_PROTOCOL'] . " 403");
    echo "This is a bug. Please report.";
    exit;
  }
  else {
    $row = $sql_result->fetch_assoc();
    $me = array();
    $me['avatar'] = $row['avatar'];
    $me['nickname'] = stringDecode($row['nickname']);
  }


  // Query for the user's all chat room
  $sql_result = $db->query(sqlcmd_getAllChatRoom($_SESSION['user_id']));
  // Query failed
  if ($sql_result === FALSE) {
    header($_SERVER['SERVER_PROTOCOL'] . " 501");
    echo "Debugging errno: " . $db->error;
    exit;
  }
  else {
    $chat_room = array();
    while ($row = $sql_result->fetch_assoc()) {
      array_push($chat_room, array('uid'=>$row['uid'],
                                   'nickname'=>stringDecode($row['nickname']),
                                   'avatar'=>$row['avatar'],
                                   'send_by_me'=>$row['send_by_me'],
                                   // 'preview'=>$row['preview']));
                                   'preview'=>stringDecode($row['preview'])));
    }
  }


  //Close the connection
  $db->close();

?>

<!DOCTYPE HTML>
<html>

  <head>
    <title>聊天室</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/mycss.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="js/request.js"></script>

    <!-- Dynamic Chat Box 
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    Dynamic Chat Box -->

    <!-- For Chat Website -->
    <!-- <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> -->
    <script src="js/chat.js"></script>
    
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    
    
    <script src='//production-assets.codepen.io/assets/editor/live/console_runner-079c09a0e3b9ff743e39ee2d5637b9216b3545af0de366d4b9aad9dc87e26bfd.js'></script>
    <script src='//production-assets.codepen.io/assets/editor/live/events_runner-73716630c22bbc8cff4bd0f07b135f00a0bdc5d14629260c3ec49e5606f98fdd.js'></script>
    <script src='//production-assets.codepen.io/assets/editor/live/css_live_reload_init-2c0dc5167d60a5af3ee189d570b1835129687ea2a61bee3513dee3a50c115a77.js'></script>
    <link rel="shortcut icon" type="image/x-icon" href="//production-assets.codepen.io/assets/favicon/favicon-8ea04875e70c4b0bb41da869e81236e54394d63638a1ef12fa558a4a835f1164.ico" />
    <link rel="mask-icon" type="" href="//production-assets.codepen.io/assets/favicon/logo-pin-f2d2b6d2c61838f7e76325261b7195c27224080bc099486ddd6dccb469b8e8e6.svg" color="#111" />
    <link rel="canonical" href="https://codepen.io/emilcarlsson/pen/ZOQZaV?limit=all&page=74&q=contact+" />
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,300' rel='stylesheet' type='text/css'>
    <script src='//production-assets.codepen.io/assets/common/stopExecutionOnTimeout-b2a7b3fe212eaa732349046d8416e00a9dec26eb7fd347590fbced3ab38af52e.js'></script>
    <script src="https://use.typekit.net/hoy3lrg.js"></script>
    
    
    <script>
        try {
            Typekit.load({async: true});
        } catch(e) {}
    </script>
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css'>
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
    <link rel="stylesheet" href="css/message.css">
    <!-- End -->

    <script>
      r = <?php echo $_SESSION['randomNumber']; ?>;
    </script>
  </head>
  
  <body style="overflow-y: hidden; overflow-x: hidden;">
    <div id="frame" class="container-fluid h-100">
     
      <!-- Title bar -->
      <?php
        include($_SERVER['DOCUMENT_ROOT'] . "/component/bar.php");
        display_title_bar("chat");
      ?>
   
      <div id="sidepanel">
        <div id="profile">
          <div class="wrap">
            <!--個人檔案照片-->
            <img id="profile-img" src="<?php echo htmlentities($me['avatar'], ENT_NOQUOTES); ?>" class="online" alt=""/>
            <!--名字-->
            <p><?php echo htmlentities($me['nickname'], ENT_NOQUOTES); ?></p>
            <!-- <i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>
            <div id="status-options">
              <ul>
                <li id="status-online" class="active"><span class="status-circle"></span> <p>Online</p></li>
                <li id="status-away"><span class="status-circle"></span> <p>Away</p></li>
                <li id="status-busy"><span class="status-circle"></span> <p>Busy</p></li>
                <li id="status-offline"><span class="status-circle"></span> <p>Offline</p></li>
              </ul>
            </div> -->
            <!-- <div id="expanded">
              <label for="twitter"><i class="fa fa-facebook fa-fw" aria-hidden="true"></i></label>
              <input name="twitter" type="text" value="mikeross" />
              <label for="twitter"><i class="fa fa-twitter fa-fw" aria-hidden="true"></i></label>
              <input name="twitter" type="text" value="ross81" />
              <label for="twitter"><i class="fa fa-instagram fa-fw" aria-hidden="true"></i></label>
              <input name="twitter" type="text" value="mike.ross" />
            </div> -->
          </div>
        </div>

        <form id="search">
          <label for="user_id"><i class="fa fa-search" aria-hidden="true"></i></label>
          <input type="text" name="user_id" placeholder="Search contacts..."/>
        </form>

        <div id="contacts">
          <ul>

            <?php
              for($i = 0; $i < count($chat_room); $i++) {
                echo 
            '<li class="contact';
                if ($_GET['user_id'] === $chat_room[$i]['uid']) echo ' active';
                echo 
            '" onclick="location.href=\'/chat.php?user_id=' . $chat_room[$i]['uid'] . '\';">
              <div class="wrap">
                <!-- <span class="contact-status online"></span> -->
                <img src="' . htmlentities($chat_room[$i]['avatar'], ENT_NOQUOTES) . '"/>
                <div class="meta">
                  <p class="nickname">' . htmlentities($chat_room[$i]['nickname'], ENT_NOQUOTES) . '</p>
                  <p class="preview">';
                if ($chat_room[$i]['send_by_me']) echo '你: ';
                echo htmlentities($chat_room[$i]['preview'], ENT_NOQUOTES) . '</p>
                </div>
              </div>
            </li>';
              }
            ?>
            
            <!-- <li class="contact active">
              <div class="wrap">
                <span class="contact-status busy"></span>
                <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
                <div class="meta">
                  <p class="name">Harvey Specter</p>
                  <p class="preview">Wrong. You take the gun, or you pull out a bigger one. Or, you call their bluff. Or, you do any one of a hundred and forty six other things.</p>
                </div>
              </div>
            </li>
            <li class="contact">
              <div class="wrap">
                <span class="contact-status away"></span>
                <img src="http://emilcarlsson.se/assets/rachelzane.png" alt="" />
                <div class="meta">
                  <p class="name">Rachel Zane</p>
                  <p class="preview">I was thinking that we could have chicken tonight, sounds good?</p>
                </div>
              </div>
            </li>
            <li class="contact">
              <div class="wrap">
                <span class="contact-status online"></span>
                <img src="http://emilcarlsson.se/assets/donnapaulsen.png" alt="" />
                <div class="meta">
                  <p class="name">Donna Paulsen</p>
                  <p class="preview">Mike, I know everything! I'm Donna..</p>
                </div>
              </div>
            </li>
            <li class="contact">
              <div class="wrap">
                <span class="contact-status busy"></span>
                <img src="http://emilcarlsson.se/assets/jessicapearson.png" alt="" />
                <div class="meta">
                  <p class="name">Jessica Pearson</p>
                  <p class="preview">Have you finished the draft on the Hinsenburg deal?</p>
                </div>
              </div>
            </li>
            <li class="contact">
              <div class="wrap">
                <span class="contact-status"></span>
                <img src="http://emilcarlsson.se/assets/haroldgunderson.png" alt="" />
                <div class="meta">
                  <p class="name">Harold Gunderson</p>
                  <p class="preview">Thanks Mike! :)</p>
                </div>
              </div>
            </li>
            <li class="contact">
              <div class="wrap">
                <span class="contact-status"></span>
                <img src="http://emilcarlsson.se/assets/danielhardman.png" alt="" />
                <div class="meta">
                  <p class="name">Daniel Hardman</p>
                  <p class="preview">We'll meet again, Mike. Tell Jessica I said 'Hi'.</p>
                </div>
              </div>
            </li>
            <li class="contact">
              <div class="wrap">
                <span class="contact-status busy"></span>
                <img src="http://emilcarlsson.se/assets/katrinabennett.png" alt="" />
                <div class="meta">
                  <p class="name">Katrina Bennett</p>
                  <p class="preview">I've sent you the files for the Garrett trial.</p>
                </div>
              </div>
            </li>
            <li class="contact">
              <div class="wrap">
                <span class="contact-status"></span>
                <img src="http://emilcarlsson.se/assets/charlesforstman.png" alt="" />
                <div class="meta">
                  <p class="name">Charles Forstman</p>
                  <p class="preview">Mike, this isn't over.</p>
                </div>
              </div>
            </li>
            <li class="contact">
              <div class="wrap">
                <span class="contact-status"></span>
                <img src="http://emilcarlsson.se/assets/jonathansidwell.png" alt="" />
                <div class="meta">
                  <p class="name">Jonathan Sidwell</p>
                  <p class="preview"><span>You:</span> That's bullshit. This deal is solid.</p>
                </div>
              </div>
            </li> -->

          </ul>
        </div>
        <!-- <div id="bottom-bar">
          <button id="addcontact"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> <span>Add contact</span></button>
          <button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>
        </div> -->
      </div>
    
      <div class="content">
        <div class="contact-profile">
          <img src="<?php echo htmlentities($target['avatar'], ENT_NOQUOTES); ?>"/>
          <p><?php echo htmlentities($target['nickname'], ENT_NOQUOTES); ?></p>
          <!-- <div class="social-media">
            <i class="fa fa-facebook" aria-hidden="true"></i>
            <i class="fa fa-twitter" aria-hidden="true"></i>
            <i class="fa fa-instagram" aria-hidden="true"></i>
          </div> -->
        </div>
        <div class="messages">
          <ul>

            <?php
              if (isset($_GET['user_id'])) {
                for($i = 0; $i < count($content); $i++) {
                  if ($content[$i]['send_by_me'] == 1) echo 
            '<li class="replies">
              <img src="' . htmlentities($me['avatar'], ENT_NOQUOTES) . '" />
              <p >' . htmlentities($content[$i]['content'], ENT_NOQUOTES) . '</p>
            </li>';
                  else echo
            '<li class="sent">
              <img src="' . htmlentities($target['avatar'], ENT_NOQUOTES) . '"/>
              <p>' . htmlentities($content[$i]['content'], ENT_NOQUOTES) . '</p>
            </li>';
                }
              }
            ?>

            <!-- <li class="sent">
              <img src="http://emilcarlsson.se/assets/mikeross.png" alt="" />
              <p>How the hell am I supposed to get a jury to believe you when I am not even sure that I do?!</p>
            </li>
            <li class="replies">
              <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
              <p>When you're backed against the wall, break the god damn thing down.</p>
            </li>
            <li class="replies">
              <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
              <p>Excuses don't win championships.</p>
            </li>
            <li class="sent">
              <img src="http://emilcarlsson.se/assets/mikeross.png" alt="" />
              <p>Oh yeah, did Michael Jordan tell you that?</p>
            </li>
            <li class="replies">
              <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
              <p>No, I told him that.</p>
            </li>
            <li class="replies">
              <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
              <p>What are your choices when someone puts a gun to your head?</p>
            </li>
            <li class="sent">
              <img src="http://emilcarlsson.se/assets/mikeross.png" alt="" />
              <p>What are you talking about? You do what they say or they shoot you.</p>
            </li>
            <li class="replies">
              <img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
              <p>Wrong. You take the gun, or you pull out a bigger one. Or, you call their bluff. Or, you do any one of a hundred and forty six other things.</p>
            </li> -->

          </ul>
        </div>

        <?php
          if (isset($_GET['user_id'])) echo
        '<div class="message-input">
          <div class="wrap">
            <input id="message" type="text" placeholder="Write your message..." />
            <!-- <i class="fa fa-paperclip attachment" aria-hidden="true"></i> -->
            <button class="submit" onclick="sendMessage(\'' . $_GET['user_id'] . '\')"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
          </div>
        </div>';
        ?>

      </div>
    
	  </div>
  </body>

</html>