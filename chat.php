<?php include($_SERVER['DOCUMENT_ROOT'] . '/cookie.php');

  check_cookie("index");
  
  session_start();
  $_SESSION['randomNumber'] = mt_rand();
  
  if (!isset($_SESSION['user_id']) && !isset($_COOKIE['JWT'])) {
    header("Location: profile.php");
    exit;
  }
  else if (!isset($_SESSION['user_id']) && isset($_COOKIE['JWT'])) {
    unset($_SESSION['user_id']);
  }
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
    <script src="js/aisu.js"></script>
    <script src="js/check.js"></script>

    <!-- Dynamic Chat Box 
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    Dynamic Chat Box -->

    <!-- For Chat Website -->
    <!--
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
-->
    <script src="js/message.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>

    <script src='//production-assets.codepen.io/assets/editor/live/console_runner-079c09a0e3b9ff743e39ee2d5637b9216b3545af0de366d4b9aad9dc87e26bfd.js'></script>
    <script src='//production-assets.codepen.io/assets/editor/live/events_runner-73716630c22bbc8cff4bd0f07b135f00a0bdc5d14629260c3ec49e5606f98fdd.js'></script>
    <script src='//production-assets.codepen.io/assets/editor/live/css_live_reload_init-2c0dc5167d60a5af3ee189d570b1835129687ea2a61bee3513dee3a50c115a77.js'></script>
    <meta charset='UTF-8'>
    <meta name="robots" content="noindex">
    <link rel="shortcut icon" type="image/x-icon" href="//production-assets.codepen.io/assets/favicon/favicon-8ea04875e70c4b0bb41da869e81236e54394d63638a1ef12fa558a4a835f1164.ico" />
    <link rel="mask-icon" type="" href="//production-assets.codepen.io/assets/favicon/logo-pin-f2d2b6d2c61838f7e76325261b7195c27224080bc099486ddd6dccb469b8e8e6.svg" color="#111" />
    <link rel="canonical" href="https://codepen.io/emilcarlsson/pen/ZOQZaV?limit=all&page=74&q=contact+" />
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,300' rel='stylesheet' type='text/css'>
    <script src='//production-assets.codepen.io/assets/common/stopExecutionOnTimeout-b2a7b3fe212eaa732349046d8416e00a9dec26eb7fd347590fbced3ab38af52e.js'></script>
    <script src="https://use.typekit.net/hoy3lrg.js"></script>
    <script>
        try{
            Typekit.load({ async: true });
        }catch(e){}
    </script>
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css'>
    <link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
    <link rel="stylesheet" href="css/message.css">
    <!-- End -->

    <script>
      r = <?php echo $_SESSION['randomNumber']; ?>;
    </script>

    
  </head>
  
  <body>
    <div class="container-fluid h-100">
     
      <!-- Title bar -->
      <?php include($_SERVER['DOCUMENT_ROOT'] . "/component/bar.php");
        display_title_bar("index");
      ?>
     
     <div id="frame">
	    <div id="sidepanel">
		    <div id="profile">
			    <div class="wrap">
                    <!--個人檔案照片-->
                    <img id="profile-img" src="http://emilcarlsson.se/assets/mikeross.png" class="online" alt="" />
                    <!--名字-->
				    <p>Mike Ross</p>
				    <i class="fa fa-chevron-down expand-button" aria-hidden="true"></i>
				    <div id="status-options">
					    <ul>
						    <li id="status-online" class="active"><span class="status-circle"></span> <p>Online</p></li>
				    		<li id="status-away"><span class="status-circle"></span> <p>Away</p></li>
				    		<li id="status-busy"><span class="status-circle"></span> <p>Busy</p></li>
				    		<li id="status-offline"><span class="status-circle"></span> <p>Offline</p></li>
				    	</ul>
    				</div>
    				<div id="expanded">
    					<label for="twitter"><i class="fa fa-facebook fa-fw" aria-hidden="true"></i></label>
    					<input name="twitter" type="text" value="mikeross" />
    					<label for="twitter"><i class="fa fa-twitter fa-fw" aria-hidden="true"></i></label>
    					<input name="twitter" type="text" value="ross81" />
    					<label for="twitter"><i class="fa fa-instagram fa-fw" aria-hidden="true"></i></label>
    					<input name="twitter" type="text" value="mike.ross" />
    				</div>
    			</div>
    		</div>
    		<div id="search">
    			<label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
    			<input type="text" placeholder="Search contacts..." />
    		</div>
	    	<div id="contacts">
		    	<ul>
		    		<li class="contact">
		    			<div class="wrap">
			    			<span class="contact-status online"></span>
			    			<img src="http://emilcarlsson.se/assets/louislitt.png" alt="" />
			    			<div class="meta">
			    				<p class="name">Louis Litt</p>
			    				<p class="preview">You just got LITT up, Mike.</p>
			    			</div>
			    		</div>
	    			</li>
	    			<li class="contact active">
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
		    		</li>
		    	</ul>
            </div>
            <!--
	    	<div id="bottom-bar">
	    		<button id="addcontact"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> <span>Add contact</span></button>
	    		<button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>
            </div>
            -->
	    </div>
	    <div class="content">
	    	<div class="contact-profile">
	    		<img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
	    		<p>Harvey Specter</p>
	    		<div class="social-media">
	    			<i class="fa fa-facebook" aria-hidden="true"></i>
	    			<i class="fa fa-twitter" aria-hidden="true"></i>
	    			 <i class="fa fa-instagram" aria-hidden="true"></i>
	    		</div>
	    	</div>
	    	<div class="messages">
	    		<ul>
	    			<li class="sent">
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
	    			</li>
		    	</ul>
    		</div>
    		<div class="message-input">
    			<div class="wrap">
    			<input type="text" placeholder="Write your message..." />
    			<i class="fa fa-paperclip attachment" aria-hidden="true"></i>
    			<button class="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
    			</div>
    		</div>
    	</div>
    </div>
    
    </div>
  </body>

</html>